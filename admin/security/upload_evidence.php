<?php
session_start();
require_once '../auth.php';
requireAdminLogin();

header('Content-Type: application/json');

// Pastikan directory upload ada
$uploadDir = '../../uploads/ohs/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

try {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileType = mime_content_type($fileTmp); // Gunakan mime_content_type untuk validasi yang lebih akurat

    // Validate file size (5MB max)
    if ($fileSize > 5 * 1024 * 1024) {
        throw new Exception('File is too large. Maximum size is 5MB.');
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG and PNG are allowed.');
    }

    // Generate unique filename
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = 'evidence_' . time() . '_' . rand(1000, 9999) . '.' . $fileExt;

    $uploadPath = $uploadDir . $newFileName;
    if (!move_uploaded_file($fileTmp, $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Return success response with relative path
    echo json_encode([
        'success' => true,
        'path' => 'uploads/ohs/' . $newFileName
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}