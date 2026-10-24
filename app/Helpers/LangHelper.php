<?php

namespace App\Helpers;

class LangHelper
{
    private static array $translations = [];
    private static ?string $currentLang = null;

    /**
     * Lấy ngôn ngữ hiện tại của người dùng (mặc định 'vi')
     */
    public static function getLang(): string
    {
        if (self::$currentLang !== null) {
            return self::$currentLang;
        }

        if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['app_lang'])) {
            self::$currentLang = in_array($_SESSION['app_lang'], ['vi', 'en']) ? $_SESSION['app_lang'] : 'vi';
        } else {
            self::$currentLang = 'vi';
        }

        return self::$currentLang;
    }

    /**
     * Thiết lập ngôn ngữ
     */
    public static function setLang(string $lang): void
    {
        $lang = in_array($lang, ['vi', 'en']) ? $lang : 'vi';
        self::$currentLang = $lang;
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['app_lang'] = $lang;
        }
    }

    /**
     * Lấy chuỗi bản dịch theo key
     */
    public static function trans(string $key, ?string $default = null): string
    {
        $lang = self::getLang();

        if (!isset(self::$translations[$lang])) {
            $langFile = dirname(__DIR__) . '/Lang/' . $lang . '.php';
            if (file_exists($langFile)) {
                self::$translations[$lang] = require $langFile;
            } else {
                self::$translations[$lang] = [];
            }
        }

        return self::$translations[$lang][$key] ?? ($default !== null ? $default : $key);
    }
}

