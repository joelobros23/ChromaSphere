<?php
header('Content-Type: application/json');
require_once 'config.php';

// Check if the user is logged in (e.g., check for a session or JWT)
// This is a placeholder; replace with your actual authentication logic
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    if ($post_id <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid post ID']);
        exit;
    }

    try {
        $pdo = connectDB();

        // Check if the user has already liked the post
        $stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
        $like = $stmt->fetch();

        if ($like) {
            // Unlike the post
            $stmt = $pdo->prepare("DELETE FROM likes WHERE id = :id");
            $stmt->execute(['id' => $like['id']]);
            echo json_encode(['message' => 'Post unliked', 'liked' => false]);
        } else {
            // Like the post
            $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)");
            $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
            echo json_encode(['message' => 'Post liked', 'liked' => true]);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>