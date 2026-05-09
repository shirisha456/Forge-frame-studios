<?php
declare(strict_types=1);

/**
 * Marketplace feed — Forgeframe Studios (company_id 3).
 * Default: top 5 by server counts. ?catalog=all returns every offering in products.json.
 */

define('FORGEFRAME', true);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$COMPANY_ID = 3;

$catalogAll = isset($_GET['catalog']) && (string) $_GET['catalog'] === 'all';
$maxItems = $catalogAll ? 100 : 5;

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/server_visit_counts.php';

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
$scheme = $https ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/api/marketplace-top-products.php');
$siteRootPath = dirname(dirname($script));
$origin = ($siteRootPath === '/' || $siteRootPath === '\\' || $siteRootPath === '.')
    ? $scheme . '://' . $host
    : $scheme . '://' . $host . rtrim($siteRootPath, '/');

$json = file_get_contents(PRODUCTS_FILE);
$list = json_decode((string) $json, true) ?: [];
$bySlug = [];
foreach ($list as $p) {
    $s = (string) ($p['slug'] ?? '');
    if ($s !== '') {
        $bySlug[$s] = $p;
    }
}

$counts = forge_aggregate_load_counts();
$out = [];

$append = static function (string $slug, array $p, string $origin, array $counts): array {
    $img0 = $p['images'][0]['file'] ?? '';
    $rawImg = get_image_src($img0);
    if (preg_match('#^https?://#i', $rawImg)) {
        $imageUrl = $rawImg;
    } else {
        $imageUrl = $origin . '/' . ltrim($rawImg, '/');
    }
    return [
        'name' => (string) ($p['title'] ?? $slug),
        'description' => (string) ($p['short_desc'] ?? ''),
        'category' => 'Video production',
        'image_url' => $imageUrl,
        'visit_count' => (int) ($counts[$slug] ?? 0),
        'external_url' => $origin . '/product.php?' . http_build_query(['slug' => $slug]),
    ];
};

if ($catalogAll) {
    foreach ($list as $p) {
        $slug = (string) ($p['slug'] ?? '');
        if ($slug === '') {
            continue;
        }
        $out[] = $append($slug, $p, $origin, $counts);
        if (count($out) >= $maxItems) {
            break;
        }
    }
} else {
    $order = forge_top_slugs(20);
    foreach ($order as $slug) {
        if (!isset($bySlug[$slug])) {
            continue;
        }
        $out[] = $append($slug, $bySlug[$slug], $origin, $counts);
        if (count($out) >= $maxItems) {
            break;
        }
    }
}

if ($out === []) {
    foreach (array_slice($list, 0, $maxItems) as $p) {
        $slug = (string) ($p['slug'] ?? '');
        if ($slug === '') {
            continue;
        }
        $out[] = $append($slug, $p, $origin, $counts);
    }
}

echo json_encode(
    ['company_id' => $COMPANY_ID, 'products' => $out],
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
);
