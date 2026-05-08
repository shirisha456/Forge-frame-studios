<?php
/**
 * Combined Users Page
 *
 * Shows:
 *  - Local company users (Forge Frame Studios, Company A)
 *  - Remote company users fetched using cURL from two teammate APIs (Company B & C)
 *  - Combined list of all users in a single table
 *
 * Optional debug: add ?debug=1 to URL to see raw JSON responses.
 */

define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config.php';

$current_page = 'combined-users';
$page_title = 'Combined Users';
$meta_description = 'Combined list of users from Forge Frame Studios and teammate companies.';

require_once __DIR__ . '/includes/header.php';

// ------------------------------------------------------------
// Remote company configuration
// ------------------------------------------------------------
// Only Combined Users page consumes these remote endpoints.
// Each value can be a URL string, or ['url' => '...', 'fallback' => path to JSON backup].
// Backup is used when the live URL returns HTML (browser-only security) instead of JSON.
$remote_company_endpoints = [
    'Shruthi Katta' => [
        'url'      => 'http://shruthikatta.me/api/get_users.php?i=1',
        'fallback' => __DIR__ . '/data/remote_shruthikatta_users.json',
    ],
];

// cURL settings
$curl_timeout_seconds = 5;

// Debug toggle (?debug=1)
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

// ------------------------------------------------------------
// Helper: Fetch local users (Company A) from database
// ------------------------------------------------------------
function get_local_users(?PDO $pdo, string &$error = ''): array
{
    if (!$pdo instanceof PDO) {
        return [];
    }

    try {
        // Primary schema for this project: first_name/last_name/email/... in users table
        $stmt = $pdo->prepare(
            "SELECT id,
                    TRIM(CONCAT(first_name, ' ', last_name)) AS full_name,
                    email,
                    '' AS role,
                    'Forge Frame Studios' AS company_name,
                    created_at
             FROM users
             ORDER BY first_name ASC, last_name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        // Backward compatibility for older lab schema that stored full_name/role/company_name directly.
        try {
            $stmt = $pdo->prepare('SELECT id, full_name, email, role, company_name, created_at FROM users ORDER BY full_name ASC');
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Throwable $fallbackException) {
            $error = $fallbackException->getMessage();
            return [];
        }
    }
}

// ------------------------------------------------------------
// Remote endpoint config: URL string or [ url, optional fallback file ]
// ------------------------------------------------------------
function normalize_remote_endpoint($cfg): array
{
    if (is_string($cfg)) {
        return ['url' => $cfg, 'fallback' => null];
    }
    if (!is_array($cfg)) {
        return ['url' => '', 'fallback' => null];
    }
    return [
        'url'      => isset($cfg['url']) ? (string) $cfg['url'] : '',
        'fallback' => isset($cfg['fallback']) && $cfg['fallback'] !== '' ? (string) $cfg['fallback'] : null,
    ];
}

function is_likely_html_response(string $body): bool
{
    $t = ltrim($body);
    if ($t === '') {
        return false;
    }
    return strpos($t, '<') === 0;
}

/**
 * @return array<int, array<string, mixed>>|null null if not valid JSON user list
 */
function decode_json_user_list(string $body): ?array
{
    $trimmed = trim($body);
    if ($trimmed === '') {
        return null;
    }
    if (strncmp($trimmed, "\xEF\xBB\xBF", 3) === 0) {
        $trimmed = substr($trimmed, 3);
    }
    $decoded = json_decode($trimmed, true);
    if (!is_array($decoded)) {
        return null;
    }
    if (isset($decoded[0]) && is_array($decoded[0])) {
        return $decoded;
    }
    if (isset($decoded['users']) && is_array($decoded['users'])) {
        return $decoded['users'];
    }
    if (isset($decoded['data']) && is_array($decoded['data'])) {
        return $decoded['data'];
    }
    return [];
}

function load_remote_fallback_json(?string $path): array
{
    if ($path === null || $path === '' || !is_readable($path)) {
        return [];
    }
    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return [];
    }
    $list = decode_json_user_list($raw);
    return is_array($list) ? $list : [];
}

/**
 * @return array{users: array, error: ?string}
 */
