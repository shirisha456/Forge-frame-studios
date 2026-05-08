<?php
define('FORGEFRAME', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config.php';

$current_page = 'users';
$page_title = 'Search Users';
$meta_description = 'Search Forgeframe Studios users by name, email, or phone.';

$query = trim((string) ($_GET['q'] ?? ''));
$results = [];
$db_error = '';
$searched = isset($_GET['q']);

if ($searched) {
    $pdo = forgeframe_get_pdo();
    $db_error = forgeframe_get_db_error();

    if ($pdo instanceof PDO && $query !== '') {
        try {
            $search_sql = 'SELECT id, first_name, last_name, email, home_address, home_phone, cell_phone, created_at
                           FROM users
                           WHERE first_name LIKE :keyword_first
                              OR last_name LIKE :keyword_last
                              OR email LIKE :keyword_email
                              OR home_phone LIKE :keyword_home
                              OR cell_phone LIKE :keyword_cell
                           ORDER BY created_at DESC
                           LIMIT 100';

            $statement = $pdo->prepare($search_sql);
            $keyword = '%' . $query . '%';
            $statement->bindValue(':keyword_first', $keyword, PDO::PARAM_STR);
            $statement->bindValue(':keyword_last', $keyword, PDO::PARAM_STR);
            $statement->bindValue(':keyword_email', $keyword, PDO::PARAM_STR);
            $statement->bindValue(':keyword_home', $keyword, PDO::PARAM_STR);
            $statement->bindValue(':keyword_cell', $keyword, PDO::PARAM_STR);
            $statement->execute();
            $results = $statement->fetchAll();
        } catch (Throwable $exception) {
            $db_error = $exception->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section page-hero-inner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-dark mb-3">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/users.php">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Search User</li>
            </ol>
        </nav>
        <h1 class="page-title">Search Users</h1>
        <p class="lead text-muted">Use one keyword to search across first name, last name, email, home phone, and cell phone.</p>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <?php if ($db_error !== ''): ?>
            <div class="alert alert-danger user-alert" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Database issue: <?php echo htmlspecialchars($db_error); ?>
            </div>
        <?php endif; ?>

        <div class="user-form-card mb-4">
            <form method="get" action="/user-search.php" class="row g-3 align-items-end">
                <div class="col-lg-9">
                    <label class="form-label" for="q">Keyword</label>
                    <input class="form-control form-control-dark" type="text" id="q" name="q" placeholder="Try: Mary, wang@example.com, +1 555, 2099" value="<?php echo htmlspecialchars($query); ?>">
                </div>
                <div class="col-lg-3 d-grid">
                    <button type="submit" class="btn btn-primary btn-cta-fill">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Search
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <a href="/users.php" class="btn btn-outline-amber btn-sm">Back to User Section</a>
            </div>
        </div>

        <?php if ($searched && $query === ''): ?>
            <div class="alert alert-warning user-alert" role="alert">
                Enter a keyword to search users.
            </div>
        <?php elseif ($searched): ?>
            <?php if (empty($results)): ?>
                <div class="alert alert-info user-alert" role="alert">
                    No results found for "<strong><?php echo htmlspecialchars($query); ?></strong>".
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h5 mb-0 text-white">Search Results</h2>
                    <span class="badge bg-amber text-dark"><?php echo htmlspecialchars((string) count($results)); ?> match(es)</span>
                </div>
                <div class="table-responsive user-results-table-wrap">
                    <table class="table table-dark table-hover align-middle user-results-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Home Address</th>
                                <th>Home Phone</th>
                                <th>Cell Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></a></td>
                                    <td><?php echo htmlspecialchars($user['home_address']); ?></td>
                                    <td><?php echo htmlspecialchars($user['home_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($user['cell_phone']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
