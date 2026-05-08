<?php
/**
 * Create placeholder JPG files via browser or CLI.
 * Visit this page once (e.g. https://yoursite.com/make_placeholders.php) to generate all images so they render.
 */
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';

$base64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBEQACEQADAPwA/9k=';
$bytes = base64_decode($base64);
$dir = BASE_PATH . '/assets/images';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$slugs = ['commercial-ads', 'corporate-videos', 'product-videos', 'youtube-production', 'full-post-production', 'color-grading', 'motion-graphics', 'explainer-animation', 'event-videography', 'drone-videography'];
$names = ['hero-bg', 'about-story', 'news-reel-2025', 'news-color-grading', 'news-event-bts'];
$created = 0;
foreach ($slugs as $s) {
    for ($i = 1; $i <= 3; $i++) {
        $path = $dir . '/' . $s . '-' . $i . '.jpg';
        if (file_put_contents($path, $bytes) !== false) $created++;
    }
}
foreach ($names as $n) {
    $path = $dir . '/' . $n . '.jpg';
    if (file_put_contents($path, $bytes) !== false) $created++;
}

if (php_sapi_name() === 'cli') {
    echo "Placeholder images created: $created files in assets/images.\n";
    exit(0);
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Placeholders</title></head>
<body>
<p>Placeholder images created: <?php echo (int)$created; ?> files in <code>assets/images</code>.</p>
<p><a href="/">Back to site</a></p>
</body></html>
