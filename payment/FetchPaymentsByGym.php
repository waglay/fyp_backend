<?php
// include('../database/Database.php');

// header('Content-Type: application/json');
// header("Access-Control-Allow-Origin: *");

// $gym_ids = json_decode(file_get_contents('php://input'), true);

// if (!is_array($gym_ids)) {
//     http_response_code(400);
//     echo json_encode(["message" => "Invalid input data. An array of gym IDs is expected."]);
//     exit();
// }

// try {
//     $db = Database::getInstance();
//     $conn = $db->getConnection();
// } catch (Exception $e) {
//     http_response_code(500);
//     echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
//     exit();
// }

// $placeholders = implode(',', array_fill(0, count($gym_ids), '?'));
// // $stmt = $conn->prepare("SELECT * FROM payment WHERE gym_id IN ($placeholders)");
// $stmt = $conn->prepare("SELECT payment.*, member.* FROM payment JOIN member on member.member_id=payment.member_id and gym_id IN ($placeholders)");
// $stmt->bind_param(str_repeat('i', count($gym_ids)), ...$gym_ids);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     $payments = $result->fetch_all(MYSQLI_ASSOC);
//     echo json_encode(["message" => "Payments fetched successfully", "payments" => $payments]);
// } else {
//     echo json_encode(["message" => "No payments found"]);
// }

// $stmt->close();
include('../database/Database.php');

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Check if 'gym_id' parameter is set in the query string
if (!isset($_GET['gym_id'])) {
    http_response_code(400);
    echo json_encode(["message" => "Missing 'gym_id' parameter in the request."]);
    exit();
}

// Retrieve the 'gym_id' from the query string
$gym_id = $_GET['gym_id'];

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Prepare the SQL statement with a single placeholder for the gym_id
// $stmt = $conn->prepare("SELECT payment.*, member.* FROM payment JOIN member on member.member_id=payment.member_id and gym_id = ?");
// $stmt = $conn->prepare("SELECT payment.*, member.*, gym.* FROM payment JOIN member on member.member_id=payment.member_id join gym on gym.gym_id=payment.gym_id where gym_id = ?  ORDER BY payment.payment_id DESC");
$stmt = $conn->prepare("SELECT payment.*, member.*, gym.* 
FROM payment 
JOIN member ON member.member_id = payment.member_id 
JOIN gym ON gym.gym_id = payment.gym_id 
WHERE gym.gym_id = ? 
ORDER BY payment.payment_id DESC;
");

$stmt->bind_param('i', $gym_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["message" => "Payments fetched successfully", "payments" => $payments]);
} else {
    echo json_encode(["message" => "No payments found"]);
}

$stmt->close();
