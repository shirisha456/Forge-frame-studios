<?php
/**
 * Forgeframe Studios — Admin: Contact list
 * Reads /data/contacts.txt with fopen/fgets, parses CSV (str_getcsv), displays in table.
 * Protected: requires admin session.
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';

session_start();
if (empty($_SESSION['user']) || $_SESSION['user'] !== 'admin' || empty($_SESSION['is_admin'])) {
    header('Location: /login.php');
    exit;
}

$rows = [];
if (file_exists(CONTACTS_FILE) && is_readable(CONTACTS_FILE)) {
    $handle = fopen(CONTACTS_FILE, 'r');
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') continue;
            $fields = str_getcsv($line);
            if (count($fields) >= 5) {
                $rows[] = [
                    'date' => $fields[0] ?? '',
                    'name' => $fields[1] ?? '',
                    'email' => $fields[2] ?? '',
                    'phone' => $fields[3] ?? '',
                    'message' => $fields[4] ?? '',
                    'ip' => $fields[5] ?? ''
                ];
            }
        }
        fclose($handle);
    }
}
$rows = array_reverse($rows); // newest first

$page_title = 'Contact List';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Contact submissions</h1>
        <p class="text-muted"><a href="/admin.php" class="text-amber">← Back to Admin</a></p>
        <?php if (empty($rows)): ?>
        <p class="text-muted">No contacts yet.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['date']); ?></td>
                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                        <td><?php echo htmlspecialchars($r['email']); ?></td>
                        <td><?php echo htmlspecialchars($r['phone']); ?></td>
                        <td><?php echo htmlspecialchars($r['message']); ?></td>
                        <td><?php echo htmlspecialchars($r['ip']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
