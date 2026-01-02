<?php
// viewer.php - SQLite Database Viewer with soft header & footer

session_start();
require_once 'upload.php';

if (empty($_SESSION['current_db_file']) || !file_exists($_SESSION['current_db_file'])) {
    header('Location: index.php');
    exit;
}

$dbFile = $_SESSION['current_db_file'];
$originalName = $_SESSION['original_name'] ?? basename($dbFile);

$pdo = null;
$tables = [];
$queryResult = null;
$currentTable = $_GET['table'] ?? '';
$sql = $_POST['sql'] ?? '';
$error = '';
$page = max(1, (int)($_GET['page'] ?? 1));

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($sql) && strtoupper(substr(ltrim($sql), 0, 6)) === 'SELECT') {
        $stmt = $pdo->query($sql);
        $queryResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($currentTable && in_array($currentTable, $tables)) {
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $countStmt = $pdo->query("SELECT COUNT(*) FROM " . $pdo->quote($currentTable));
        $totalRows = $countStmt->fetchColumn();
        $totalPages = ceil($totalRows / $limit);

        $stmt = $pdo->query("SELECT * FROM " . $pdo->quote($currentTable) . " LIMIT $limit OFFSET $offset");
        $queryResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $error = 'Database error: ' . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($originalName); ?> - SQLite Viewer</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Soft UI -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ==================== HEADER ==================== -->
<header class="bg-white shadow-sm border-bottom">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-database-fill text-primary fs-2 me-3"></i>
                <div>
                    <h4 class="mb-0 fw-semibold text-dark">SQLite Viewer</h4>
                    <small class="text-muted">Lightweight • Modern • Secure</small>
                </div>
            </div>

            <div class="text-end">
                <p class="mb-1 fw-medium text-dark"><?php echo htmlspecialchars($originalName); ?></p>
                <a href="?clear=1" class="btn btn-outline-danger btn-sm shadow-sm">
                    <i class="bi bi-upload me-1"></i> Upload New File
                </a>
            </div>
        </div>
    </div>
</header>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="container py-5 flex-grow-1">
    <?php if ($error): ?>
        <div class="alert alert-danger rounded-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Tables Sidebar -->
        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h5 class="px-3 pt-2 text-muted">
                    <i class="bi bi-list-ul me-2"></i>
                    Tables (<?php echo count($tables); ?>)
                </h5>
                <ul class="list-group list-group-flush">
                    <?php foreach ($tables as $table): ?>
                        <li class="list-group-item <?php echo $currentTable === $table ? 'active' : ''; ?>">
                            <a href="?table=<?php echo urlencode($table); ?>" class="text-decoration-none d-block py-2">
                                <i class="bi bi-table me-2"></i>
                                <?php echo htmlspecialchars($table); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($tables)): ?>
                        <li class="list-group-item text-muted">No tables found</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Main Viewer Area -->
        <div class="col-md-9">
            <!-- Data Display -->
            <?php if ($queryResult !== null): ?>
                <div class="card p-4 shadow-sm">
                    <h5 class="mb-4">
                        <i class="bi bi-grid-3x3-gap me-2"></i>
                        <?php echo $currentTable ? 'Table: ' . htmlspecialchars($currentTable) : 'Query Result'; ?>
                        <?php if ($currentTable && isset($totalRows)): ?>
                            <span class="badge bg-primary ms-2"><?php echo number_format($totalRows); ?> rows</span>
                        <?php endif; ?>
                    </h5>

                    <?php if (empty($queryResult)): ?>
                        <p class="text-muted">No rows found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <?php foreach (array_keys($queryResult[0]) as $col): ?>
                                            <th><?php echo htmlspecialchars($col); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($queryResult as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td><?php echo htmlspecialchars($value ?? '<em class="text-muted">NULL</em>'); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($currentTable && isset($totalPages) && $totalPages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?table=<?php echo urlencode($currentTable); ?>&page=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Custom Query -->
            <div class="card p-4 shadow-sm mt-4">
                <h5 class="mb-3">
                    <i class="bi bi-terminal me-2"></i>Run Custom SELECT Query
                </h5>
                <form method="post">
                    <div class="input-group">
                        <textarea name="sql" class="form-control" rows="4" placeholder="SELECT * FROM your_table LIMIT 50"><?php echo htmlspecialchars($sql); ?></textarea>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-play-fill"></i> Execute
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">Only SELECT queries are allowed for safety.</small>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- ==================== FOOTER ==================== -->
<footer class="bg-white border-top mt-auto">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-muted small">
            <div><i class="bi bi-code-slash me-1"></i>SQLite Viewer • <?= date('Y') ?></div>
            <div class="mt-2 mt-md-0">Built with <i class="bi bi-heart-fill text-danger small mx-1"></i> Bootstrap & PHP</div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>