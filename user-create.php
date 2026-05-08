<?php
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config.php';

$current_page = 'users';
$page_title = 'Create User';
$meta_description = 'Create a new user record for Forgeframe Studios.';

$form_data = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'home_address' => '',
    'home_phone' => '',
    'cell_phone' => '',
];

$errors = [];
$success_message = '';
$db_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form_data as $key => $value) {
        $form_data[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    foreach ($form_data as $key => $value) {
        if ($value === '') {
            $errors[$key] = 'This field is required.';
        }
    }

    if ($form_data['email'] !== '' && !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    $phone_pattern = '/^[0-9+\-\s()]{7,25}$/';
    if ($form_data['home_phone'] !== '' && !preg_match($phone_pattern, $form_data['home_phone'])) {
        $errors['home_phone'] = 'Use digits, spaces, dashes, parentheses, or plus sign only.';
    }
    if ($form_data['cell_phone'] !== '' && !preg_match($phone_pattern, $form_data['cell_phone'])) {
        $errors['cell_phone'] = 'Use digits, spaces, dashes, parentheses, or plus sign only.';
    }

    if (empty($errors)) {
        $pdo = forgeframe_get_pdo();
        $db_error = forgeframe_get_db_error();

        if ($pdo instanceof PDO) {
            try {
                $insert_sql = 'INSERT INTO users (first_name, last_name, email, home_address, home_phone, cell_phone)
                               VALUES (:first_name, :last_name, :email, :home_address, :home_phone, :cell_phone)';
                $statement = $pdo->prepare($insert_sql);
                $statement->execute([
                    ':first_name' => $form_data['first_name'],
                    ':last_name' => $form_data['last_name'],
                    ':email' => $form_data['email'],
                    ':home_address' => $form_data['home_address'],
                    ':home_phone' => $form_data['home_phone'],
                    ':cell_phone' => $form_data['cell_phone'],
                ]);

                $success_message = 'User created successfully.';

                // Reset form after successful submission.
                foreach ($form_data as $key => $value) {
                    $form_data[$key] = '';
                }
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $errors['email'] = 'That email already exists. Try a different email.';
                } else {
                    $db_error = $exception->getMessage();
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-inner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-dark mb-3">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/users.php">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create User</li>
            </ol>
        </nav>
        <h1 class="page-title">Create User</h1>
        <p class="lead text-muted">Add a new user profile to the Forgeframe Studios contact database.</p>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <?php if ($success_message !== ''): ?>
            <div class="alert alert-success user-alert" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($db_error !== ''): ?>
            <div class="alert alert-danger user-alert" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Database issue: <?php echo htmlspecialchars($db_error); ?>
            </div>
        <?php endif; ?>

        <div class="user-form-card">
            <form method="post" action="/user-create.php" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="first_name">First Name</label>
                        <input class="form-control form-control-dark <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($form_data['first_name']); ?>" required>
                        <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['first_name']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input class="form-control form-control-dark <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($form_data['last_name']); ?>" required>
                        <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['last_name']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control form-control-dark <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="home_address">Home Address</label>
                        <textarea class="form-control form-control-dark <?php echo isset($errors['home_address']) ? 'is-invalid' : ''; ?>" id="home_address" name="home_address" rows="3" required><?php echo htmlspecialchars($form_data['home_address']); ?></textarea>
                        <?php if (isset($errors['home_address'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['home_address']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="home_phone">Home Phone</label>
                        <input class="form-control form-control-dark <?php echo isset($errors['home_phone']) ? 'is-invalid' : ''; ?>" type="text" id="home_phone" name="home_phone" value="<?php echo htmlspecialchars($form_data['home_phone']); ?>" required>
                        <?php if (isset($errors['home_phone'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['home_phone']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cell_phone">Cell Phone</label>
                        <input class="form-control form-control-dark <?php echo isset($errors['cell_phone']) ? 'is-invalid' : ''; ?>" type="text" id="cell_phone" name="cell_phone" value="<?php echo htmlspecialchars($form_data['cell_phone']); ?>" required>
                        <?php if (isset($errors['cell_phone'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['cell_phone']); ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-cta-fill">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Create User
                    </button>
                    <a href="/users.php" class="btn btn-outline-amber">Back to User Section</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
