<?php
/**
 * Website page to display Users API details and local users list.
 *
 * Note:
 * - User records are stored in MySQL/MariaDB.
 * - The SQL file `data/users.sql` is used once to create/seed the table.
 * - This page reads from DB and also shows the public API endpoint.
 */

define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$current_page = 'users-api';
$page_title = 'Users';
$meta_description = 'Public Users API endpoint and current Forge Frame Studios users.';

require_once __DIR__ . '/includes/header.php';

$users = [];
$error_message = '';

if (!$pdo instanceof PDO) {
    $error_message = $db_connection_error !== '' ? $db_connection_error : 'Database not configured.';
} else {
    try {
        $stmt = $pdo->prepare('
            SELECT full_name, email, role, company_name
            FROM users
            ORDER BY full_name ASC
        ');
        $stmt->execute();
        $users = $stmt->fetchAll();
    } catch (Throwable $e) {
        $error_message = 'Unable to load users from database. Check your DB config and imported SQL data.';
    }
}
?>

<section class="section py-5">
    <div class="container">
        <h2 class="h4 mb-3">Current local users</h2>

        <?php if ($error_message !== ''): ?>
            <div class="alert alert-danger mb-3"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="alert alert-secondary">
                <strong>Setup steps:</strong>
                <ol class="mb-0">
                    <li>Update DB credentials in <code>includes/db.php</code>.</li>
                    <li>Import <code>data/users.sql</code> into your selected MySQL database.</li>
                    <li>Refresh this page.</li>
                </ol>
            </div>
        <?php elseif (empty($users)): ?>
            <div class="alert alert-warning">
                No users found. Import <code>data/users.sql</code> into your MySQL/MariaDB database first.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Full name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Company</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars((string) $user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string) $user['company_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

