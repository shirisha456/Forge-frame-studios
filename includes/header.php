<?php
if (!defined('FORGEFRAME')) {
    require_once __DIR__ . '/config.php';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/marketplace_partner_visit.php';
marketplace_partner_report_visit_to_hub();
$current_page = $current_page ?? 'home';
$page_title = isset($page_title) ? $page_title . ' — ' . SITE_NAME : SITE_NAME;
$meta_description = $meta_description ?? SITE_TAGLINE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars(SITE_URL . ($canonical_path ?? $_SERVER['REQUEST_URI'])); ?>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="<?php echo ASSETS_CSS; ?>?v=2" rel="stylesheet">
    <!-- Schema.org Organization / LocalBusiness -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo htmlspecialchars(SITE_NAME); ?>",
        "url": "<?php echo SITE_URL; ?>",
        "description": "<?php echo htmlspecialchars(SITE_TAGLINE); ?>",
        "sameAs": []
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "<?php echo htmlspecialchars(SITE_NAME); ?>",
        "description": "<?php echo htmlspecialchars(SITE_TAGLINE); ?>",
        "url": "<?php echo SITE_URL; ?>"
    }
    </script>
</head>
<body>
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    <header class="site-header" id="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark" aria-label="Main navigation">
            <div class="container">
                <a class="navbar-brand" href="/"><?php echo htmlspecialchars(SITE_NAME); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>" href="/">Home</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>" href="/about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'products' ? 'active' : ''; ?>" href="/products.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'news' ? 'active' : ''; ?>" href="/news.php">News</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>" href="/contact.php">Contacts</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'users' ? 'active' : ''; ?>" href="/users.php">User</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo $current_page === 'combined-users' ? 'active' : ''; ?>" href="/combined-users.php">Combined Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="/login.php">Admin</a></li>
                    </ul>
                    <div class="d-flex align-items-center gap-2">
                        <a class="btn btn-cta-nav" href="/contact.php?ref=quote">Get a Quote</a>
                        <?php if (!empty($_SESSION['is_admin'])): ?>
                            <a class="btn btn-outline-light btn-sm" href="/admin.php">Dashboard</a>
                            <a class="btn btn-outline-light btn-sm" href="/logout.php">Logout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main id="main-content" class="main-content" role="main">
