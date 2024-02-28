<?php
require_once '../database/Database.php';
header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM member");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows >  0) {
    $members = $result->fetch_all(MYSQLI_ASSOC);
    // Remove sensitive information from the response
    foreach ($members as &$member) {
        unset($member['member_password']);
    }

    http_response_code(200);
    echo json_encode(["message" => "Users fetched successfully", "users" => $members]);
} else {
    http_response_code(404);
    echo json_encode(["message" => "No users found"]);
}

$stmt->close();
