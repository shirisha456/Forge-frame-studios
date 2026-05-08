<?php
/**
 * User module database configuration (PDO)
 *
 * Edit these credentials for your hosting environment.
 * This file is intentionally simple and shared by user pages.
 */

$forgeframe_db = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'forgeframe_studios_site',
    'username' => 'forgeframe_user',
    'password' => 'Forgeframe@123',
    'charset' => 'utf8mb4',
];

$forgeframe_pdo = null;
$forgeframe_db_error = '';

/**
 * Return a reusable PDO instance for the user module.
 */
function forgeframe_get_pdo(): ?PDO
{
    global $forgeframe_db, $forgeframe_pdo, $forgeframe_db_error;

    if ($forgeframe_pdo instanceof PDO) {
        return $forgeframe_pdo;
    }

    if ($forgeframe_db_error !== '') {
        return null;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $forgeframe_db['host'],
        $forgeframe_db['port'],
        $forgeframe_db['dbname'],
        $forgeframe_db['charset']
    );

    try {
        $forgeframe_pdo = new PDO(
            $dsn,
            $forgeframe_db['username'],
            $forgeframe_db['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $forgeframe_pdo;
    } catch (PDOException $exception) {
        $forgeframe_db_error = $exception->getMessage();
        return null;
    }
}

/**
 * Get latest database connection error, if any.
 */
function forgeframe_get_db_error(): string
{
    global $forgeframe_db_error;
    if ($forgeframe_db_error === '') {
        forgeframe_get_pdo();
    }
    return $forgeframe_db_error;
}
