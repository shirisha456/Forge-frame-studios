<?php
/**
 * Forge Frame Studios — Home
 * Hero, pitch, featured services, CTA.
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
$current_page = 'home';
$page_title = SITE_NAME;
$meta_description = SITE_TAGLINE;
require_once __DIR__ . '/includes/header.php';

// Load products for featured section (first 3)
$products_json = file_get_contents(PRODUCTS_FILE);
$all_products = json_decode($products_json, true);
$featured = array_slice($all_products, 0, 3);
?>

<!-- Hero: full-bleed background, gradient overlay, center-left text -->
<section class="hero" aria-label="Hero">
    <div class="hero-bg">
        <img src="<?php echo htmlspecialchars(get_image_src('hero-bg.jpg')); ?>" alt="" role="presentation" loading="eager">
        <div class="hero-overlay"></div>
    </div>
    <div class="container hero-container">
        <div class="hero-content">
            <h1 class="hero-title"><?php echo htmlspecialchars(SITE_TAGLINE); ?></h1>
            <p class="hero-subhead">We are a media and design studio for video production and creative storytelling—film-grade execution from concept to final cut.</p>
            <div class="hero-cta">
                <a href="/products.php" class="btn btn-primary btn-cta-fill">View Services</a>
                <a href="/contact.php" class="btn btn-outline-light btn-cta-outline">Contact Us</a>
            </div>
            <div class="hero-chevron" aria-hidden="true">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </div>
</section>

<!-- Pitch -->
<section class="section pitch-section" aria-labelledby="pitch-heading">
    <div class="container">
        <h2 id="pitch-heading" class="section-title">Why Forge Frame Studios</h2>
        <p class="pitch-lead text-center mx-auto">We combine media, design, and cinematic craft with a clear process. Every project gets a dedicated team, transparent timelines, and deliverables built for your audience—from brand anthem films and documentary shorts to spots and social cuts.</p>
    </div>
</section>

<!-- Featured services (3 cards) -->
<section class="section services-preview" aria-labelledby="featured-heading">
    <div class="container">
        <h2 id="featured-heading" class="section-title">Featured Services</h2>
        <div class="row g-4">
            <?php foreach ($featured as $p): ?>
            <div class="col-md-4">
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
        <div class="text-center mt-4">
            <a href="/products.php" class="btn btn-outline-amber">View All Services</a>
        </div>
    </div>
</section>

<!-- Cinematic feel: attractive placeholder image -->
<section class="section cinematic-teaser" aria-label="Studio reel teaser">
    <div class="container">
        <div class="teaser-box">
            <img src="<?php echo htmlspecialchars(get_placeholder_image_url('studio-reel', 1200, 500)); ?>" alt="Studio reel preview" class="teaser-img" loading="lazy">
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section cta-section" aria-labelledby="cta-heading">
    <div class="container text-center">
        <h2 id="cta-heading" class="section-title text-white">Ready to tell your story?</h2>
        <p class="cta-text">Get a custom quote. We respond within 1–2 business days.</p>
        <a href="/contact.php" class="btn btn-primary btn-lg btn-cta-fill">Get a Quote</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
