<?php
require_once '../database/Database.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit(); // Guard clause to exit if the request method is not POST
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit(); // Guard clause to exit if the database connection fails
}

$member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '';
$member_name = isset($_POST['member_name']) ? $_POST['member_name'] : '';
$member_email = isset($_POST['member_email']) ? $_POST['member_email'] : '';
// Add other fields as needed

if (!$member_id) {
    http_response_code(400);
    echo json_encode(["message" => "Member ID is required"]);
    exit(); // Guard clause to exit if the member ID is not provided
}

$stmt = $conn->prepare("UPDATE member SET member_name = ?, member_email = ? WHERE member_id = ?");
$stmt->bind_param("ssi", $member_name, $member_email, $member_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Member updated successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to update member"]);
}

$stmt->close();
