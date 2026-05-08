<?php
/**
 * Quick DB diagnostic page for setup.
 * Remove this file after your database is configured.
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$current_page = 'db-check';
$page_title = 'Database Check';
$meta_description = 'Database setup diagnostics for users API.';
require_once __DIR__ . '/includes/header.php';

$table_exists = false;
$user_count = 0;
$query_error = '';

if ($pdo instanceof PDO) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        $table_exists = (bool) $stmt->fetchColumn();

        if ($table_exists) {
            $count_stmt = $pdo->query('SELECT COUNT(*) FROM users');
            $user_count = (int) $count_stmt->fetchColumn();
        }
    } catch (Throwable $e) {
        $query_error = 'Connected, but failed while checking table/query.';
    }
}
?>

<section class="section py-5">
    <div class="container">
        <h1 class="section-title mb-4">Database Check</h1>

        <?php if (!$pdo instanceof PDO): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($db_connection_error !== '' ? $db_connection_error : 'Database not configured.', ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <p>Fix <code>includes/db.php</code>, then refresh this page.</p>
        <?php else: ?>
            <div class="alert alert-success">Database connection successful.</div>

            <?php if ($query_error !== ''): ?>
                <div class="alert alert-warning"><?php echo htmlspecialchars($query_error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif (!$table_exists): ?>
                <div class="alert alert-warning">
                    Connected to DB, but <code>users</code> table was not found.
                    Import <code>data/users.sql</code> in phpMyAdmin.
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <code>users</code> table found. Current rows: <strong><?php echo htmlspecialchars((string) $user_count, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <p class="mb-0">
                    Test API now:
                    <a href="/api/users.php" target="_blank" rel="noopener">/api/users.php</a>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

