<?php
/**
 * Forge Frame Studios — News listing (3 sample posts)
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
$current_page = 'news';
$page_title = 'News';
$meta_description = 'Latest news and updates from Forge Frame Studios.';
require_once __DIR__ . '/includes/header.php';

$news_json = file_get_contents(NEWS_FILE);
$news = json_decode($news_json, true);
?>

<section class="section page-hero-inner">
    <div class="container">
        <h1 class="page-title">News</h1>
        <p class="lead text-muted">Updates from the studio.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($news as $post): ?>
            <article class="col-md-6 col-lg-4" itemscope itemtype="https://schema.org/Article">
                <div class="card news-card h-100">
                    <div class="card-img-wrap">
                        <img src="<?php echo htmlspecialchars(get_image_src($post['image'])); ?>"
                             alt=""
                             loading="lazy"
                             itemprop="image">
                    </div>
                    <div class="card-body">
                        <time datetime="<?php echo htmlspecialchars($post['date']); ?>" class="small text-muted" itemprop="datePublished"><?php echo date('F j, Y', strtotime($post['date'])); ?></time>
                        <h2 class="h5 card-title mt-2" itemprop="headline"><?php echo htmlspecialchars($post['title']); ?></h2>
                        <p class="card-text"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <button type="button" class="btn btn-link btn-read-more p-0" data-bs-toggle="modal" data-bs-target="#newsModal<?php echo (int)$post['id']; ?>">Read more</button>
                    </div>
                </div>
                <!-- Modal for full article -->
                <div class="modal fade" id="newsModal<?php echo (int)$post['id']; ?>" tabindex="-1" aria-labelledby="newsModalLabel<?php echo (int)$post['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content modal-dark">
                            <div class="modal-header border-0">
                                <h2 class="modal-title h4" id="newsModalLabel<?php echo (int)$post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></h2>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="small text-muted"><?php echo date('F j, Y', strtotime($post['date'])); ?></p>
                                <div class="news-full-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
