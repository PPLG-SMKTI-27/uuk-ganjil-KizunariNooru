<?php
/**
 * Lightweight Validator helper
 */
class Validator {
    private static $errors = [];

    public static function required($value) {
        return !(is_null($value) || $value === '' || (is_array($value) && empty($value)));
    }

    public static function email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function dateFormat($value, $format = 'Y-m-d') {
        if (empty($value)) return false;
        $d = DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) === $value;
    }

    public static function nisn($value) {
        // NISN harus 10 digit angka
        return !empty($value) && preg_match('/^\d{10}$/', $value);
    }

    public static function nik($value) {
        // NIK harus 16 digit angka
        return !empty($value) && preg_match('/^\d{16}$/', $value);
    }

    public static function addError($key, $msg) {
        if (!isset(self::$errors[$key])) {
            self::$errors[$key] = [];
        }
        if (!is_array(self::$errors[$key])) {
            self::$errors[$key] = [self::$errors[$key]];
        }
        self::$errors[$key][] = $msg;
    }

    public static function hasError() {
        return !empty(self::$errors);
    }

    public static function errors() {
        return self::$errors;
    }

    public static function clear() {
        self::$errors = [];
    }
}

?>
