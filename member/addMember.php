<?php
// Include the database connection file
require_once '../database/Database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the database connection
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
        exit();
    }

    // Prepare the data from the POST request
    $member_name = isset($_POST['member_name']) ? $_POST['member_name'] : '';
    $member_email = isset($_POST['member_email']) ? $_POST['member_email'] : '';
    $member_password = isset($_POST['member_password']) ? $_POST['member_password'] : '';
    $member_phone = isset($_POST['member_phone']) ? $_POST['member_phone'] : '';
    $member_address = isset($_POST['member_address']) ? $_POST['member_address'] : '';
    $member_height = isset($_POST['member_height']) ? $_POST['member_height'] : '';
    $member_weight = isset($_POST['member_weight']) ? $_POST['member_weight'] : '';
    $member_type = isset($_POST['member_type']) ? $_POST['member_type'] : '';

    // Hash the password
    $hashed_password = password_hash($member_password, PASSWORD_DEFAULT);

    // Generate a member token
    $member_token = bin2hex(random_bytes(16));

    // Set the token expiry time (1 hour from now)
    $token_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO member (member_name, member_email, member_password, member_phone, member_address, member_height, member_weight, member_token, member_type, token_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind the parameters
    $stmt->bind_param("ssssssssss", $member_name, $member_email, $hashed_password, $member_phone, $member_address, $member_height, $member_weight, $member_token, $member_type, $token_expiry);

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
        // $member = $result->mysqli_fetch_assoc();

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
        echo json_encode(["message" => "Failed to add member"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
