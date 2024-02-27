<?php
require_once '../database/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit(); // Guard clause to exit if the request method is not GET
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit(); // Guard clause to exit if the database connection fails
}

$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : '';

if (!$member_id) {
    http_response_code(400);
    echo json_encode(["message" => "Member ID is required"]);
    exit(); // Guard clause to exit if the member ID is not provided
}

$stmt = $conn->prepare("SELECT * FROM member WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows >  0) {
    $member = $result->fetch_assoc();
    // Remove sensitive information from the response
    unset($member['member_password']);

    http_response_code(200);
    echo json_encode(["message" => "Member fetched successfully", "member" => $member]);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Member not found"]);
}

$stmt->close();
