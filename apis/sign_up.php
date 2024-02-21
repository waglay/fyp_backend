<?php
// echo ("running currently");
require_once '../database/Database.php';

// Define the API endpoint
// $apiEndpoint = '/apis/sign_up.php';

// Check if the request is a POST request to the API endpoint
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === $apiEndpoint) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    // Validate the data
    if (!isset($data['member_name'], $data['member_email'], $data['member_password'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Connect to the database
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT member_email FROM member WHERE member_email = ?");
        $stmt->bind_param("s", $data['member_email']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows >  0) {
            // Email already exists
            http_response_code(409); // Conflict
            echo json_encode(['error' => 'Email already exists']);
            $stmt->close();
            exit;
        }

        // Prepare the SQL statement for inserting the new member
        $stmt = $conn->prepare("INSERT INTO member (member_name, member_email, member_password, member_phone, member_address, member_hight, member_weight) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $member_phone = $data['member_phone'] ?? null;
        $member_address = $data['member_address'] ?? null;
        $member_hight = $data['member_hight'] ?? null;
        $member_weight = $data['member_weight'] ?? null;
        $stmt->bind_param(
            // "ssssssd" for string, string, string, string, string, double, double
            "sssssdd",
            $data['member_name'],
            $data['member_email'],
            $data['member_password'],
            $member_phone,
            $member_address,
            $member_hight,
            $member_weight
        );

        // Execute the statement
        if ($stmt->execute()) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Member added successfully', 'member_id' => $stmt->insert_id]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to add member']);
            $stmt->close();
        }

        // Close the statement and connection
        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
        $stmt->close();
    }
} else {
    // Not a POST request or not the correct endpoint
    // $stmt->close();
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Not Found']);
}
