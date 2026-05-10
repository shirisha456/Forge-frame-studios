<?php
/**
 * Forge Frame Studios — Contact form
 * Server-side validation; appends CSV to /data/contacts.txt
 * Format: YYYY-MM-DD HH:MM:SS,Name,Email,Phone,Message (escaped),IP
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';

$errors = [];
$success = false;
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $errors['name'] = 'Name is required.';
    }
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if ($message === '') {
        $errors['message'] = 'Message is required.';
    }

    if (empty($errors)) {
        $subject_prefill = trim($_POST['subject'] ?? '');
        if ($subject_prefill !== '') {
            $message = 'Subject: ' . $subject_prefill . "\n\n" . $message;
        }
        $timestamp = date('Y-m-d H:i:s');
        $message_escaped = '"' . str_replace('"', '""', $message) . '"';
        $name_safe = str_replace([',', "\n", "\r"], [' ', ' ', ' '], $name);
        $phone_safe = str_replace([',', "\n", "\r"], [' ', ' ', ' '], $phone);
        $line = $timestamp . ',' . $name_safe . ',' . $email . ',' . $phone_safe . ',' . $message_escaped . ',' . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n";
        if (file_put_contents(CONTACTS_FILE, $line, LOCK_EX | FILE_APPEND) !== false) {
            $success = true;
        } else {
            $errors['form'] = 'Sorry, we could not save your message. Please try again or email us directly.';
        }
    }
}

// Prefill subject from query string (e.g. contact.php?subject=Commercial+Videos)
$subject_prefill = isset($_GET['subject']) ? trim($_GET['subject']) : '';

$current_page = 'contact';
$page_title = 'Contacts';
$meta_description = 'Get in touch with Forge Frame Studios for a quote or project inquiry.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-inner">
    <div class="container">
        <h1 class="page-title">Contacts</h1>
        <p class="lead text-muted">Tell us about your project. We'll respond within 1–2 business days.</p>
    </div>
</section>

<section class="section bg-charcoal-soft" aria-labelledby="company-contacts-heading">
    <div class="container">
        <h2 id="company-contacts-heading" class="section-title text-center">Company contacts</h2>
        <p class="text-center text-muted mb-2">Get in touch with our team.</p>
        <p class="text-center text-muted mb-4">
            <a href="mailto:<?php echo htmlspecialchars($company_contact_details['email']); ?>" class="text-amber text-decoration-none"><?php echo htmlspecialchars($company_contact_details['email']); ?></a>
            · <?php echo htmlspecialchars($company_contact_details['phone']); ?>
            · <?php echo htmlspecialchars($company_contact_details['address']); ?>
        </p>
        <div class="row g-4 justify-content-center">
            <?php foreach (array_slice($company_contacts_people, 0, 5) as $person): ?>
            <div class="col-md-6 col-lg-4">
                <div class="contact-person-card card h-100">
                    <div class="card-body text-center">
                        <h3 class="h5 contact-person-name"><?php echo htmlspecialchars($person['name']); ?></h3>
                        <p class="small contact-person-role text-amber mb-2"><?php echo htmlspecialchars($person['role']); ?></p>
                        <a href="mailto:<?php echo htmlspecialchars($person['email']); ?>" class="contact-person-email"><?php echo htmlspecialchars($person['email']); ?></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    Thanks — we received your request. We'll respond within 1–2 business days.
                </div>
                <?php else: ?>
                <?php if (!empty($errors['form'])): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($errors['form']); ?></div>
                <?php endif; ?>
                <form method="post" action="/contact.php" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-amber">*</span></label>
                        <input type="text" class="form-control form-control-dark <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required aria-required="true">
                        <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-amber">*</span></label>
                        <input type="email" class="form-control form-control-dark <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required aria-required="true">
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control form-control-dark" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <?php if ($subject_prefill): ?>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control form-control-dark" value="<?php echo htmlspecialchars($subject_prefill); ?>" readonly aria-readonly="true">
                        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject_prefill); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="mb-4">
                        <label for="message" class="form-label">Message <span class="text-amber">*</span></label>
                        <textarea class="form-control form-control-dark <?php echo isset($errors['message']) ? 'is-invalid' : ''; ?>" id="message" name="message" rows="5" required aria-required="true"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        <?php if (isset($errors['message'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['message']); ?></div><?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-cta-fill">Send Message</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