function fetch_remote_users(
    string $url,
    string $company_label,
    int $timeout_seconds,
    bool $debug,
    array &$debug_log,
    ?string $fallback_path
): array {
    $empty = ['users' => [], 'error' => null];

    if (!function_exists('curl_init')) {
        $fb = load_remote_fallback_json($fallback_path);
        if (!empty($fb)) {
            if ($debug) {
                $debug_log[] = [
                    'company'       => $company_label,
                    'source'        => 'fallback_json',
                    'fallback_file' => $fallback_path,
                    'reason'        => 'cURL extension not available',
                ];
            }
            return ['users' => $fb, 'error' => null];
        }
        return array_merge($empty, ['error' => 'cURL extension is not enabled on this server.']);
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeout_seconds,
        CURLOPT_CONNECTTIMEOUT => min(10, $timeout_seconds),
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: en-US,en;q=0.9',
        ],
        CURLOPT_ENCODING       => '',
    ]);

    $response_body = curl_exec($ch);
    $curl_error    = curl_error($ch);
    $http_status   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($debug) {
        $debug_log[] = [
            'company'      => $company_label,
            'url'          => $url,
            'http_status'  => $http_status,
            'curl_error'   => $curl_error,
            'raw_response' => $response_body,
        ];
    }

    if ($response_body === false || $curl_error !== '') {
        $fb = load_remote_fallback_json($fallback_path);
        if (!empty($fb)) {
            if ($debug) {
                $debug_log[] = [
                    'company'       => $company_label,
                    'source'        => 'fallback_json',
                    'fallback_file' => $fallback_path,
                    'reason'        => 'cURL error: ' . ($curl_error !== '' ? $curl_error : 'no response'),
                ];
            }
            return ['users' => $fb, 'error' => null];
        }
        return array_merge($empty, ['error' => 'Network error: ' . ($curl_error !== '' ? $curl_error : 'empty response')]);
    }

    $body = (string) $response_body;

    if (is_likely_html_response($body)) {
        $fb = load_remote_fallback_json($fallback_path);
        if (!empty($fb)) {
            if ($debug) {
                $debug_log[] = [
                    'company'       => $company_label,
                    'source'        => 'fallback_json',
                    'fallback_file' => $fallback_path,
                    'reason'        => 'Live URL returned HTML (e.g. browser security page), not JSON',
                ];
            }
            return ['users' => $fb, 'error' => null];
        }
        return array_merge($empty, [
            'error' => 'Live URL returned HTML, not JSON (common when the host shows a JavaScript security page to bots). Your browser still works because it runs JavaScript. Fix: ask shruthikatta.me to disable that protection for /api/get_users.php, or keep using the JSON backup file.',
        ]);
    }

    $list = decode_json_user_list($body);
    if ($list === null) {
        $fb = load_remote_fallback_json($fallback_path);
        if (!empty($fb)) {
            if ($debug) {
                $debug_log[] = [
                    'company'       => $company_label,
                    'source'        => 'fallback_json',
                    'fallback_file' => $fallback_path,
                    'reason'        => 'Response was not valid JSON',
                ];
            }
            return ['users' => $fb, 'error' => null];
        }
        return array_merge($empty, ['error' => 'Response was not valid JSON from ' . $company_label . '.']);
    }

    return ['users' => $list, 'error' => null];
}

// ------------------------------------------------------------
// Fetch data
// ------------------------------------------------------------
$debug_log = [];

// Local users (Company A)
$local_db_error = function_exists('forgeframe_get_db_error') ? forgeframe_get_db_error() : '';
$local_pdo = function_exists('forgeframe_get_pdo') ? forgeframe_get_pdo() : null;
if (!$local_pdo instanceof PDO && $local_db_error === '') {
    $local_db_error = 'Database not configured.';
}
$local_users = get_local_users($local_pdo, $local_db_error);

// Remote users (other companies via cURL)
$remote_users_by_company = [];
$remote_errors = [];

foreach ($remote_company_endpoints as $label => $cfg) {
    $ep = normalize_remote_endpoint($cfg);
    $result = fetch_remote_users(
        $ep['url'],
        $label,
        $curl_timeout_seconds,
        $debug,
        $debug_log,
        $ep['fallback']
    );
    if (!empty($result['users'])) {
        $remote_users_by_company[$label] = $result['users'];
    } else {
        $remote_users_by_company[$label] = [];
        $remote_errors[$label] = $result['error'] !== null && $result['error'] !== ''
            ? $result['error']
            : ($label . ' data unavailable');
    }
}

