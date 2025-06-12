<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'config.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($username) || empty($password) || empty($email)) {
        $response['status'] = 'error';
        $response['message'] = 'All fields are required.';
    } else {
        // Basic email validation (you can use more robust validation if needed)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid email format.';
        } else {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $response['status'] = 'error';
                $response['message'] = 'Username or email already exists.';
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert the user into the database
                $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $email);

                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'User registered successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'User registration failed: ' . $stmt->error;
                }
            }

            $stmt->close();
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);

$conn->close();
?>