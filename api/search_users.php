<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search_term = isset($_GET['query']) ? $_GET['query'] : '';

    if (empty($search_term)) {
        echo json_encode(['error' => 'Search term is required.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, username, profile_picture FROM users WHERE username LIKE ?");
    $search_term = '%' . $search_term . '%';
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$conn->close();
?>