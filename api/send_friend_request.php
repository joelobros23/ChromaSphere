<?php
header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id1 = $_SESSION['user_id'];
    $user_id2 = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($user_id2 <= 0 || $user_id1 == $user_id2) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
        exit;
    }

    $pdo = connectDB();

    try {
        // Check if a friendship already exists (either direction)
        $stmt = $pdo->prepare("SELECT * FROM friendships WHERE (user_id1 = :user_id1 AND user_id2 = :user_id2) OR (user_id1 = :user_id2 AND user_id2 = :user_id1)");
        $stmt->execute(['user_id1' => $user_id1, 'user_id2' => $user_id2]);
        $existingFriendship = $stmt->fetch();

        if ($existingFriendship) {
            if ($existingFriendship['status'] == 'pending') {
                 if($existingFriendship['user_id1'] == $user_id1){
                    echo json_encode(['status' => 'error', 'message' => 'Friend request already sent.']);
                 } else {
                    echo json_encode(['status' => 'error', 'message' => 'You have a pending friend request from this user.']);
                 }
            } elseif ($existingFriendship['status'] == 'accepted') {
                echo json_encode(['status' => 'error', 'message' => 'Already friends.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Friend request was previously rejected.']);
            }
            exit;
        }

        // Create the friend request
        $stmt = $pdo->prepare("INSERT INTO friendships (user_id1, user_id2) VALUES (:user_id1, :user_id2)");
        $stmt->execute(['user_id1' => $user_id1, 'user_id2' => $user_id2]);

        echo json_encode(['status' => 'success', 'message' => 'Friend request sent.']);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>