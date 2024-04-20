<?php
require_once '../database/Database.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

try {
    // Get the database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $member_email = $_POST['member_email'] ?? '';
    $member_password = $_POST['member_password'] ?? '';

    // Prepare the data from the POST request
    $member_name = $_POST['member_name'] ?? '';
    $member_phone = $_POST['member_phone'] ?? '';
    $member_address = $_POST['member_address'] ?? '';
    $member_height = $_POST['member_height'] ?? '0'; // Default value if not provided
    $member_weight = $_POST['member_weight'] ?? '0'; // Default value if not provided
    $member_type = $_POST['member_type'] ?? '1'; // Default value if not provided

    // Hash the password
    $hashed_password = password_hash($member_password, PASSWORD_DEFAULT);

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

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO member (member_name, member_email, member_password, member_phone, member_address, member_height, member_weight, member_type, member_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind the parameters
    $stmt->bind_param("sssssssss", $member_name, $member_email, $hashed_password, $member_phone, $member_address, $member_height, $member_weight, $member_type, $member_image_url);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the ID of the newly inserted member
        $member_id = $conn->insert_id;

        // Fetch the member details
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
        echo json_encode([
            "message" => "Member added successfully",
            "member" => $member
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to add member", "error" => $stmt->error]);
    }
} catch (Exception $e) {
    // Handle the exception
    http_response_code(500);
    echo json_encode(["message" => "Server error", "error" => $e->getMessage()]);
}
