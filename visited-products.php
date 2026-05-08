<?php
/**
 * Forgeframe Studios — Last 5 visited or Top 5 most visited (separate page)
 * ?type=recent → Last 5 previously visited products
 * ?type=most  → Top 5 most visited products
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';

$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
if ($type !== 'recent' && $type !== 'most') {
    header('Location: /products.php');
    exit;
}

$current_page = 'products';
$page_title = $type === 'recent' ? 'Last 5 previously visited' : 'Top 5 most visited';
$meta_description = $type === 'recent' ? 'Your last 5 previously visited service pages.' : 'Your top 5 most visited service pages.';
require_once __DIR__ . '/includes/header.php';

$products_json = file_get_contents(PRODUCTS_FILE);
$products = json_decode($products_json, true);
$products_meta_json = json_encode(array_map(function ($p) {
    return [
        'slug' => $p['slug'],
        'title' => $p['title'],
        'image' => $p['images'][0]['file'],
        'alt' => $p['images'][0]['alt']
    ];
}, $products));
?>

<section class="section page-hero-inner">
    <div class="container">
        <h1 class="page-title"><?php echo $type === 'recent' ? 'Last 5 previously visited products' : 'Top 5 most visited products'; ?></h1>
        <p class="lead text-muted">
            <?php if ($type === 'recent'): ?>
            Services you viewed most recently (up to 5).
            <?php else: ?>
            Services you've visited the most (top 5 by visit count).
            <?php endif; ?>
        </p>
        <p class="mb-0"><a href="/products.php" class="text-amber">&larr; Back to Services</a></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div id="visited-container" class="recent-widget">
            <!-- Filled by cookies.js -->
        </div>
        <p id="visited-empty" class="text-muted" style="display: none;">
            <?php if ($type === 'recent'): ?>
            You haven't viewed any products yet. <a href="/products.php">Browse services</a> and return here to see your last 5.
            <?php else: ?>
            No visit data yet. <a href="/products.php">Browse services</a> and return here to see your top 5 most visited.
            <?php endif; ?>
        </p>
        <div id="cookies-disabled-message" class="alert alert-info mt-3" style="display: none;" role="alert">
            Cookies are disabled. Enable cookies to see your visited products.
        </div>
    </div>
</section>

<script>
window.PRODUCTS_META = <?php echo $products_meta_json; ?>;
window.ASSETS_IMAGES = '<?php echo addslashes(ASSETS_IMAGES); ?>';
window.VISITED_TYPE = '<?php echo addslashes($type); ?>';
</script>
<?php
$footer_scripts = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof cookiesEnabled === 'function' && !cookiesEnabled()) {
        document.getElementById('cookies-disabled-message').style.display = 'block';
        return;
    }
    var container = document.getElementById('visited-container');
    var emptyMsg = document.getElementById('visited-empty');
    var meta = window.PRODUCTS_META || [];
    if (window.VISITED_TYPE === 'recent') {
        var slugs = getRecentProductSlugs();
        if (!Array.isArray(slugs)) slugs = [];
        if (slugs.length > 0 && typeof renderRecentProducts === 'function') {
            renderRecentProducts(container, slugs, meta);
        } else {
            emptyMsg.style.display = 'block';
        }
    } else {
        if (typeof renderTopVisited === 'function' && renderTopVisited(container)) {
            // has content
        } else {
            emptyMsg.style.display = 'block';
        }
    }
});
</script>
HTML;
require_once __DIR__ . '/includes/footer.php';
?>
