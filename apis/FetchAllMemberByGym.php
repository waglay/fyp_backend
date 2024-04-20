<?php
require_once '../database/Database.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}
if (!isset($_GET['gym_id'])) {
    // If 'memberId' is not set, send a 400 Bad Request response
    http_response_code(400);
    echo json_encode(["message" => "Member ID is required"]);
    exit(); // Terminate the script
} else {
    // If 'memberId' is set, proceed with your logic
    $gym_id = $_GET['gym_id'];
    // Your logic here, e.g., processing the request with the memberId
}

$stmt = $conn->prepare("SELECT payment.*, member.* FROM payment JOIN member on member.member_id=payment.member_id WHERE payment.gym_id = ?");
$stmt->bind_param("i", $gym_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows >  0) {
    $members = $result->fetch_all(MYSQLI_ASSOC);
    // Remove sensitive information from the response
    foreach ($members as &$member) {
        unset($member['member_password'], $member['token_expiry'], $member['member_token']);
    }

    http_response_code(200);
    echo json_encode(["message" => "Members fetched successfully", "members" => $members]);
} else {
    http_response_code(404);
    echo json_encode(["message" => "No users found"]);
}

$stmt->close();
