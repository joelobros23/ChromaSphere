<?php
header('Content-Type: application/json');
session_start();

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . uniqid() . '_' . $file_name; // Ensure unique filenames
        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Allow certain file formats
        $allowable_types = array("jpg", "png", "jpeg", "gif");
        if (!in_array($imageFileType, $allowable_types)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.']);
            exit;
        }

        // Check file size (example limit: 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            echo json_encode(['status' => 'error', 'message' => 'Sorry, your file is too large.']);
            exit;
        }


        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $target_file;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Sorry, there was an error uploading your file.']);
            exit;
        }
    }

    try {
        $pdo = connectDB();
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image) VALUES (:user_id, :title, :content, :image)");
        $stmt->execute(['user_id' => $user_id, 'title' => $title, 'content' => $content, 'image' => $image]);

        echo json_encode(['status' => 'success', 'message' => 'Post created successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>