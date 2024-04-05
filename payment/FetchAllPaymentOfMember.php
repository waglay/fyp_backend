<?php
include('../database/Database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}
if (!isset($_GET['memberId'])) {
    // If 'memberId' is not set, send a 400 Bad Request response
    http_response_code(400);
    echo json_encode(["message" => "Member ID is required"]);
    exit(); // Terminate the script
} else {
    // If 'memberId' is set, proceed with your logic
    $memberId = $_GET['memberId'];
    // Your logic here, e.g., processing the request with the memberId
}

// $user_id = $_POST['memberId'];
$stmt = $conn->prepare("SELECT * FROM payment where member_id = ?");
$stmt->bind_param("i", $memberId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows >   0) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["message" => "Payments fetched successfully", "payments" => $payments]);
} else {
    echo json_encode(["message" => "No payments found"]);
}

$stmt->close();
