<?php
include('../database/Database.php');

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
$member_id = $_GET['member_id'];
$stmt = $conn->prepare("SELECT gym_id FROM gym where owner_id = '$member_id'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows >   0) {
    $gyms = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["message" => "Gyms fetched successfully", "gyms" => $gyms]);
} else {
    echo json_encode(["message" => "No gyms found"]);
}

$stmt->close();
