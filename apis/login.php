<?php
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}

require '../database/Database.php';
session_start();
try {
    // Get the database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    header("Access-Control-Allow-Origin: *");

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method']);
        exit();
    }

    // Get the input from the request
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM member WHERE member_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists
    if ($user) {
        // Verify the password
        if (password_verify($password, $user['member_password'])) {

            $token = bin2hex(random_bytes(16));
            $stmt = $conn->prepare("insert into api_tokens (token, member_id, `date updated`) values (?, ?, sysdate())");
            $stmt->bind_param("ss", $token, $user['member_id']);
            $success = $stmt->execute();
            if ($success) {
                // Login successful
                unset($user['member_password']);
                unset($user['member_token']);
                $_SESSION['token'] = $token;
                $_SESSION['id'] = $user['member_id'];
                $_SESSION['user'] = $user;
                http_response_code(200);
                echo json_encode(['message' => 'Login successful', 'member' => $user, 'Token' => $token]);
            } else {
                // Update failed
                http_response_code(500);
                echo json_encode(['message' => 'Update failed']);
            }
        } else {
            // Login failed
            http_response_code(401);
            echo json_encode(['message' => 'Login failed']);
        }
    } else {
        // User not found
        http_response_code(404);
        echo json_encode(['message' => 'User not found']);
    }
} catch (Exception $e) {
    // Handle the exception
    http_response_code(500);
    echo json_encode(['message' => 'Server error', 'error' => $e->getMessage()]);
}
