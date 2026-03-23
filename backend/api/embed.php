<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/Steganography.php';
require_once '../includes/Encryption.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Image upload failed');
    }

    if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
        throw new Exception('Message is required');
    }

    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = 'upload_' . time() . '_' . basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    $message = trim($_POST['message']);
    $encryptionKey = isset($_POST['encryption_key']) ? trim($_POST['encryption_key']) : '';

    // Encrypt message if key provided
    if (!empty($encryptionKey)) {
        $message = Encryption::encrypt($message, $encryptionKey);
    }

    // Create steganography instance and embed message
    $stego = new Steganography($uploadPath);
    $stego->embedMessage($message);

    // Generate output filename
    $pathInfo = pathinfo($fileName);
    $outputFileName = 'stego_' . time() . '_' . $pathInfo['filename'] . '.png';
    $outputPath = $uploadDir . $outputFileName;

    // Save stego image
    $stego->saveImage($outputPath, IMAGETYPE_PNG);

    // Clean up original upload
    unlink($uploadPath);

    $downloadUrl = 'http://localhost:8000/uploads/' . $outputFileName;

    echo json_encode([
        'success' => true,
        'message' => 'Message successfully embedded in image',
        'downloadUrl' => $downloadUrl
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
