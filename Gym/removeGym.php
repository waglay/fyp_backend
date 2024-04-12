<?php
include('../database/Database.php');

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
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

$gym_id = isset($_POST['gym_id']) ? $_POST['gym_id'] : '';

if (!$gym_id) {
    http_response_code(400);
    echo json_encode(["message" => "Gym ID is required"]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM gym WHERE gym_id = ?");
$stmt->bind_param("i", $gym_id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Gym deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to delete gym"]);
}

$stmt->close();
?>
