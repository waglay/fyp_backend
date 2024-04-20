<?php
include('../database/Database.php');

$paymentsPerPage = 10;

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM payment");
$stmt->execute();
$result = $stmt->get_result();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
if ($result->num_rows > 0) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["message" => "Payments fetched successfully", "payments" => $payments]);
} else {
    echo json_encode(["message" => "No payments found"]);
}

$stmt->close();
