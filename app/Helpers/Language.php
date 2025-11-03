<?php

namespace App\Helpers;

class Language
{
    private static $currentLanguage = 'en_US';
    private static $translations = [];
    private static $availableLanguages = [];

    /**
     * Initialize language system
     */
    public static function init()
    {
        // Load available languages
        self::loadAvailableLanguages();
        
        // Set language from session or browser
        $lang = $_SESSION['language'] ?? self::detectLanguage();
        self::setLanguage($lang);
    }

    /**
     * Load available languages
     */
    private static function loadAvailableLanguages()
    {
        $languagesDir = ROOT_PATH . '/languages';
        
        if (!is_dir($languagesDir)) {
            mkdir($languagesDir, 0755, true);
        }
        
        $dirs = glob($languagesDir . '/*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $code = basename($dir);
            $infoFile = $dir . '/language.json';
            
            if (file_exists($infoFile)) {
                $info = json_decode(file_get_contents($infoFile), true);
                self::$availableLanguages[$code] = $info;
            }
        }
    }

    /**
     * Detect language from browser
     */
    private static function detectLanguage()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            
            foreach ($browserLangs as $lang) {
                $lang = substr($lang, 0, 5);
                $lang = str_replace('-', '_', $lang);
                
                if (isset(self::$availableLanguages[$lang])) {
                    return $lang;
                }
                
                // Try just the language code
                $shortLang = substr($lang, 0, 2);
                foreach (array_keys(self::$availableLanguages) as $availLang) {
                    if (substr($availLang, 0, 2) === $shortLang) {
                        return $availLang;
                    }
                }
            }
        }
        
        return 'en_US'; // Default
    }

    /**
     * Set current language
     */
    public static function setLanguage($language)
    {
        if (isset(self::$availableLanguages[$language])) {
            self::$currentLanguage = $language;
            $_SESSION['language'] = $language;
            self::loadTranslations();
        }
    }

    /**
     * Get current language
     */
    public static function getCurrentLanguage()
    {
        return self::$currentLanguage;
    }

    /**
     * Get available languages
     */
    public static function getAvailableLanguages()
    {
        return self::$availableLanguages;
    }

    /**
     * Load translations for current language
     */
    private static function loadTranslations()
    {
        $translationFile = ROOT_PATH . '/languages/' . self::$currentLanguage . '/messages.php';
        
        if (file_exists($translationFile)) {
            self::$translations = require $translationFile;
        }
    }

    /**
     * Translate a string
     */
    public static function translate($key, $params = [])
    {
        $translation = self::$translations[$key] ?? $key;
        
        // Replace parameters
        foreach ($params as $paramKey => $value) {
            $translation = str_replace(':' . $paramKey, $value, $translation);
        }
        
        return $translation;
    }

    /**
     * Short helper for translate
     */
    public static function t($key, $params = [])
    {
        return self::translate($key, $params);
    }
}

// Global helper function
if (!function_exists('__')) {
    function __($key, $params = []) {
        return Language::translate($key, $params);
    }
}

if (!function_exists('_t')) {
    function _t($key, $params = []) {
        return Language::translate($key, $params);
    }
}

