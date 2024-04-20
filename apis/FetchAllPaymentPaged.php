<?php
include('../database/Database.php');

// Define how many payments to display per page
$paymentsPerPage = 10;

// Get the current page number from the query string
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Calculate the offset for the SQL query based on the current page number
$offset = ($page - 1) * $paymentsPerPage;

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM payment");
$stmt->execute();
$totalResult = $stmt->get_result();
$totalPayments = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalPayments / $paymentsPerPage);

$stmt = $conn->prepare("SELECT * FROM payment LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $paymentsPerPage);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["message" => "Payments fetched successfully", "payments" => $payments, "totalPages" => $totalPages]);
} else {
    $payments = [];
}

$stmt->close();
