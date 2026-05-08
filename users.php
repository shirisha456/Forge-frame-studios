<?php
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config.php';

$current_page = 'users';
$page_title = 'User Section';
$meta_description = 'Manage users in Forgeframe Studios: create new user records and search existing contacts.';

$pdo = forgeframe_get_pdo();
$db_error = forgeframe_get_db_error();
$total_users = 0;

if ($pdo instanceof PDO) {
    try {
        $total_users = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    } catch (Throwable $exception) {
        $db_error = $exception->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-inner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-dark mb-3">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">User</li>
            </ol>
        </nav>
        <h1 class="page-title">User Management</h1>
        <p class="lead text-muted">Create new user records and quickly search your existing client contacts.</p>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <?php if ($db_error !== ''): ?>
            <div class="alert alert-danger user-alert" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Database connection error: <?php echo htmlspecialchars($db_error); ?>.
                Update credentials in <code>config.php</code> and import <code>schema.sql</code>.
            </div>
        <?php endif; ?>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-4">
                <div class="user-stat-card h-100">
                    <p class="text-uppercase small mb-2 text-muted">Current Stats</p>
                    <h2 class="h6 mb-2 text-white">Total users in database</h2>
                    <p class="display-5 mb-0"><?php echo htmlspecialchars((string) $total_users); ?></p>
                </div>
            </div>
            <div class="col-lg-4">
                <a class="user-action-card h-100 d-block text-decoration-none" href="/user-create.php">
                    <div class="user-action-icon"><i class="fa-solid fa-user-plus"></i></div>
                    <h2 class="h4 mb-2 text-white">Create User</h2>
                    <p class="mb-0 text-muted">Add a new contact record with full profile fields and server-side validation.</p>
                </a>
            </div>
            <div class="col-lg-4">
                <a class="user-action-card h-100 d-block text-decoration-none" href="/user-search.php">
                    <div class="user-action-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <h2 class="h4 mb-2 text-white">Search User</h2>
                    <p class="mb-0 text-muted">Find users by name, email, or phone with keyword-based partial matching.</p>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
