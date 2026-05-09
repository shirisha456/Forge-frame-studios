<?php

/**
 * Sends the current page URL to the Cross-Domain marketplace hub when the visitor has an mpvt cookie
 * (set after signing in on the hub and following an outbound link with ?mpvt=…).
 *
 * Configure: export MARKETPLACE_HUB_BASE_URL="http://127.0.0.1:8080/Cross-Domain-Enterprise-Online-Market-Place"
 * (no trailing slash; adjust host/port/path to match your hub).
 */
function marketplace_partner_report_visit_to_hub(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    if (PHP_SAPI === 'cli') {
        return;
    }
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (str_contains((string) $script, '/api/')) {
        return;
    }

    if (!empty($_GET['mpvt']) && is_string($_GET['mpvt'])) {
        $raw = $_GET['mpvt'];
        if ($raw !== '' && strlen($raw) < 6000) {
            $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            if (PHP_VERSION_ID >= 70300) {
                setcookie('mpvt', $raw, [
                    'expires' => time() + 86400 * 30,
                    'path' => '/',
                    'secure' => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            } else {
                setcookie('mpvt', $raw, time() + 86400 * 30, '/', '', $secure, true);
            }
            $_COOKIE['mpvt'] = $raw;
        }
    }

    $tok = isset($_COOKIE['mpvt']) ? trim((string) $_COOKIE['mpvt']) : '';
    if ($tok === '') {
        return;
    }

    $hub = getenv('MARKETPLACE_HUB_BASE_URL');
    if (!is_string($hub) || trim($hub) === '') {
        $hub = 'http://127.0.0.1:8080/Cross-Domain-Enterprise-Online-Market-Place';
    }
    $endpoint = rtrim(trim($hub), '/') . '/api/partner_visit.php';

    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $pageUrl = mb_substr($scheme . '://' . $host . $uri, 0, 2048);

    $post = http_build_query([
        'token' => $tok,
        'page_url' => $pageUrl,
    ]);

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $post,
            'timeout' => 2,
            'ignore_errors' => true,
        ],
    ]);
    @file_get_contents($endpoint, false, $ctx);
}
