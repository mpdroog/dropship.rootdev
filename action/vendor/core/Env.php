<?php
namespace core;

/**
 * Safe to read environment-variables.
 */
class Env {
    private static $server = null;

    public static function init() {
        if (! isset($_SERVER)) {
            user_error("DevErr: Env-class loaded without SERVER-var");
        }
        self::$server = $_SERVER;
    }

    // Visitor IP
    public static function ip() {
        return self::$server["HTTP_X_REAL_IP"] || self::$server["REMOTE_ADDR"];
    }
    // HTTP-protocol spoken to client.
    public static function protocol() {
         return self::$server["SERVER_PROTOCOL"];
    }
    public static function encoding() {
        $enc = "plain";
        $map = [
            "application/json" => "json",
            "text/html" => "html"
        ];
        $accept = self::$server["HTTP_ACCEPT"] ?? "";

        foreach ($map as $k => $m) {
            if (strpos($accept, $k) !== false) {
                $enc = $m;
            }
        }

        return $enc;
    }
    // Get browser language and match it on $accept
    // If no match is found $accept[0] is chosen.
    public static function getBrowserLang(array $accept = ["en"]) {
        if (! isset(self::$server['HTTP_ACCEPT_LANGUAGE'])) {
            return $accept[0];
        }

        $raw = self::$server['HTTP_ACCEPT_LANGUAGE'];
        $langs = substr($raw, 0, strpos($raw, ";"));
        foreach (explode(",", $langs) as $lang) {
            $lang = strtolower($lang);
            if (strpos($lang, "-") !== false) {
                $lang = substr($lang, 0, strpos($lang, "-"));
            }
            if (in_array($lang, $accept)) {
                return $lang;
            }
        }
        return $accept[0];
    }

    // Get previous page
    public static function prev() {
        // https://domain.nl/...
        $url = self::$server['HTTP_REFERER'];
        return substr($url, strpos($url, "/", 8));
    }
}


