<?php
/**
 * Session Class
 */
class Session {

    public static function init() {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            if (session_id() == '') {
                session_start();
            }
        } else {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    public static function checkLogin() {
        self::init();
        if (self::get("login") == false) {
            self::destroy();
            header("Location: login.php");
            exit();
        }
    }

    public static function checkLogout() {
        self::init();
        if (self::get("login") == true) {
            header("Location: index.php");
            exit();
        }
    }

    public static function destroy() {
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
?>