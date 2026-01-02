<?php
// index.php - Compact modern upload page

session_start();
require_once 'upload.php';

if (!empty($_SESSION['current_db_file']) && file_exists($_SESSION['current_db_file'])) {
    header('Location: viewer.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SQLite Viewer - Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="bg-white shadow-sm border-bottom">
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-center">
            <i class="bi bi-database-fill text-primary fs-2 me-3"></i>
            <div class="text-center">
                <h4 class="mb-0 fw-semibold text-dark">SQLite Viewer</h4>
                <small class="text-muted">Lightweight • Modern • Secure</small>
            </div>
        </div>
    </div>
</header>

<!-- ==================== COMPACT MAIN CONTENT ==================== -->
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">

            <div class="text-center mb-4">
                <h1 class="h3 fw-bold mb-2">Upload SQLite Database</h1>
                <p class="text-muted small">Explore tables and run queries instantly</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4 shadow-sm mb-3"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success rounded-4 shadow-sm mb-3"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Compact Modern Upload Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    <form method="post" enctype="multipart/form-data" id="uploadForm">
                        <div class="upload-area modern-upload compact-upload" id="dropArea">

                            <!-- Icon (smaller) -->
                            <div class="upload-icon mb-3">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                            </div>

                            <!-- Default State -->
                            <div id="defaultState">
                                <h5 class="mb-2">Drop file here</h5>
                                <p class="text-muted small mb-3">or click to browse</p>

                                <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" 
                                        onclick="document.getElementById('fileInput').click();">
                                    <i class="bi bi-folder2-open me-2"></i>Choose File
                                </button>
                            </div>

                            <!-- Selected File State -->
                            <div id="fileSelectedState" class="d-none">
                                <div class="selected-file-preview p-3 bg-light rounded-3 mb-3">
                                    <i class="bi bi-file-earmark-check-fill text-success fs-3 mb-2"></i>
                                    <div id="fileNameDisplay" class="fw-medium small">database.sqlite</div>
                                    <small class="text-muted">Ready to upload</small>
                                </div>
                                <button type="button" class="btn btn-outline-secondary rounded-pill btn-sm" 
                                        onclick="document.getElementById('fileInput').click();">
                                    Change
                                </button>
                            </div>

                            <p class="mt-3 text-muted small">
                                .sqlite, .db, .sqlite3 • Max 50MB
                            </p>

                            <input type="file" name="db_file" id="fileInput" 
                                   accept=".sqlite,.sqlite3,.db" required style="display:none;">
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="bi bi-shield-lock me-1"></i>
                    Processed locally • Secure session
                </small>
            </div>
        </div>
    </div>
</main>

<footer class="bg-white border-top mt-auto">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-muted small">
            <div><i class="bi bi-code-slash me-1"></i>SQLite Viewer • <?= date('Y') ?></div>
            <div class="mt-2 mt-md-0">Built with <i class="bi bi-heart-fill text-danger small mx-1"></i> Bootstrap & PHP</div>
        </div>
    </div>
</footer>

<script src="assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>