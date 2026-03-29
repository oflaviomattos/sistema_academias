<?php
define('DB_HOST',    'localhost');
define('DB_NAME',    'hgin9424_gestao_academias');
define('DB_USER',    'hgin9424_gestao_academias');
define('DB_PASS',    'EjA&}HYsdHiB');
define('DB_CHARSET', 'utf8mb4');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        // PDO::MYSQL_ATTR_INIT_COMMAND só existe com driver mysql carregado
        if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
        }
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Garante charset mesmo sem MYSQL_ATTR_INIT_COMMAND
            $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            $msg = 'Falha na conexao: ' . $e->getMessage();
            if (defined('display_errors') || ini_get('display_errors')) {
                die('<pre style="color:red">' . htmlspecialchars($msg) . '</pre>');
            }
            die($msg);
        }
    }
    return $pdo;
}
