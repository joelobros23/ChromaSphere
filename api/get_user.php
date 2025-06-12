<?php
header('Content-Type: application/json');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($user_id === null) {
        http_response_code(400);
        echo json_encode(['message' => 'User ID is required.']);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT id, username, email, profile_picture, bio, created_at FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found.']);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed. Use GET.']);
}
?>