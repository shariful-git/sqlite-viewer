<?php
// upload.php - Upload handling logic (included in index.php and viewer.php if needed)

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['db_file']) && $_FILES['db_file']['error'] === UPLOAD_ERR_OK) {
    $uploadedFile = $_FILES['db_file']['tmp_name'];
    $originalName = basename($_FILES['db_file']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, ['sqlite', 'sqlite3', 'db'])) {
        $error = 'Only .sqlite, .sqlite3, or .db files are allowed.';
    } elseif ($_FILES['db_file']['size'] > 50 * 1024 * 1024) {
        $error = 'File too large (max 50MB).';
    } else {
        $uploadDir = __DIR__ . '/temp/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeName = session_id() . '_' . uniqid() . '.' . $ext;
        $dbFile = $uploadDir . $safeName;

        if (move_uploaded_file($uploadedFile, $dbFile)) {
            // Clean old files for this session
            foreach (glob($uploadDir . session_id() . '_*') as $old) {
                if ($old !== $dbFile) @unlink($old);
            }

            $_SESSION['current_db_file'] = $dbFile;
            $_SESSION['original_name'] = $originalName;

            header('Location: viewer.php');
            exit;
        } else {
            $error = 'Failed to save file.';
        }
    }
}

// Clear session (for "Upload New File" button)
if (isset($_GET['clear'])) {
    if (!empty($_SESSION['current_db_file']) && file_exists($_SESSION['current_db_file'])) {
        @unlink($_SESSION['current_db_file']);
    }
    unset($_SESSION['current_db_file'], $_SESSION['original_name']);
    header('Location: index.php');
    exit;
}