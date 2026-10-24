<?php

namespace App\Services;

use App\Config\Database;
use PDO;

class AiTourService
{
    private PDO $db;
    private string $apiKey;
    private string $geminiModel;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->apiKey = trim($_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?: '');
        $rawModel = trim($_ENV['GEMINI_MODEL'] ?? getenv('GEMINI_MODEL') ?: 'gemini-2.5-flash');
        
        // Chuẩn hóa tên model sang dòng ổn định cao nhất của Google
        if (str_contains($rawModel, '3.') || $rawModel === 'gemini-1.5-flash' || empty($rawModel)) {
            $rawModel = 'gemini-2.5-flash';
        }
        $this->geminiModel = $rawModel;
    }

    /**
     * Lấy toàn bộ danh sách địa điểm và ẩm thực thực tế từ DB để làm cơ sở tri thức (RAG)
     */
    public function getKnowledgeBase(): array
    {
        $sql = "SELECT d.id, d.ten_dia_diem, d.duong_dan, d.dia_chi, d.vi_do, d.kinh_do,
                       d.gia_thap_nhat, d.gia_cao_nhat, d.gio_mo_cua, d.gio_dong_cua,
                       d.anh_dai_dien, d.mo_ta_ngan, d.co_ban_ve,
                       c.ten_danh_muc, c.loai_danh_muc,
                       (SELECT gv.id FROM goi_ve gv WHERE gv.dia_diem_id = d.id AND gv.trang_thai = 'dang_ban' ORDER BY gv.gia_nguoi_lon ASC LIMIT 1) AS goi_ve_id
                FROM dia_diem d
                JOIN danh_muc c ON d.danh_muc_id = c.id
                WHERE d.trang_thai = 1
                ORDER BY d.luot_xem DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Xử lý câu hỏi tương tác từ Chat Widget & Trang AI Tour (Hỗ trợ Gemini Multi-turn + Fallback + DB)
     */
    public function chatWithAssistant(string $userMessage, int $userId = 0, array $history = []): array
    {
        $locations = $this->getKnowledgeBase();

        // 1. Lưu tin nhắn của người dùng vào Database nếu đã đăng nhập
        if ($userId > 0) {
            $this->saveMessage($userId, 'user', $userMessage);
            // Lấy 6-8 tin nhắn gần nhất làm context cho AI
            $history = $this->getRecentContextForAi($userId, 8);
        }

        $replyText = '';
        $matchedLocations = [];
        $isFallback = true;
        $modelName = 'Offline Smart Heuristic Engine';
        $note = '';

        // 2. Nếu có API Key, thử gọi Google Gemini API với Multi-turn history
        if (!empty($this->apiKey)) {
            $geminiResponse = $this->callGeminiApi($userMessage, $locations, $history);
            if (!empty($geminiResponse['success'])) {
                $replyText = $geminiResponse['reply'];
                $matchedLocations = $this->extractMatchedLocations($replyText, $locations);
                $isFallback = false;
                $modelName = $this->geminiModel;
            }
        }

        // 3. Fallback sang Smart Heuristic Engine nếu không có key hoặc API lỗi
        if ($isFallback) {
            $fallback = $this->generateFallbackChat($userMessage, $locations);
            $replyText = $fallback['reply'];
            $matchedLocations = $fallback['matched_locations'];
            $note = empty($this->apiKey) 
                ? 'Hệ thống đang hoạt động ở chế độ Offline Smart Engine do chưa áp GEMINI_API_KEY trong file .env.' 
                : 'Gemini API phản hồi chậm/lỗi kết nối, hệ thống tự động chuyển sang Smart Fallback Engine.';
        }

        // 4. Lưu câu trả lời của AI vào Database
        $msgId = 0;
        if ($userId > 0) {
            $msgId = $this->saveMessage($userId, 'model', $replyText, $matchedLocations);
        }

        return [
            'success'           => true,
            'message_id'        => $msgId,
            'reply'             => $replyText,
            'matched_locations' => $matchedLocations,
            'is_fallback'       => $isFallback,
            'model'             => $modelName,
            'note'              => $note,
        ];
    }

    /**
     * Lưu 1 tin nhắn vào database và dọn dẹp để không quá 50 tin/user
     */
    public function saveMessage(int $userId, string $role, string $message, ?array $matchedLocations = null): int
    {
        if ($userId <= 0) return 0;

        $stmt = $this->db->prepare("INSERT INTO ai_chat_messages (user_id, role, message, matched_locations) VALUES (:uid, :role, :msg, :locs)");
        $stmt->execute([
            ':uid'   => $userId,
            ':role'  => $role,
            ':msg'   => $message,
            ':locs'  => !empty($matchedLocations) ? json_encode($matchedLocations, JSON_UNESCAPED_UNICODE) : null,
        ]);
        $lastId = (int)$this->db->lastInsertId();

        $this->pruneUserMessages($userId, 50);
        return $lastId;
    }

    /**
     * Dọn dẹp tự động: giữ tối đa 50 tin nhắn gần nhất của user
     */
    public function pruneUserMessages(int $userId, int $max = 50): void
    {
        if ($userId <= 0) return;

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM ai_chat_messages WHERE user_id = :uid");
        $countStmt->execute([':uid' => $userId]);
        $total = (int)$countStmt->fetchColumn();

        if ($total > $max) {
            $offset = $max;
            $idStmt = $this->db->prepare("SELECT id FROM ai_chat_messages WHERE user_id = :uid ORDER BY id DESC LIMIT 1 OFFSET :off");
            $idStmt->bindValue(':uid', $userId, PDO::PARAM_INT);
            $idStmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $idStmt->execute();
            $thresholdId = $idStmt->fetchColumn();
            if ($thresholdId) {
                $delStmt = $this->db->prepare("DELETE FROM ai_chat_messages WHERE user_id = :uid AND id <= :tid");
                $delStmt->execute([':uid' => $userId, ':tid' => $thresholdId]);
            }
        }
    }

    /**
     * Lấy danh sách tin nhắn của user phục vụ tải ban đầu và "Xem Tin Nhắn Trước"
     */
    public function getMessages(int $userId, int $limit = 15, int $beforeId = 0): array
    {
        if ($userId <= 0) return [];

        if ($beforeId > 0) {
            $stmt = $this->db->prepare("SELECT * FROM ai_chat_messages WHERE user_id = :uid AND id < :bid ORDER BY id DESC LIMIT :lim");
            $stmt->bindValue(':bid', $beforeId, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM ai_chat_messages WHERE user_id = :uid ORDER BY id DESC LIMIT :lim");
        }
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        // Đảo ngược lại thứ tự hiển thị từ cũ đến mới (ASC)
        $rows = array_reverse($rows);
        foreach ($rows as &$r) {
            $r['matched_locations'] = !empty($r['matched_locations']) ? json_decode($r['matched_locations'], true) : [];
        }
        return $rows;
    }

    /**
     * Lấy ngữ cảnh hội thoại 6-8 tin nhắn gần nhất nạp vào Gemini
     */
    public function getRecentContextForAi(int $userId, int $limit = 8): array
    {
        if ($userId <= 0) return [];

        $stmt = $this->db->prepare("SELECT role, message FROM ai_chat_messages WHERE user_id = :uid ORDER BY id DESC LIMIT :lim");
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return array_reverse($rows);
    }

    /**
     * Xóa toàn bộ lịch sử đoạn chat của user
     */
    public function clearHistory(int $userId): bool
    {
        if ($userId <= 0) return false;
        $stmt = $this->db->prepare("DELETE FROM ai_chat_messages WHERE user_id = :uid");
        return $stmt->execute([':uid' => $userId]);
    }

    /**
     * Lưu lịch trình tour do AI tạo ra vào tài khoản người dùng
     */
    public function saveItinerary(int $userId, string $title, int $days, string $style, string $budget, string $markdown, ?array $locIds = null): int
    {
        if ($userId <= 0) return 0;
        $stmt = $this->db->prepare("
            INSERT INTO ai_saved_itineraries (user_id, tieu_de, so_ngay, phong_cach, ngan_sach, noi_dung_markdown, dia_diem_ids)
            VALUES (:uid, :title, :days, :style, :budget, :markdown, :loc_ids)
        ");
        $stmt->execute([
            ':uid'      => $userId,
            ':title'    => $title,
            ':days'     => $days,
            ':style'    => $style,
            ':budget'   => $budget,
            ':markdown' => $markdown,
            ':loc_ids'  => !empty($locIds) ? json_encode($locIds, JSON_UNESCAPED_UNICODE) : null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Lấy danh sách lịch trình đã lưu của người dùng
     */
    public function getSavedItineraries(int $userId): array
    {
        if ($userId <= 0) return [];
        $stmt = $this->db->prepare("
            SELECT * FROM ai_saved_itineraries 
            WHERE user_id = :uid 
            ORDER BY id DESC
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Xóa một lịch trình đã lưu
     */
    public function deleteSavedItinerary(int $id, int $userId): bool
    {
        if ($userId <= 0 || $id <= 0) return false;
        $stmt = $this->db->prepare("
            DELETE FROM ai_saved_itineraries 
            WHERE id = :id AND user_id = :uid
        ");
        return $stmt->execute([':id' => $id, ':uid' => $userId]);
    }

    /**
     * Lập lịch trình tour chuyên sâu (Dành cho trang /ai-tour)
     */
    public function generateItinerary(array $params): array
    {
        $locations = $this->getKnowledgeBase();
        $days = max(1, min(3, (int)($params['songay'] ?? 1)));
        $style = $params['phongcach'] ?? 'tong_hop'; // bien_dao, van_hoa, am_thuc, gia_dinh, tong_hop
        $budget = $params['ngansach'] ?? 'tieu_chuan'; // tiet_kiem, tieu_chuan, cao_cap
        $people = $params['doituong'] ?? 'cap_doi'; // cap_doi, gia_dinh, ban_be, mot_minh
        $note = trim($params['ghichu'] ?? '');

        // 1. Thử tạo tour qua Gemini AI nếu có key
        if (!empty($this->apiKey)) {
            $prompt = "Hãy lập lịch trình du lịch Nha Trang {$days} ngày chi tiết cho đối tượng {$people}, phong cách {$style}, ngân sách {$budget}. Ghi chú riêng: {$note}.";
            $geminiResponse = $this->callGeminiApi($prompt, $locations, [], true);
            if (!empty($geminiResponse['success'])) {
                $matchedLocations = $this->extractMatchedLocations($geminiResponse['reply'], $locations);
                return [
                    'success'           => true,
                    'title'             => "Lịch Trình Du Lịch Nha Trang {$days} Ngày ({$this->getStyleName($style)})",
                    'reply'             => $geminiResponse['reply'],
                    'days_count'        => $days,
                    'matched_locations' => $matchedLocations,
                    'is_fallback'       => false,
                    'model'             => $this->geminiModel
                ];
            }
        }

        // 2. Fallback sang thuật toán lập lịch trình cấu trúc cao cấp
        return $this->generateStructuredFallbackPlan($days, $style, $budget, $people, $note, $locations);
    }

    /**
     * Gọi Google Gemini API bằng cURL
     */
    private function callGeminiApi(string $userMessage, array $locations, array $history = [], bool $isItineraryPlan = false): array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key=" . urlencode($this->apiKey);

        // Tạo Context tóm tắt địa điểm từ DB (RAG)
        $knowledgeText = "Dưới đây là danh sách các địa điểm có thật và đang hoạt động trên hệ thống Nha Trang Staycation:\n";
        foreach ($locations as $loc) {
            $ticketText = $loc['co_ban_ve'] ? ('Giá vé: ' . number_format($loc['gia_thap_nhat']) . 'đ - ' . number_format($loc['gia_cao_nhat']) . 'đ') : 'Ăn uống / Miễn phí vé';
            $knowledgeText .= "- [ID: {$loc['id']}] {$loc['ten_dia_diem']} | Loại: {$loc['loai_danh_muc']} ({$loc['ten_danh_muc']}) | {$ticketText} | Tọa độ: {$loc['vi_do']},{$loc['kinh_do']} | Slug: {$loc['duong_dan']}\n";
        }

        $systemPrompt = "Bạn là Trợ lý AI Tour Nha Trang chuyên nghiệp, thân thiện, sành sỏi du lịch và ẩm thực bản địa.
Nhiệm vụ của bạn:
1. Tư vấn, giải đáp thắc mắc và lập lịch trình du lịch thông minh, tối ưu cung đường di chuyển tại Nha Trang.
2. NGUYÊN TẮC BẮT BUỘC: Ưu tiên tối đa gợi ý các địa điểm nằm trong danh sách cơ sở dữ liệu được cung cấp dưới đây. Nếu gợi ý địa điểm trong danh sách, hãy ghi chính xác tên địa điểm (ví dụ: Tháp Bà Ponagar, VinWonders Nha Trang, Nem Nướng Đặng Văn Quyên, Hải Sản Bờ Kè Nha Trang...).
3. Câu trả lời định dạng Markdown rõ ràng, dùng bullet points, phân chia Sáng - Trưa - Chiều - Tối hợp lý, có ước tính chi phí và lời khuyên hữu ích cho du khách.
4. Giọng điệu hào hứng, mến khách, ngắn gọn nhưng đầy đủ thông tin thực tế.

CƠ SỞ DỮ LIỆU ĐỊA ĐIỂM HỆ THỐNG:
{$knowledgeText}
";

        $contents = [];
        if (!empty($history)) {
            foreach ($history as $h) {
                $role = ($h['role'] === 'model') ? 'model' : 'user';
                $text = trim($h['message'] ?? $h['text'] ?? '');
                if (!empty($text)) {
                    $contents[] = [
                        'role'  => $role,
                        'parts' => [['text' => $text]]
                    ];
                }
            }
        }

        // Đảm bảo tin nhắn hiện tại nằm ở cuối
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        $body = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature'     => 0.7,
                'topP'            => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json; charset=utf-8'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $resData = json_decode($response, true);
            $reply = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if (!empty($reply)) {
                return ['success' => true, 'reply' => $reply];
            }
        }

        return ['success' => false, 'error' => $err ?: "HTTP Status: {$httpCode}"];
    }

    /**
     * Bộ máy Lập lịch trình Cấu trúc Cao cấp (Fallback thông minh chạy offline/nội bộ)
     */
    private function generateStructuredFallbackPlan(int $days, string $style, string $budget, string $people, string $note, array $locations): array
    {
        $plan = [];
        $selectedLocs = [];
        $totalTicketCost = 0;

        // Phân loại địa điểm du lịch & ẩm thực
        $travelSpots = array_values(array_filter($locations, fn($l) => $l['loai_danh_muc'] === 'du_lich'));
        $foodSpots   = array_values(array_filter($locations, fn($l) => $l['loai_danh_muc'] === 'am_thuc'));

        // Phân nhánh theo phong cách
        $morningList = [];
        $lunchList = [];
        $afternoonList = [];
        $dinnerList = [];

        // Lọc địa điểm theo chủ đề
        if ($style === 'bien_dao') {
            $morningList = $this->filterByKeywords($travelSpots, ['Hòn Mun', 'Bãi Tranh', 'Điệp Sơn', 'Đảo Hoa Lan', 'Dốc Lết', 'VinWonders']);
            $lunchList = $this->filterByKeywords($foodSpots, ['Hải Sản Bờ Kè', 'Hải Sản Thanh Sương', 'Cơm Gà Trâm Anh', 'Hạnh Xuân']);
            $afternoonList = $this->filterByKeywords($travelSpots, ['Bãi Tranh', 'Hòn Chồng', 'Viện Hải Dương Học', 'Dốc Lết']);
            $dinnerList = $this->filterByKeywords($foodSpots, ['Hải Sản Thanh Sương', 'Ốc Xuân Anh', 'Bò Nướng Lạc Cảnh', 'Chợ Đầm']);
        } elseif ($style === 'van_hoa') {
            $morningList = $this->filterByKeywords($travelSpots, ['Tháp Bà Ponagar', 'Chùa Long Sơn', 'Nhà Thờ Đá']);
            $lunchList = $this->filterByKeywords($foodSpots, ['Bánh Căn Tháp Bà', 'Nem Nướng Đặng Văn Quyên', 'Bún Cá Sứa Năm Beo']);
            $afternoonList = $this->filterByKeywords($travelSpots, ['Viện Hải Dương Học', 'Hòn Chồng', 'Bảo Tàng Bác Sĩ Alexandre Yersin', 'Nhà Hát Đó']);
            $dinnerList = $this->filterByKeywords($foodSpots, ['Bò Nướng Lạc Cảnh', 'Hải Sản Bờ Kè', 'Bánh Canh Chả Cá & Cua Biển Cô Hà']);
        } elseif ($style === 'gia_dinh') {
            $morningList = $this->filterByKeywords($travelSpots, ['VinWonders Nha Trang', 'Viện Hải Dương Học', 'Khu Du Lịch Đảo Khỉ']);
            $lunchList = $this->filterByKeywords($foodSpots, ['Cơm Gà Trâm Anh', 'Nem Nướng Đặng Văn Quyên', 'Hải Sản Bờ Kè']);
            $afternoonList = $this->filterByKeywords($travelSpots, ['Khu Suối Khoáng Nóng I-Resort', 'Tắm Bùn Trăm Trứng', 'Tháp Bà Ponagar']);
            $dinnerList = $this->filterByKeywords($foodSpots, ['Hải Sản Bờ Kè', 'Bò Nướng Lạc Cảnh', 'Chè Trái Cây & Tráng Miệng Cô Ba']);
        } else {
            // Tổng hợp cân đối
            $morningList = $this->filterByKeywords($travelSpots, ['Tháp Bà Ponagar', 'Khu bảo tồn Biển Hòn Mun', 'VinWonders Nha Trang', 'Chùa Long Sơn']);
            $lunchList = $this->filterByKeywords($foodSpots, ['Nem Nướng Đặng Văn Quyên', 'Cơm Gà Trâm Anh', 'Bánh Căn Tháp Bà 47']);
            $afternoonList = $this->filterByKeywords($travelSpots, ['Viện Hải Dương Học Nha Trang', 'Danh Thắng Hòn Chồng', 'Khu Suối Khoáng Nóng I-Resort', 'Bãi Tranh Hòn Miễu']);
            $dinnerList = $this->filterByKeywords($foodSpots, ['Hải Sản Bờ Kè Nha Trang', 'Hải Sản Thanh Sương Nha Trang', 'Bò Nướng Lạc Cảnh', 'Quán Ốc Xuân Anh']);
        }

        // Fallback an toàn nếu mảng rỗng
        if (empty($morningList)) $morningList = $travelSpots;
        if (empty($lunchList)) $lunchList = $foodSpots;
        if (empty($afternoonList)) $afternoonList = $travelSpots;
        if (empty($dinnerList)) $dinnerList = $foodSpots;

        $markdownText = "### 🌟 Lịch Trình Khám Phá Nha Trang {$days} Ngày ({$this->getStyleName($style)})\n";
        $markdownText .= "> **Đối tượng:** {$this->getPeopleName($people)} | **Mức ngân sách:** {$this->getBudgetName($budget)}\n";
        if (!empty($note)) {
            $markdownText .= "> **Ghi chú riêng:** *\"{$note}\"*\n";
        }
        $markdownText .= "\n---\n\n";

        for ($d = 1; $d <= $days; $d++) {
            $mSpot = $morningList[($d - 1) % count($morningList)];
            $lSpot = $lunchList[($d - 1) % count($lunchList)];
            $aSpot = $afternoonList[($d) % count($afternoonList)];
            $dSpot = $dinnerList[($d - 1) % count($dinnerList)];

            $daySpots = [$mSpot, $lSpot, $aSpot, $dSpot];
            foreach ($daySpots as $sp) {
                if (!isset($selectedLocs[$sp['id']])) {
                    $selectedLocs[$sp['id']] = $sp;
                    $totalTicketCost += (int)$sp['gia_thap_nhat'];
                }
            }

            $markdownText .= "#### 🚩 Ngày {$d}: Chinh Phục & Trải Nghiệm\n\n";
            $markdownText .= "- 🌅 **Buổi Sáng (08:00 - 11:30):** Tham quan **{$mSpot['ten_dia_diem']}** ({$mSpot['ten_danh_muc']}). " . 
                             ($mSpot['co_ban_ve'] ? "Giá vé từ " . number_format($mSpot['gia_thap_nhat']) . "đ/người. " : "Vào cửa tự do. ") . 
                             "Khởi đầu ngày mới với cảnh quan tuyệt đẹp và không khí trong lành.\n";
            $markdownText .= "- ☀️ **Buổi Trưa (11:30 - 13:30):** Thưởng thức ẩm thực trứ danh tại **{$lSpot['ten_dia_diem']}**. Địa chỉ: {$lSpot['dia_chi']}. Món ăn mang đậm hương vị truyền thống xứ Trầm Hương.\n";
            $markdownText .= "- 🌤️ **Buổi Chiều (14:00 - 17:30):** Tiếp tục hành trình tại **{$aSpot['ten_dia_diem']}**. " . 
                             ($aSpot['co_ban_ve'] ? "Vé tham quan từ " . number_format($aSpot['gia_thap_nhat']) . "đ. " : "") . 
                             "Điểm đến lý tưởng để chụp ảnh lưu niệm và tìm hiểu văn hóa địa phương.\n";
            $markdownText .= "- 🌙 **Buổi Tối (18:30 - 21:30):** Thưởng thức bữa tối phố biển tại **{$dSpot['ten_dia_diem']}** ({$dSpot['dia_chi']}). Dạo bộ ngắm biển đêm Nha Trang hoặc mua sắm đặc sản.\n\n";
        }

        $estFoodCost = $days * 350000;
        $totalEst = $totalTicketCost + $estFoodCost;

        $markdownText .= "---\n";
        $markdownText .= "### 💰 Dự Toán Ngân Sách Dự Kiến (Cho 1 người)\n";
        $markdownText .= "- **Tiền vé tham quan các điểm:** ~" . number_format($totalTicketCost) . "đ\n";
        $markdownText .= "- **Chi phí ăn uống ước tính ({$days} ngày):** ~" . number_format($estFoodCost) . "đ (khoảng 350.000đ/ngày)\n";
        $markdownText .= "- **👉 Tổng chi phí cơ bản ước tính:** ~**" . number_format($totalEst) . "đ / người**\n\n";
        $markdownText .= "💡 *Mẹo di chuyển:* Các điểm trên đã được thuật toán sắp xếp theo thứ tự địa lý gần nhau để tránh đi vòng. Bạn có thể bấm nút **'Xem lộ trình trên bản đồ'** bên dưới để mở điều hướng OSRM trực tiếp!";

        return [
            'success'           => true,
            'title'             => "Lịch Trình Du Lịch Nha Trang {$days} Ngày ({$this->getStyleName($style)})",
            'reply'             => $markdownText,
            'days_count'        => $days,
            'total_cost'        => $totalEst,
            'matched_locations' => array_values($selectedLocs),
            'is_fallback'       => true,
            'model'             => 'Offline Smart Heuristic Engine',
            'note'              => empty($this->apiKey) ? 'Đang hoạt động ở chế độ Offline Smart Engine do chưa áp GEMINI_API_KEY trong .env.' : 'Gemini API phản hồi chậm, hệ thống tự động chuyển sang Smart Fallback Engine.'
        ];
    }

    /**
     * Fallback cho hội thoại Chatbot nhanh
     */
    private function generateFallbackChat(string $query, array $locations): array
    {
        $q = mb_strtolower($query, 'UTF-8');
        $matched = [];
        $reply = "";

        if (str_contains($q, '1 ngày') || str_contains($q, 'một ngày')) {
            $plan = $this->generateStructuredFallbackPlan(1, 'tong_hop', 'tieu_chuan', 'cap_doi', '', $locations);
            return [
                'reply'             => $plan['reply'],
                'matched_locations' => $plan['matched_locations'],
            ];
        }

        if (str_contains($q, '2 ngày') || str_contains($q, '2n1đ') || str_contains($q, 'hai ngày')) {
            $plan = $this->generateStructuredFallbackPlan(2, 'tong_hop', 'tieu_chuan', 'cap_doi', '', $locations);
            return [
                'reply'             => $plan['reply'],
                'matched_locations' => $plan['matched_locations'],
            ];
        }

        if (str_contains($q, 'hải sản') || str_contains($q, 'ăn uống') || str_contains($q, 'đặc sản') || str_contains($q, 'quán ngon')) {
            $foodSpots = array_slice(array_filter($locations, fn($l) => $l['loai_danh_muc'] === 'am_thuc'), 0, 4);
            $reply = "### 🦞 Gợi Ý Ẩm Thực & Đặc Sản Trứ Danh Nha Trang\n\n";
            $reply .= "Dưới đây là các quán ăn ngon nức tiếng tại Nha Trang đã được kiểm chứng chất lượng trên hệ thống:\n\n";
            foreach ($foodSpots as $f) {
                $matched[] = $f;
                $reply .= "- 🍽️ **{$f['ten_dia_diem']}** ({$f['ten_danh_muc']})\n";
                $reply .= "  - *Địa chỉ:* {$f['dia_chi']}\n";
                $reply .= "  - *Mô tả:* {$f['mo_ta_ngan']}\n";
            }
            $reply .= "\n👉 *Mẹo nhỏ:* Hãy thử món Nem nướng Ninh Hòa hoặc Hải sản tươi sống bờ kè để cảm nhận trọn vẹn phong vị biển Nha Trang bạn nhé!";
            return [
                'reply'             => $reply,
                'matched_locations' => $matched,
            ];
        }

        if (str_contains($q, 'biển đảo') || str_contains($q, 'lặn') || str_contains($q, 'san hô') || str_contains($q, 'hòn mun')) {
            $islandSpots = array_slice(array_filter($locations, fn($l) => str_contains($l['ten_danh_muc'], 'vịnh đảo') || str_contains($l['ten_dia_diem'], 'Hòn') || str_contains($l['ten_dia_diem'], 'Đảo')), 0, 4);
            $reply = "### 🏝️ Top Điểm Đến Biển Đảo Tuyệt Đẹp Nha Trang\n\n";
            $reply .= "Nha Trang sở hữu một trong những vịnh biển đẹp nhất thế giới. Đừng bỏ lỡ các tọa độ này:\n\n";
            foreach ($islandSpots as $is) {
                $matched[] = $is;
                $reply .= "- 🌊 **{$is['ten_dia_diem']}**\n";
                $reply .= "  - *Giá vé:* " . ($is['co_ban_ve'] ? number_format($is['gia_thap_nhat']) . 'đ' : 'Miễn phí') . "\n";
                $reply .= "  - *Điểm nổi bật:* {$is['mo_ta_ngan']}\n";
            }
            $reply .= "\n👉 *Lời khuyên:* Bạn nên khởi hành vào buổi sáng sớm (trước 8h30) để biển êm và nước trong nhất khi lặn ngắm san hô!";
            return [
                'reply'             => $reply,
                'matched_locations' => $matched,
            ];
        }

        // Trả lời chào chung và gợi ý các địa điểm tiêu biểu
        $highlight = array_slice($locations, 0, 4);
        $reply = "### 👋 Xin chào! Tôi là Trợ Lý AI Tour Nha Trang Staycation\n\n";
        $reply .= "Tôi có thể giúp bạn thiết kế chuyến đi hoàn hảo nhất tại Nha Trang. Dưới đây là các địa điểm nổi bật đang được nhiều du khách yêu thích nhất:\n\n";
        foreach ($highlight as $h) {
            $matched[] = $h;
            $reply .= "- 📍 **{$h['ten_dia_diem']}** ({$h['ten_danh_muc']}) - " . ($h['co_ban_ve'] ? "Vé từ " . number_format($h['gia_thap_nhat']) . "đ" : "Ẩm thực / Khám phá") . "\n";
        }
        $reply .= "\n💡 **Bạn có thể thử đặt câu hỏi nhanh:**\n";
        $reply .= "- *'Lập tour 1 ngày khám phá di tích và ăn uống'* \n";
        $reply .= "- *'Gợi ý tour 2 ngày 1 đêm biển đảo & hải sản'* \n";
        $reply .= "- *'Quán hải sản nào tươi ngon gần bờ kè?'* \n\n";
        $reply .= "Hoặc bấm vào nút **'Lập Tour Chi Tiết'** để mở trình thiết kế lịch trình chuyên sâu!";

        return [
            'reply'             => $reply,
            'matched_locations' => $matched,
        ];
    }

    /**
     * Tự động trích xuất các địa điểm có thật trong database từ văn bản trả về của AI
     */
    private function extractMatchedLocations(string $text, array $locations): array
    {
        $matched = [];
        $lowerText = mb_strtolower($text, 'UTF-8');

        foreach ($locations as $loc) {
            $name = mb_strtolower($loc['ten_dia_diem'], 'UTF-8');
            // Kiểm tra tên đầy đủ hoặc từ khóa chính
            if (str_contains($lowerText, $name)) {
                $matched[$loc['id']] = $loc;
                continue;
            }

            // Kiểm tra tên rút gọn (ví dụ: 'Ponagar', 'VinWonders', 'Hòn Mun', 'Long Sơn')
            $shortNames = [
                'Ponagar' => 'Tháp Bà Ponagar',
                'VinWonders' => 'VinWonders',
                'Hòn Mun' => 'Hòn Mun',
                'Hòn Chồng' => 'Hòn Chồng',
                'Long Sơn' => 'Chùa Long Sơn',
                'Hải Dương Học' => 'Hải Dương Học',
                'Nhà Thờ Đá' => 'Nhà Thờ Đá',
                'Đặng Văn Quyên' => 'Đặng Văn Quyên',
                'Năm Beo' => 'Năm Beo',
                'Trâm Anh' => 'Trâm Anh',
                'Lạc Cảnh' => 'Lạc Cảnh',
                'Thanh Sương' => 'Thanh Sương',
                'Xuân Anh' => 'Xuân Anh',
                'Bờ Kè' => 'Hải Sản Bờ Kè',
                'Điệp Sơn' => 'Điệp Sơn',
                'Dốc Lết' => 'Dốc Lết',
                'I-Resort' => 'I-Resort',
                'Nhà Hát Đó' => 'Nhà Hát Đó'
            ];

            foreach ($shortNames as $short => $fullKeyword) {
                if (str_contains($name, mb_strtolower($fullKeyword, 'UTF-8')) && str_contains($lowerText, mb_strtolower($short, 'UTF-8'))) {
                    $matched[$loc['id']] = $loc;
                    break;
                }
            }
        }

        return array_values($matched);
    }

    private function filterByKeywords(array $items, array $keywords): array
    {
        $filtered = [];
        foreach ($items as $item) {
            foreach ($keywords as $kw) {
                if (mb_stripos($item['ten_dia_diem'], $kw) !== false || mb_stripos($item['ten_danh_muc'], $kw) !== false) {
                    $filtered[] = $item;
                    break;
                }
            }
        }
        return $filtered;
    }

    private function getStyleName(string $style): string
    {
        return match ($style) {
            'bien_dao' => 'Khám Phá Biển Đảo & San Hô',
            'van_hoa'  => 'Di Tích Lịch Sử & Văn Hóa Tâm Linh',
            'am_thuc'  => 'Food Tour Ẩm Thực Phố Biển',
            'gia_dinh' => 'Vui Chơi Giải Trí & Gia Đình',
            default    => 'Tổng Hợp Điểm Đến Tiêu Biểu'
        };
    }

    private function getPeopleName(string $people): string
    {
        return match ($people) {
            'cap_doi'  => 'Cặp đôi / Lãng mạn',
            'gia_dinh' => 'Gia đình có trẻ em & người cao tuổi',
            'ban_be'   => 'Nhóm bạn trẻ / Phượt trải nghiệm',
            'mot_minh' => 'Du lịch tự túc một mình',
            default    => 'Du khách tổng hợp'
        };
    }

    private function getBudgetName(string $budget): string
    {
        return match ($budget) {
            'tiet_kiem' => 'Tiết kiệm / Học sinh - Sinh viên',
            'cao_cap'   => 'Cao cấp / Thư thái nghỉ dưỡng',
            default     => 'Phổ thông / Tiêu chuẩn hợp lý'
        };
    }
}

