<!-- 
// echo ("running currently");
// require_once '../database/Database.php';

// Define the API endpoint
// $apiEndpoint = '/apis/sign_up.php';

// Check if the request is a POST request to the API endpoint
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === $apiEndpoint) {
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the JSON data from the request body
//     $jsonData = file_get_contents('php://input');
//     $data = json_decode($jsonData, true);

//     // Validate the data
//     if (!isset($data['member_name'], $data['member_email'], $data['member_password'])) {
//         http_response_code(400); // Bad Request
//         echo json_encode(['error' => 'Missing required fields']);
//         exit;
//     }

//     // Connect to the database
//     try {
//         $db = Database::getInstance();
//         $conn = $db->getConnection();

//         // Check if the email already exists
//         $stmt = $conn->prepare("SELECT member_email FROM member WHERE member_email = ?");
//         $stmt->bind_param("s", $data['member_email']);
//         $stmt->execute();
//         $stmt->store_result();

//         if ($stmt->num_rows >  0) {
//             // Email already exists
//             http_response_code(409); // Conflict
//             echo json_encode(['error' => 'Email already exists']);
//             $stmt->close();
//             exit;
//         }

//         // Prepare the SQL statement for inserting the new member
//         $stmt = $conn->prepare("INSERT INTO member (member_name, member_email, member_password, member_phone, member_address, member_hight, member_weight) VALUES (?, ?, ?, ?, ?, ?, ?)");
//         $member_phone = $data['member_phone'] ?? null;
//         $member_address = $data['member_address'] ?? null;
//         $member_hight = $data['member_hight'] ?? null;
//         $member_weight = $data['member_weight'] ?? null;
//         $member_password = password_hash($data['member_password'], PASSWORD_DEFAULT);
//         $stmt->bind_param(
//             // "ssssssd" for string, string, string, string, string, double, double
//             "sssssdd",
//             $data['member_name'],
//             $data['member_email'],
//             // Hash the password
//         // $hashedPassword = password_hash($data['member_password'], PASSWORD_DEFAULT),
//             $member_password,
//             $member_phone,
//             $member_address,
//             $member_hight,
//             $member_weight
//         );

//         // Execute the statement
//         if ($stmt->execute()) {
//             http_response_code(201); // Created
//             echo json_encode(['message' => 'Member added successfully', 'member_id' => $stmt->insert_id]);
//         } else {
//             http_response_code(500); // Internal Server Error
//             echo json_encode(['error' => 'Failed to add member']);
//             $stmt->close();
//         }

//         // Close the statement and connection
//         $stmt->close();
//     } catch (Exception $e) {
//         http_response_code(500); // Internal Server Error
//         echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
//         $stmt->close();
//     }
// } else {
//     // Not a POST request or not the correct endpoint
//     // $stmt->close();
//     http_response_code(404); // Not Found
//     echo json_encode(['error' => 'Not Found']);
// } -->

<?php

// Include the Database class
require_once 'Database.php';

try {
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Guard clause for missing required fields
    if (!isset($data['member_name'], $data['member_email'], $data['member_password'])) {
        throw new Exception('Missing required fields');
    }

    // Check if the user already exists
    $stmt = $conn->prepare("SELECT member_id FROM member WHERE member_email = ?");
    $stmt->bind_param("s", $data['member_email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows >   0) {
        throw new Exception('Email already in use');
    }

    // Hash the password
    $hashedPassword = password_hash($data['member_password'], PASSWORD_DEFAULT);

    // Generate a token
    $token = bin2hex(random_bytes(16)); // Generate a random token

    // Prepare the SQL statement with optional fields
    $sql = "INSERT INTO member (member_name, member_email, member_password, member_phone, member_address, member_height, member_weight, member_token, member_type, token_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $member_phone= isset($data['member_phone']) ? $data['member_phone'] : null;
    $member_address= isset($data['member_address']) ? $data['member_address'] : null;
    $member_heigh= isset($data['member_height']) ? $data['member_height'] : null;
    $member_weigh    = isset($data['member_weight']) ? $data['member_weight'] : null;
    $member_type = isset($data['member_type']) ? $data['member_type'] : null;
    $token_expiry = isset($data['token_expiry']) ? $data['token_expiry'] : null;

    // Bind parameters
    $stmt->bind_param("ssssssssss",   
        $data['member_name'],   
        $data['member_email'],   
        $hashedPassword,   
        $member_phone,
        $member_address,
        $member_height,
        $member_weight,
        $token,
        $member_type,
        $token_expiry
    );

    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception('Failed to register member');
    }

    // Success
    http_response_code(201);
    echo json_encode(['message' => 'Member registered successfully', 'token' => $token]);

    // Close the statement
    $stmt->close();
} catch (Exception $e) {
    // Handle the exception
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
