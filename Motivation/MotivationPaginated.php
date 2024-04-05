<?php
include('../database/Database.php');

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Pagination parameters with default values
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// SQL query with pagination
$stmt = $conn->prepare("SELECT * FROM motivations LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $motivations = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["message" => "Motivations fetched successfully", "Motivations" => $motivations]);
} else {
    echo json_encode(["message" => "No motivations found"]);
}

$stmt->close();
