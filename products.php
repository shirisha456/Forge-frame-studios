<?php
/**
 * Forgeframe Studios — Services listing
 * Grid of 10 products; Recently Viewed & Top 5 Most Visited are rendered client-side via cookies.js
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
$current_page = 'products';
$page_title = 'Our Services';
$meta_description = 'Explore our video production services: commercial, corporate, product videos, YouTube, post-production, color grading, motion graphics, explainers, events, and drone.';
require_once __DIR__ . '/includes/header.php';

$products_json = file_get_contents(PRODUCTS_FILE);
$products = json_decode($products_json, true);
?>

<section class="section page-hero-inner">
    <div class="container">
        <h1 class="page-title">Our Services</h1>
        <p class="lead text-muted">Full-service video production—from concept to delivery.</p>
    </div>
</section>

<!-- Links to Last 5 visited and Top 5 most visited (separate pages) -->
<section class="section section-widget bg-charcoal-soft" aria-labelledby="visited-links-heading">
    <div class="container">
        <h2 id="visited-links-heading" class="section-title">Your visited services</h2>
        <p class="text-muted mb-3">View your browsing history by category.</p>
        <div class="d-flex flex-wrap gap-3">
            <a href="/visited-products.php?type=recent" class="btn btn-outline-amber">
                <i class="fas fa-clock me-2"></i>Last 5 previously visited products
            </a>
            <a href="/visited-products.php?type=most" class="btn btn-outline-amber">
                <i class="fas fa-chart-line me-2"></i>Top 5 most visited products
            </a>
        </div>
    </div>
</section>

<!-- Services grid -->
<section class="section" aria-labelledby="services-grid-heading">
    <div class="container">
        <h2 id="services-grid-heading" class="section-title">All Services</h2>
        <div class="row g-4">
            <?php foreach ($products as $p): ?>
            <div class="col-md-6 col-lg-4 service-col">
                <a href="/product.php?slug=<?php echo htmlspecialchars($p['slug']); ?>" class="card service-card h-100">
                    <div class="card-img-wrap">
                        <img src="<?php echo htmlspecialchars(get_image_src($p['images'][0]['file'])); ?>"
                             alt="<?php echo htmlspecialchars($p['images'][0]['alt']); ?>"
                             loading="lazy"
                             srcset="<?php echo htmlspecialchars(get_image_src($p['images'][0]['file'])); ?> 1x">
                    </div>
                    <div class="card-body">
                        <span class="badge-img"><img src="<?php echo htmlspecialchars(get_image_src($p['images'][0]['file'])); ?>" alt="" role="presentation" loading="lazy"></span>
                        <h3 class="card-title"><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($p['short_desc']); ?></p>
                        <span class="card-meta"><?php echo htmlspecialchars($p['duration']); ?> · From <?php echo htmlspecialchars($p['starting_price']); ?></span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
