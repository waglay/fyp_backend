<?php

require_once '../database/Database.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    // Get database instance
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // SQL query to get total number of membership sales per month for the past 12 months
    $query = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, COUNT(DISTINCT member_id) as total_memberships_sold
              FROM payment
              WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY month
              ORDER BY month DESC";
    $result = $conn->query($query);

    // Prepare an array to hold the results
    $Monthly12data = array();
    while ($row = $result->fetch_assoc()) {
        $Monthly12data[] = $row;
    }

    // SQL query to get total revenue generated this month
    $query = "SELECT SUM(g.gym_price) as total_revenue_this_month
              FROM payment p JOIN gym g ON p.gym_id = g.gym_id
              WHERE MONTH(p.payment_date) = MONTH(CURDATE()) AND YEAR(p.payment_date) = YEAR(CURDATE())";
    $result = $conn->query($query);
    $totalRevenueThisMonth = $result->fetch_assoc()['total_revenue_this_month'];
    // $data['total_revenue_this_month'] = $totalRevenueThisMonth;

    // SQL query to get 10 most recent payments
    $query = "SELECT * FROM payment ORDER BY payment_date DESC LIMIT 10";
    $result = $conn->query($query);
    $recentPayments = $result->fetch_all(MYSQLI_ASSOC);

    // Output the data as JSON
    http_response_code(200);
    echo json_encode(
        [
            "message" => "dashboard data retrieved successfully",
            "12_month_history" => $Monthly12data,
            "total_revenue_this_month" => $totalRevenueThisMonth,
            "recent_payments" => $recentPayments
        ]
    );
    // $conn->close();
    // $db->__destruct();
} catch (Exception $e) {
    // Handle the exception and output an error message as JSON
    echo json_encode(array("error" => $e->getMessage()));
}
