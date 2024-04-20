<?php
include('../database/Database.php');

// Define how many payments to display per page
$paymentsPerPage = 10;

// Get the current page number from the query string
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Calculate the offset for the SQL query based on the current page number
$offset = ($page - 1) * $paymentsPerPage;

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM payment");
$stmt->execute();
$totalResult = $stmt->get_result();
$totalPayments = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalPayments / $paymentsPerPage);

$stmt = $conn->prepare("SELECT * FROM payment LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $paymentsPerPage);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $payments = [];
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            margin-right: 5px;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .no-payments {
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Payments</h2>
        <?php if (!empty($payments)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Payment Date</th>
                        <th>Member ID</th>
                        <th>Gym ID</th>
                        <th>Months</th>
                        <th>Till When</th>
                        <th>Is Valid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment) : ?>
                        <tr>
                            <td><?php echo $payment['payment_id']; ?></td>
                            <td><?php echo $payment['payment_date']; ?></td>
                            <td><?php echo $payment['member_id']; ?></td>
                            <td><?php echo $payment['gym_id']; ?></td>
                            <td><?php echo $payment['months']; ?></td>
                            <td><?php echo $payment['tillwhen']; ?></td>
                            <td><?php echo $payment['is_valid'] ? 'Yes' : 'No'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo ($page - 1); ?>">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" <?php if ($page === $i) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?php echo ($page + 1); ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <p class="no-payments">No payments found</p>
        <?php endif; ?>
    </div>
</body>

</html>
