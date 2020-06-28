<?php

namespace core;

class CustomPDO {
    private static $_pdo = null;

    private function __construct() {
    }

    public static function getInstance() {
        if(self::$_pdo == null) {
            self::$_pdo = new \PDO('mysql:host=localhost;dbname=lab3', 'lab3', '12345678');
        }
        return self::$_pdo;
    }
}