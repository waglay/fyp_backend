<?php
require_once "../database/Database.php";
header("Access-Control-Allow-Origin: *");

function getAllMotivations($conn)
{
    $sql = "SELECT * FROM motivations";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $motivations = array();
        while ($row = $result->fetch_assoc()) {
            $motivations[] = $row;
        }
        header('Content-Type: application/json');
        return json_encode(["message" => "Motivations fetched successfully", "Motivations" => $motivations]);
    } else {
        header('Content-Type: application/json');
        return json_encode(array());
    }
}

// Define the API endpoint route
$route = '/api/motivations';

// Check if the request matches the API endpoint route
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $jsonData = getAllMotivations($conn);
        echo $jsonData;
    } catch (Exception $e) {
        http_response_code(500);
        echo "Database connection failed: " . $e->getMessage();
    }
} else {
    http_response_code(404);
    echo "Route not found";
}
