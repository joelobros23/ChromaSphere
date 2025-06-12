<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/';
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.']);
            exit;
        }

        // Check file size (example: limit to 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            http_response_code(400);
            echo json_encode(['message' => 'File size too large. Maximum size is 5MB.']);
            exit;
        }

        // Create a unique filename
        $filename = uniqid() . '.' . $imageFileType;
        $uploadFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // Return the filename
            echo json_encode(['filename' => $filename]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to upload image.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'No image uploaded or an error occurred.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed.']);
}
?>