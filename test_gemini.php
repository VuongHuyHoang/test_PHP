<?php
/**
 * Script kiểm tra kết nối Google Gemini API Key từ file .env
 * Chạy lệnh: php test_gemini.php
 */

require_once __DIR__ . '/app/bootstrap.php';

echo "====================================================\n";
echo "   KIỂM TRA KẾT NỐI GOOGLE GEMINI API KEY (.ENV)    \n";
echo "====================================================\n\n";

$rawKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?: '';
$apiKey = trim($rawKey);
$model = trim($_ENV['GEMINI_MODEL'] ?? getenv('GEMINI_MODEL') ?: 'gemini-1.5-flash');

// Tự động kiểm tra model
$candidateModels = [$model, 'gemini-2.5-flash', 'gemini-flash-latest', 'gemini-2.5-flash-lite'];
$candidateModels = array_unique(array_filter($candidateModels));

echo "1. Đọc cấu hình từ file .env:\n";
echo "   - Model cấu hình: $model\n";
if (empty($apiKey)) {
    echo "   - GEMINI_API_KEY: [CHƯA CÓ / ĐANG ĐỂ TRỐNG]\n\n";
    echo "❌ KẾT QUẢ: Bạn chưa dán API Key vào file .env!\n";
    echo "👉 Hướng dẫn: Mở file .env, tìm dòng GEMINI_API_KEY= và dán khóa API của bạn vào.\n";
    echo "👉 Lấy key miễn phí tại: https://aistudio.google.com/app/apikey\n\n";
    exit(1);
}

// Ẩn bớt ký tự để bảo mật
$maskedKey = substr($apiKey, 0, 8) . '...' . substr($apiKey, -6);
echo "   - GEMINI_API_KEY: $maskedKey (Độ dài: " . strlen($apiKey) . " ký tự)\n\n";

echo "2. Đang gửi yêu cầu thử nghiệm đến máy chủ Google Gemini...\n";

$body = [
    'contents' => [
        [
            'role'  => 'user',
            'parts' => [
                ['text' => 'Hãy chào ngắn gọn đúng 1 câu: Xin chào Nha Trang Staycation!']
            ]
        ]
    ]
];

$success = false;
$activeModel = $model;

foreach ($candidateModels as $tryModel) {
    echo "   -> Thử gọi model: $tryModel... ";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$tryModel}:generateContent?key=" . urlencode($apiKey);

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

    $startTime = microtime(true);
    $response = curl_exec($ch);
    $duration = round((microtime(true) - $startTime), 2);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($httpCode === 200 && !empty($response)) {
        echo "THÀNH CÔNG! (HTTP 200 - {$duration}s)\n\n";
        $data = json_decode($response, true);
        $replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        echo "====================================================\n";
        echo "🎉 KẾT NỐI THÀNH CÔNG RỰC RỠ! (API KEY HOẠT ĐỘNG TỐT) \n";
        echo "====================================================\n";
        echo "🤖 Phản hồi thử nghiệm từ Gemini AI ($tryModel):\n";
        echo trim($replyText) . "\n\n";
        echo "👉 Model hoạt động tốt nhất: $tryModel\n";
        if ($model !== $tryModel) {
            echo "💡 Gợi ý: Hãy mở .env và đổi GEMINI_MODEL=$tryModel để tối ưu tốc độ và độ tương thích tốt nhất!\n";
        }
        $success = true;
        exit(0);
    } else {
        echo "Thất bại (HTTP $httpCode)\n";
    }
}

echo "\n====================================================\n";
echo "❌ KẾT NỐI THẤT BẠI TRÊN CÁC MODEL! CHI TIẾT LỖI:   \n";
echo "====================================================\n";

if (!empty($curlErr)) {
    echo "Lỗi cURL: $curlErr\n";
    echo "👉 Gợi ý: Kiểm tra kết nối Internet của máy tính hoặc thử lại sau vài giây.\n";
} else {
    $errData = json_decode($response, true);
    $errMsg = $errData['error']['message'] ?? $response;
    $errStatus = $errData['error']['status'] ?? '';

    echo "Mã lỗi: $httpCode ($errStatus)\n";
    echo "Nội dung phản hồi từ Google: $errMsg\n\n";

    if ($httpCode === 400 || str_contains($errMsg, 'API_KEY_INVALID')) {
        echo "👉 NGUYÊN NHÂN: Mã API Key không hợp lệ!\n";
        echo "👉 Lưu ý quan trọng: Khóa API Google Gemini chính thức thường bắt đầu bằng tiền tố 'AIzaSy...'\n";
        echo "👉 Cách lấy key chuẩn: Đăng nhập https://aistudio.google.com/app/apikey và bấm 'Create API key'.\n";
    } elseif ($httpCode === 403) {
        echo "👉 NGUYÊN NHÂN: Key bị khóa quyền hoặc chưa kích hoạt API Generative Language.\n";
    } elseif ($httpCode === 429) {
        echo "👉 NGUYÊN NHÂN: Đã vượt quá số lượng request miễn phí trong phút (Rate limit).\n";
    }
}

echo "\n";
exit(1);
