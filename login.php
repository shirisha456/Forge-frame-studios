<?php
/**
 * Forgeframe Studios — Admin login
 * Reads /data/users.txt: lines format username:password
 * If password starts with $2y$ use password_verify(); else plaintext compare (class lab).
 * Default: admin / Admin@123
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';

session_start();
if (!empty($_SESSION['is_admin']) && $_SESSION['user'] === 'admin') {
    header('Location: /admin.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = trim($_POST['userid'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($userid === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } elseif (!file_exists(USERS_FILE) || !is_readable(USERS_FILE)) {
        $error = 'Login is not available.';
    } else {
        $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $authenticated = false;
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) !== 2) continue;
            $file_user = trim($parts[0]);
            $file_pass = $parts[1];
            if ($file_user !== $userid) continue;
            if (strpos($file_pass, '$2y$') === 0) {
                $authenticated = password_verify($password, $file_pass);
            } else {
                $authenticated = ($password === $file_pass);
            }
            if ($authenticated) {
                $_SESSION['user'] = $file_user;
                $_SESSION['is_admin'] = true;
                header('Location: /admin.php');
                exit;
            }
            break;
        }
        if (!$authenticated) {
            $error = 'Invalid username or password.';
        }
    }
}

$page_title = 'Admin Login';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h1 class="page-title">Admin Login</h1>
                <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" action="/login.php">
                    <div class="mb-3">
                        <label for="userid" class="form-label">Username</label>
                        <input type="text" class="form-control form-control-dark" id="userid" name="userid" autocomplete="username" value="<?php echo htmlspecialchars($_POST['userid'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control form-control-dark" id="password" name="password" autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-cta-fill">Log in</button>
                </form>
                <p class="small text-muted mt-3"><a href="/">← Back to site</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
