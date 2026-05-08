<?php
/**
 * Forge Frame Studios — Database connection (PDO)
 */

if (!defined('FORGEFRAME')) {
    define('FORGEFRAME', true);
}

/* -------------------------
   CONFIGURE DATABASE HERE
-------------------------- */
$db_host = '127.0.0.1';
$db_name = 'forgeframe_studios_site';
$db_user = 'forgeframe_user';
$db_pass = 'Forgeframe@123';
$db_port = '3306';
$db_charset = 'utf8mb4';

/* -------------------------
   PDO CONNECTION
-------------------------- */
$pdo = null;
$db_connection_error = '';

try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset={$db_charset}";

    $pdo = new PDO(
        $dsn,
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

} catch (PDOException $e) {
    $pdo = null;
    $db_connection_error = $e->getMessage();
}