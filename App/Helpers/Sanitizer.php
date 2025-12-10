<?php
/**
 * Simple Sanitizer helper
 */
class Sanitizer {
    public static function email($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public static function string($value) {
        // Use htmlspecialchars instead of deprecated FILTER_SANITIZE_STRING
        return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }

    public static function int($value) {
        return (int)$value;
    }

    public static function password($plain) {
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    public static function verifyPassword($plain, $hash) {
        return password_verify($plain, $hash);
    }
}

?>
