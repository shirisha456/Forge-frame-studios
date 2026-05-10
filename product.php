<?php
/**
 * Forge Frame Studios — Single product/service page
 * product.php?slug=<slug> — cookie update runs on load via cookies.js
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if ($slug === '') {
    header('Location: /products.php');
    exit;
}

$products_json = file_get_contents(PRODUCTS_FILE);
$products = json_decode($products_json, true);
$product = null;
foreach ($products as $p) {
    if ($p['slug'] === $slug) {
        $product = $p;
        break;
    }
}

if (!$product) {
    header('Location: /products.php');
    exit;
}

require_once __DIR__ . '/includes/server_visit_counts.php';
forge_aggregate_increment_slug($slug);

$current_page = 'products';
$page_title = $product['title'];
$meta_description = $product['short_desc'];
require_once __DIR__ . '/includes/header.php';

// Embed product meta for cookie widgets (all products for titles/images on products.php)
$products_meta_json = json_encode(array_map(function ($p) {
    return [
        'slug' => $p['slug'],
        'title' => $p['title'],
        'image' => $p['images'][0]['file'],
        'alt' => $p['images'][0]['alt']
    ];
}, $products));
?>

<section class="section product-hero">
    <div class="container">
        <div class="product-hero-image mb-4">
            <img src="<?php echo htmlspecialchars(get_image_src($product['images'][0]['file'])); ?>"
                 alt="<?php echo htmlspecialchars($product['images'][0]['alt']); ?>"
                 loading="eager"
                 class="img-fluid rounded-3"
                 srcset="<?php echo htmlspecialchars(get_image_src($product['images'][0]['file'])); ?> 1x">
        </div>
        <h1 class="page-title"><?php echo htmlspecialchars($product['title']); ?></h1>
        <p class="lead text-muted"><?php echo htmlspecialchars($product['short_desc']); ?></p>
        <p class="product-meta"><span class="badge bg-amber text-dark"><?php echo htmlspecialchars($product['duration']); ?></span> <span class="text-muted">Starting at <?php echo htmlspecialchars($product['starting_price']); ?></span></p>
    </div>
</section>

<!-- 3-image gallery carousel (vanilla JS) -->
<section class="section product-gallery" aria-label="Image gallery">
    <div class="container">
        <h2 class="visually-hidden">Gallery</h2>
        <div class="gallery-carousel" id="product-gallery">
            <button type="button" class="carousel-btn carousel-prev" aria-label="Previous image"><i class="fas fa-chevron-left"></i></button>
            <div class="carousel-track">
                <?php foreach ($product['images'] as $i => $img): ?>
                <div class="carousel-slide<?php echo $i === 0 ? ' active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars(get_image_src($img['file'])); ?>"
                         alt="<?php echo htmlspecialchars($img['alt']); ?>"
                         loading="lazy">
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="carousel-btn carousel-next" aria-label="Next image"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="carousel-dots" id="carousel-dots" aria-label="Gallery navigation"></div>
    </div>
</section>

<section class="section product-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="h4 mb-3">Overview</h2>
                <div class="product-long-desc">
                    <?php echo nl2br(htmlspecialchars($product['long_desc'])); ?>
                </div>
                <h2 class="h4 mt-5 mb-3">Deliverables</h2>
                <ul class="deliverables-list">
                    <?php foreach ($product['deliverables'] as $d): ?>
                    <li><?php echo htmlspecialchars($d); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-4">
                <h2 class="h4 mb-3">Pricing Tiers</h2>
                <div class="pricing-tiers">
                    <?php foreach ($product['tiers'] as $tier): ?>
                    <div class="card tier-card mb-3">
                        <div class="card-body">
                            <h3 class="h5 text-amber"><?php echo htmlspecialchars($tier['name']); ?></h3>
                            <p class="tier-price mb-1"><?php echo htmlspecialchars($tier['price']); ?></p>
                            <p class="small text-muted mb-0"><?php echo htmlspecialchars($tier['desc']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="product-cta mt-4">
                    <a href="/contact.php?subject=<?php echo rawurlencode($product['title']); ?>" class="btn btn-primary btn-cta-fill w-100">Get a Quote for this Service</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
window.PRODUCT_SLUG = <?php echo json_encode($slug); ?>;
window.PRODUCTS_META = <?php echo $products_meta_json; ?>;
</script>
<?php
$footer_scripts = '<script>document.addEventListener("DOMContentLoaded", function() { if (typeof updateRecentProducts === "function") updateRecentProducts(window.PRODUCT_SLUG, window.PRODUCTS_META); if (typeof updateVisitCounts === "function") updateVisitCounts(window.PRODUCT_SLUG); initProductGallery(); });</script>';
require_once __DIR__ . '/includes/footer.php';
?>
