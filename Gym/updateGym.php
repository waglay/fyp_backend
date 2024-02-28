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

$gym_id = isset($_POST['gym_id']) ? $_POST['gym_id'] : '';
$gym_name = isset($_POST['gym_name']) ? $_POST['gym_name'] : '';
$gym_address = isset($_POST['gym_address']) ? $_POST['gym_address'] : '';
$gym_phone = isset($_POST['gym_phone']) ? $_POST['gym_phone'] : '';
$gym_email = isset($_POST['gym_email']) ? $_POST['gym_email'] : '';
$gym_photos = isset($_POST['gym_photos']) ? $_POST['gym_photos'] : '';

if (!$gym_id) {
    http_response_code(400);
    echo json_encode(["message" => "Gym ID is required"]);
    exit();
}

$stmt = $conn->prepare("UPDATE gym SET gym_name = ?, gym_address = ?, gym_phone = ?, gym_email = ?, gym_photos = ? WHERE gym_id = ?");
$stmt->bind_param("sssssi", $gym_name, $gym_address, $gym_phone, $gym_email, $gym_photos, $gym_id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Gym updated successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to update gym"]);
}

$stmt->close();
?>
