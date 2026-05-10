<?php
/**
 * Forge Frame Studios — Site configuration
 * Change brand, domain, and paths here for deployment.
 */

// Prevent direct access
if (!defined('FORGEFRAME')) {
    define('FORGEFRAME', true);
}

// Brand & domain (update for production: shirishagujja.me)
define('SITE_NAME', 'Forge Frame Studios');
define('SITE_TAGLINE', 'Media and design studio for cinematic brand films and documentary storytelling');
define('SITE_DOMAIN', 'shirishagujja.me');
define('SITE_URL', 'https://' . SITE_DOMAIN);
define('BASE_PATH', dirname(__DIR__));

// Data paths (writable by web server: chmod 770 or 755 on /data)
define('DATA_PATH', BASE_PATH . '/data');
define('USERS_FILE', DATA_PATH . '/users.txt');
define('CONTACTS_FILE', DATA_PATH . '/contacts.txt');
define('PRODUCTS_FILE', DATA_PATH . '/products.json');
define('NEWS_FILE', DATA_PATH . '/news.json');
define('COMPANY_CONTACTS_FILE', DATA_PATH . '/company-contacts.txt');

// Assets (relative to web root)
define('ASSETS_CSS', '/assets/css/style.css');
define('ASSETS_JS_MAIN', '/assets/js/main.js');
define('ASSETS_JS_COOKIES', '/assets/js/cookies.js');
define('ASSETS_IMAGES', '/assets/images');

/**
 * Picsum Photos image IDs mapped to content (relevant imagery per slug).
 * Format: slug or filename base => id or [id1, id2, id3] for product images.
 */
$picsum_id_map = [
    'hero-bg' => 10,
    'about-story' => 11,
    'studio-reel' => 12,
    'news-reel-2025' => 13,
    'news-color-grading' => 14,
    'news-event-bts' => 15,
    'brand-anthem-film' => [46, 47, 48],
    'documentary-shorts' => [49, 50, 51],
    'commercial-ads' => [16, 17, 18],
    'corporate-videos' => [19, 20, 21],
    'product-videos' => [22, 23, 24],
    'youtube-production' => [25, 26, 27],
    'full-post-production' => [28, 29, 30],
    'color-grading' => [31, 32, 33],
    'motion-graphics' => [34, 35, 36],
    'explainer-animation' => [37, 38, 39],
    'event-videography' => [40, 41, 42],
    'drone-videography' => [43, 44, 45],
];

/**
 * Return URL for an image. Uses local file if it exists; otherwise returns a relevant Picsum photo by ID (mapped per slug).
 */
function get_image_src($filename, $width = 800, $height = 600) {
    global $picsum_id_map;
    if ($filename === '') return get_picsum_url(0, $width, $height);
    $path = BASE_PATH . '/assets/images/' . $filename;
    if (file_exists($path) && is_readable($path)) {
        return ASSETS_IMAGES . '/' . $filename;
    }
    $base = pathinfo($filename, PATHINFO_FILENAME);
    if (preg_match('/^(.+)-(\d)$/', $base, $m)) {
        $slug = $m[1];
        $idx = (int)$m[2] - 1;
        if (isset($picsum_id_map[$slug]) && is_array($picsum_id_map[$slug]) && isset($picsum_id_map[$slug][$idx])) {
            return get_picsum_url($picsum_id_map[$slug][$idx], $width, $height);
        }
    }
    if (isset($picsum_id_map[$base])) {
        $id = $picsum_id_map[$base];
        return get_picsum_url(is_array($id) ? $id[0] : $id, $width, $height);
    }
    return get_picsum_url(1, $width, $height);
}

function get_picsum_url($id, $width = 800, $height = 600) {
    return 'https://picsum.photos/id/' . (int)$id . '/' . (int)$width . '/' . (int)$height;
}

/**
 * Placeholder image URL by seed (for non-product pages). Uses ID when seed matches a key.
 */
function get_placeholder_image_url($seed, $width = 800, $height = 600) {
    global $picsum_id_map;
    $seed = preg_replace('/[^a-z0-9\-_]/i', '-', $seed);
    if (isset($picsum_id_map[$seed])) {
        $id = $picsum_id_map[$seed];
        return get_picsum_url(is_array($id) ? $id[0] : $id, $width, $height);
    }
    return get_picsum_url(2, $width, $height);
}

// Company contacts from data/company-contacts.txt.
// Supports:
// - key/value lines for studio details: "email: value", "phone: value", "address: value"
// - team rows: "Name | Role | Email"
$company_contact_details = [
    'email' => 'hello@' . SITE_DOMAIN,
    'phone' => '+1 (555) 123-4567',
    'address' => '123 Studio Lane, City, State 12345',
];
$company_contacts_people = [];
if (file_exists(COMPANY_CONTACTS_FILE) && is_readable(COMPANY_CONTACTS_FILE)) {
    $lines = file(COMPANY_CONTACTS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, ':') !== false && strpos($line, '|') === false) {
            [$key, $value] = array_map('trim', explode(':', $line, 2));
            $key = strtolower($key);
            if (isset($company_contact_details[$key]) && $value !== '') {
                $company_contact_details[$key] = $value;
            }
            continue;
        }
        $parts = array_map('trim', explode('|', $line, 3));
        if (count($parts) >= 3) {
            $company_contacts_people[] = ['name' => $parts[0], 'role' => $parts[1], 'email' => $parts[2]];
        }
    }
}
if (count($company_contacts_people) < 5) {
    $defaults = [
        ['name' => 'Sarah Chen', 'role' => 'Director & Founder', 'email' => 'sarah.chen@' . SITE_DOMAIN],
        ['name' => 'Marcus Webb', 'role' => 'Head of Production', 'email' => 'marcus.webb@' . SITE_DOMAIN],
        ['name' => 'Elena Rodriguez', 'role' => 'Lead Editor', 'email' => 'elena.rodriguez@' . SITE_DOMAIN],
        ['name' => 'James Okonkwo', 'role' => 'Motion Design Lead', 'email' => 'james.o@' . SITE_DOMAIN],
        ['name' => 'Lisa Park', 'role' => 'Client Relations', 'email' => 'lisa.park@' . SITE_DOMAIN],
    ];
    $company_contacts_people = array_slice(array_replace($defaults, $company_contacts_people), 0, 5);
}
