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

$gym_id = isset($_GET['gym_id']) ? $_GET['gym_id'] : '';

if (!$gym_id) {
    http_response_code(400);
    echo json_encode(["message" => "Gym ID is required"]);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM gym WHERE gym_id = ?");
$stmt->bind_param("i", $gym_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows >   0) {
    $gym = $result->fetch_assoc();
    echo json_encode(["message" => "Gym fetched successfully", "gym" => $gym]);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Gym not found"]);
}

$stmt->close();
?>