// Combine all users (local + remote)
$combined_users = [];

foreach ($local_users as $u) {
    $combined_users[] = array_merge(
        $u,
        ['company_name' => $u['company_name'] ?? 'Forge Frame Studios']
    );
}

foreach ($remote_users_by_company as $company_label => $users) {
    foreach ($users as $u) {
        $full_name = $u['full_name'] ?? $u['name'] ?? '';
        $combined_users[] = [
            'id'           => $u['id']           ?? null,
            'full_name'    => $full_name,
            'email'        => $u['email']        ?? '',
            'role'         => $u['role']         ?? '',
            'company_name' => $u['company_name'] ?? $company_label,
            'created_at'   => $u['created_at']   ?? null,
        ];
    }
}

$local_count   = count($local_users);
$remote_count  = array_sum(array_map('count', $remote_users_by_company));
$combined_count = count($combined_users);
?>

<section class="section py-5">
    <div class="container">
        <h1 class="section-title mb-4">Combined Users</h1>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Local company users</h2>
                        <p class="display-6 mb-0"><?php echo htmlspecialchars((string) $local_count, ENT_QUOTES, 'UTF-8'); ?></p>
                        <small class="text-muted">Forge Frame Studios (Company A)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Remote company users</h2>
                        <p class="display-6 mb-0"><?php echo htmlspecialchars((string) $remote_count, ENT_QUOTES, 'UTF-8'); ?></p>
                        <small class="text-muted">Connected remote companies via cURL</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Combined total</h2>
                        <p class="display-6 mb-0"><?php echo htmlspecialchars((string) $combined_count, ENT_QUOTES, 'UTF-8'); ?></p>
                        <small class="text-muted">All companies</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <h2 class="h4 mb-3">Local company users (Forge Frame Studios)</h2>
                <?php if ($local_db_error !== ''): ?>
                    <div class="alert alert-danger mb-3">
                        <?php echo htmlspecialchars($local_db_error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="alert alert-secondary mb-0">
                        Update DB credentials and import <code>schema.sql</code> plus <code>seed-users.sql</code>.
                    </div>
                <?php elseif (empty($local_users)): ?>
                    <div class="alert alert-warning mb-0">
                        No local users found. Have you imported <code>seed-users.sql</code> into your MySQL database?
                    </div>
                <?php else: ?>
                    <div class="table-responsive mb-3">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Full name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Company</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($local_users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars((string) $user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $user['company_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-12">
                <h2 class="h4 mb-3">Remote company users (via cURL)</h2>

                <?php foreach ($remote_company_endpoints as $label => $cfg): ?>
                    <div class="mb-4">
                        <h3 class="h5 mb-2"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></h3>

                        <?php if (isset($remote_errors[$label])): ?>
                            <div class="alert alert-warning">
                                <?php echo htmlspecialchars($remote_errors[$label], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php elseif (empty($remote_users_by_company[$label])): ?>
                            <div class="alert alert-info">
                                No users returned from this company.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive mb-2">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">Full name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Role</th>
                                            <th scope="col">Company</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($remote_users_by_company[$label] as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars((string) ($user['full_name'] ?? ($user['name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <?php if (!empty($user['email'])): ?>
                                                        <a href="mailto:<?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars((string) ($user['role'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars((string) ($user['company_name'] ?? $label), ENT_QUOTES, 'UTF-8'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <h2 class="h4 mb-3">Combined users (all companies)</h2>

                <?php if (empty($combined_users)): ?>
                    <div class="alert alert-warning">
                        No users available to display.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Full name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Company</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($combined_users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php if (!empty($user['email'])): ?>
                                                <a href="mailto:<?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars((string) $user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $user['company_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($debug && !empty($debug_log)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="h5 mb-2">Debug JSON responses</h2>
                    <p class="text-muted mb-2">This section is visible because <code>?debug=1</code> was added to the URL.</p>
                    <pre class="bg-dark text-light p-3 small rounded" style="max-height: 400px; overflow:auto;"><?php
                        echo htmlspecialchars(json_encode($debug_log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');
                    ?></pre>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

