<?php
include('../database/Database.php');

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");


// Check if the HTTP method is POST and data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gym_id']) && isset($_POST['gym_name']) && isset($_POST['gym_address'])) {
    // Sanitize and validate input data
    $gym_id = intval($_POST['gym_id']);
    $gym_name = mysqli_real_escape_string($conn, $_POST['gym_name']);
    $gym_address = mysqli_real_escape_string($conn, $_POST['gym_address']);

    // Prepare and execute SQL statement to update gym details
    $sql = "UPDATE gym SET gym_name='$gym_name', gym_address='$gym_address' WHERE gym_id=$gym_id";

    if (mysqli_query($conn, $sql)) {
        // Gym details updated successfully
        http_response_code(200);
        echo json_encode(array("message" => "Gym details updated successfully"));
    } else {
        // Error updating gym details
        http_response_code(500);
        echo json_encode(array("message" => "Failed to update gym details"));
    }
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(array("message" => "Invalid request"));
}
?>


// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
// http_response_code(405);
// echo json_encode(["message" => "Method not allowed"]);
// exit();
// }

// try {
// $db = Database::getInstance();
// $conn = $db->getConnection();
// } catch (Exception $e) {
// http_response_code(500);
// echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
// exit();
// }

// $gym_id = isset($_POST['gym_id']) ? $_POST['gym_id'] : '';
// $gym_name = isset($_POST['gym_name']) ? $_POST['gym_name'] : '';
// $gym_address = isset($_POST['gym_address']) ? $_POST['gym_address'] : '';
// $gym_phone = isset($_POST['gym_phone']) ? $_POST['gym_phone'] : '';
// $gym_email = isset($_POST['gym_email']) ? $_POST['gym_email'] : '';
// $gym_photos = isset($_POST['gym_photos']) ? $_POST['gym_photos'] : '';
// $gym_owner = isset($_POST['gym_owner']) ? $_POST['gym_owner'] : '';

// if (!$gym_id) {
// http_response_code(400);
// echo json_encode(["message" => "Gym ID is required"]);
// exit();
// }

// $stmt = $conn->prepare("UPDATE gym SET gym_name = ?, gym_address = ?, gym_phone = ?, gym_email = ?, gym_photos = ?, owner_id=? WHERE gym_id = ?");
// $stmt->bind_param("sssssii", $gym_name, $gym_address, $gym_phone, $gym_email, $gym_photos, $gym_owner, $gym_id);

// if ($stmt->execute()) {
// echo json_encode(["message" => "Gym updated successfully"]);
// } else {
// http_response_code(500);
// echo json_encode(["message" => "Failed to update gym"]);
// }

// $stmt->close();