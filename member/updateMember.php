<?php
// require_once '../database/Database.php';
// header('Content-Type: application/json');
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     http_response_code(405);
//     echo json_encode(["message" => "Method not allowed"]);
//     exit(); // Guard clause to exit if the request method is not POST
// }

// try {
//     $db = Database::getInstance();
//     $conn = $db->getConnection();
// } catch (Exception $e) {
//     http_response_code(500);
//     echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
//     exit(); // Guard clause to exit if the database connection fails
// }

// $member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '';
// $member_name = isset($_POST['member_name']) ? $_POST['member_name'] : '';
// $member_email = isset($_POST['member_email']) ? $_POST['member_email'] : '';
// // Add other fields as needed

// if (!$member_id) {
//     http_response_code(400);
//     echo json_encode(["message" => "Member ID is required"]);
//     exit(); // Guard clause to exit if the member ID is not provided
// }

// $stmt = $conn->prepare("UPDATE member SET member_name = ?, member_email = ? WHERE member_id = ?");
// $stmt->bind_param("ssi", $member_name, $member_email, $member_id);

// if ($stmt->execute()) {
//     http_response_code(200);
//     echo json_encode(["message" => "Member updated successfully"]);
// } else {
//     http_response_code(500);
//     echo json_encode(["message" => "Failed to update member"]);
// }

// $stmt->close();


// require_once '../database/Database.php';
// header('Content-Type: application/json');
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     http_response_code(405);
//     echo json_encode(["message" => "Method not allowed"]);
//     exit(); // Guard clause to exit if the request method is not POST
// }

// try {
//     $db = Database::getInstance();
//     $conn = $db->getConnection();
// } catch (Exception $e) {
//     http_response_code(500);
//     echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
//     exit(); // Guard clause to exit if the database connection fails
// }

// $member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '';
// $member_name = isset($_POST['member_name']) ? $_POST['member_name'] : '';
// $member_email = isset($_POST['member_email']) ? $_POST['member_email'] : '';
// $member_phone = isset($_POST['member_phone']) ? $_POST['member_phone'] : '';
// $member_address = isset($_POST['member_address']) ? $_POST['member_address'] : '';
// $member_password = isset($_POST['member_password']) ? $_POST['member_password'] : '';
// $member_height = isset($_POST['member_height']) ? $_POST['member_height'] : '';
// $member_weight = isset($_POST['member_weight']) ? $_POST['member_weight'] : '';
// $member_type = isset($_POST['member_type']) ? $_POST['member_type'] : '';

// if (!$member_id) {
//     http_response_code(400);
//     echo json_encode(["message" => "Member ID is required"]);
//     exit(); // Guard clause to exit if the member ID is not provided
// }

// // Prepare the SQL query to update the member details
// $stmt = $conn->prepare("UPDATE member SET member_name = ?, member_email = ?, member_phone = ?, member_address = ?, member_height = ?, member_weight = ?, member_type = ? WHERE member_id = ?");
// $stmt->bind_param("sssssssi", $member_name, $member_email, $member_phone, $member_address, $member_height, $member_weight, $member_type, $member_id);

// if ($stmt->execute()) {
//     http_response_code(200);
//     echo json_encode(["message" => "Member updated successfully"]);
// } else {
//     http_response_code(500);
//     echo json_encode(["message" => "Failed to update member"]);
// }

// $stmt->close();


require_once '../database/Database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit(); // Guard clause to exit if the request method is not POST
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit(); // Guard clause to exit if the database connection fails
}

$member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '';
$member_name = isset($_POST['member_name']) ? $_POST['member_name'] : '';
$member_email = isset($_POST['member_email']) ? $_POST['member_email'] : '';
$member_phone = isset($_POST['member_phone']) ? $_POST['member_phone'] : '';
$member_address = isset($_POST['member_address']) ? $_POST['member_address'] : '';
$member_password = isset($_POST['member_password']) ? $_POST['member_password'] : '';
$member_height = isset($_POST['member_height']) ? $_POST['member_height'] : '';
$member_weight = isset($_POST['member_weight']) ? $_POST['member_weight'] : '';
$member_type = isset($_POST['member_type']) ? $_POST['member_type'] : '1';

if (!$member_id) {
    http_response_code(400);
    echo json_encode(["message" => "Member ID is required"]);
    exit(); // Guard clause to exit if the member ID is not provided
}

// Handle the image upload
$member_image_url = null;
if (isset($_FILES['member_image']) && $_FILES['member_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['member_image'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    // Generate a unique file name based on member name and phone
    $unique_file_name = $member_name . '_' . $member_phone . '.' . pathinfo($file_name, PATHINFO_EXTENSION);

    // Move the uploaded file to the ../assets folder
    $target_dir = '../assets/';
    $target_file = $target_dir . $unique_file_name;
    if (move_uploaded_file($file_tmp, $target_file)) {
        $member_image_url = $target_file;
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to upload the image"]);
        exit();
    }
}

// Prepare the SQL query to update the member details
$stmt = $conn->prepare("UPDATE member SET member_name = ?, member_email = ?, member_phone = ?, member_address = ?, member_height = ?, member_weight = ?, member_type = ?, member_image_url = ? WHERE member_id = ?");
$stmt->bind_param("ssssssssi", $member_name, $member_email, $member_phone, $member_address, $member_height, $member_weight, $member_type, $member_image_url, $member_id);

if ($stmt->execute()) {
    $stmt = $conn->prepare("SELECT * FROM member WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Remove sensitive information from the response
    unset($member['member_password']);
    http_response_code(200);
    echo json_encode(["message" => "Member updated successfully", "member" => $member]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to update member"]);
    $stmt->close();
}
