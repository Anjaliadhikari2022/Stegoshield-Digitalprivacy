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

    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = 'extract_' . time() . '_' . basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    $encryptionKey = isset($_POST['encryption_key']) ? trim($_POST['encryption_key']) : '';

    // Create steganography instance and extract message
    $stego = new Steganography($uploadPath);
    $extractedMessage = $stego->extractMessage();

    // Decrypt message if key provided
    if (!empty($encryptionKey)) {
        try {
            $extractedMessage = Encryption::decrypt($extractedMessage, $encryptionKey);
        } catch (Exception $e) {
            throw new Exception('Decryption failed: ' . $e->getMessage());
        }
    }

    // Clean up uploaded file
    unlink($uploadPath);

    if (empty($extractedMessage)) {
        throw new Exception('No hidden message found in this image');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Message successfully extracted from image',
        'extractedMessage' => $extractedMessage
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
