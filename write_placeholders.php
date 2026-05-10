<?php
/**
 * Creates placeholder JPG files for all product and hero/news images.
 * Run once: php write_placeholders.php
 * Minimal 1x1 pixel JPEG (valid file).
 */
$base64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBEQACEQADAPwA/9k=';
$dir = __DIR__ . '/assets/images';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$bytes = base64_decode($base64);
$slugs = [
    'brand-anthem-film', 'documentary-shorts',
    'commercial-ads', 'corporate-videos', 'product-videos', 'youtube-production',
    'full-post-production', 'color-grading', 'motion-graphics', 'explainer-animation',
    'event-videography', 'drone-videography'
];
$names = ['hero-bg', 'about-story', 'news-reel-2025', 'news-color-grading', 'news-event-bts'];
foreach ($slugs as $s) {
    for ($i = 1; $i <= 3; $i++) {
        file_put_contents($dir . '/' . $s . '-' . $i . '.jpg', $bytes);
    }
}
foreach ($names as $n) {
    file_put_contents($dir . '/' . $n . '.jpg', $bytes);
}
echo "Placeholder images created in assets/images.\n";
