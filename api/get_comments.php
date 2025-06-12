<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

    if ($post_id > 0) {
        $sql = "SELECT comments.id, comments.content, comments.created_at, users.username, users.profile_picture 
                FROM comments 
                INNER JOIN users ON comments.user_id = users.id 
                WHERE comments.post_id = ?
                ORDER BY comments.created_at ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $comments = array();
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        echo json_encode($comments);
    } else {
        echo json_encode(array("message" => "Invalid post ID."));
    }
} else {
    echo json_encode(array("message" => "Invalid request method."));
}

$conn->close();
?>