<?php
/**
 * Local API endpoint: /api/users.php
 *
 * Returns JSON list of users for Forge Frame Studios (Company A)
 * from the local MySQL/MariaDB database.
 *
 * This endpoint returns only the assignment fields:
 * - full_name
 * - email
 * - role
 * - company_name
 */

define('FORGEFRAME', true);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!$pdo instanceof PDO) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $db_connection_error !== '' ? $db_connection_error : 'Database not configured.',
        'setup_hint' => 'Update includes/db.php credentials and import data/users.sql.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT full_name, email, role, company_name
        FROM users
        ORDER BY full_name ASC
    ');
    $stmt->execute();
    $users = $stmt->fetchAll();

    echo json_encode($users, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Failed to fetch users from local database.',
    ]);
}

