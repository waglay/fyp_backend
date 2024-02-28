<?php
include('../database/Database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$payment_date = $_POST['payment_date'];
$payment_amount = $_POST['payment_amount'];
$payment_type = $_POST['payment_type'];
$payment_status = $_POST['payment_status'];
$member_id = $_POST['member_id'];
$gym_id = $_POST['gym_id'];

$stmt = $conn->prepare("INSERT INTO payment (payment_date, payment_amount, payment_type, payment_status, member_id, gym_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sidsis", $payment_date, $payment_amount, $payment_type, $payment_status, $member_id, $gym_id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Payment registered successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to register payment"]);
}

$stmt->close();
?>
