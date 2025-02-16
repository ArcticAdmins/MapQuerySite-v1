<?php

namespace lib;

class Utils {
    public static function sanitizeInput($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function split_str($str) {
        preg_match_all("/./u", $str, $arr);
        return $arr[0];
    }

    public static function similar_text_cn($str1, $str2) {
        $arr_1 = array_unique(self::split_str($str1));
        $arr_2 = array_unique(self::split_str($str2));
        $matchCount = count($arr_2) - count(array_diff($arr_2, $arr_1));
        $ratio = $matchCount / count($arr_2);

        return $ratio;
    }
}