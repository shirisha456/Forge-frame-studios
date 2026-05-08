<?php
/**
 * Forgeframe Studios — Secure admin area
 * Lists "current users" (display only), links to contact-list, export contacts CSV.
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';

session_start();
if (empty($_SESSION['user']) || $_SESSION['user'] !== 'admin' || empty($_SESSION['is_admin'])) {
    header('Location: /login.php');
    exit;
}

$admin_users = ['Mary Smith', 'John Wang', 'Alex Bington', 'Priya Rao', 'Omar Khalid'];

// Export contacts as CSV (download)
if (isset($_GET['export']) && $_GET['export'] === 'contacts') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contacts-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Date', 'Name', 'Email', 'Phone', 'Message', 'IP']);
    if (file_exists(CONTACTS_FILE) && is_readable(CONTACTS_FILE)) {
        $handle = fopen(CONTACTS_FILE, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') continue;
                $fields = str_getcsv($line);
                fputcsv($out, $fields);
            }
            fclose($handle);
        }
    }
    fclose($out);
    exit;
}

$page_title = 'Admin';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Admin</h1>
        <p class="text-muted">Logged in as <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>. <a href="/logout.php" class="text-amber">Logout</a></p>

        <h2 class="h4 mt-4">Current users</h2>
        <p class="text-muted small">Team members with access.</p>
        <ul class="list-group list-group-dark mb-4">
            <?php foreach ($admin_users as $u): ?>
            <li class="list-group-item"><?php echo htmlspecialchars($u); ?></li>
            <?php endforeach; ?>
        </ul>

        <h2 class="h4 mt-4">Contacts</h2>
        <p class="text-muted small">View and export contact form submissions.</p>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/contact-list.php" class="btn btn-outline-amber">View contact list</a>
            <a href="/admin.php?export=contacts" class="btn btn-primary btn-cta-fill">Download contacts CSV</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
