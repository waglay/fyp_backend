<?php
require '../database/Database.php'; // Assuming Database class is in the same directory

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Extract gym_id and months from the request
    $gym_id = $_POST['gymID'] ?? null;
    $months = $_POST['months'] ?? null;
    $date = $_POST['date'] ?? null;
    $user_id = $_POST['userID'] ?? null;

    // Check if gym_id and months are provided
    if ($gym_id === null || $months === null || $date === null || $user_id === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Fields incomplete.']);
        exit;
    }

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Calculate payment_date and tillwhen
        $payment_date = date("Y-m-d");
        // $tillwhen = date("Y-m-d", strtotime("+$months months"));
        $tillwhen = date("Y-m-d", strtotime("$date +$months months"));

        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO payment (gym_id, payment_date, months, tillwhen, member_id) VALUES (?, ?, ?, ?,?)");
        $stmt->bind_param("issss", $gym_id, $payment_date, $months, $tillwhen, $user_id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Payment record created successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create payment record.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
