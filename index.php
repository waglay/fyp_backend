<!-- <form action="create_member.php" method="post">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name"><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br>
    <!-- Add other fields as necessary -->
<!-- <input type="submit" value="Create Member">
</form>

<form action="update_member.php" method="post">
    <input type="hidden" id="id" name="id" value="1"> <!-- Example ID -->
<!-- <label for="name">Name:</label><br>
    <input type="text" id="name" name="name"><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br> -->
<!-- Add other fields as necessary -->
<!-- <input type="submit" value="Update Member">
</form>  -->



<?php
require_once 'database/Database.php';
echo ("running currently");

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $result = readMembers();
    $members = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
    $members = [];
}


function createMember($name, $email, $password, $phone, $address, $height, $weight, $token, $type, $tokenExpiry)
{
    global $conn;
    $sql = "INSERT INTO member (member_name, member_email, member_password, member_phone, member_address, member_height, member_weight, member_token, member_type, token_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssddsss", $name, $email, $password, $phone, $address, $height, $weight, $token, $type, $tokenExpiry);
    return $stmt->execute();
}

function readMembers()
{
    global $conn;
    $sql = "SELECT * FROM fyp.member";
    $result = $conn->query($sql);
    return $result;
}


function updateMember($id, $name, $email, $password, $phone, $address, $height, $weight, $token, $type, $tokenExpiry)
{
    global $conn;
    $sql = "UPDATE member SET member_name=?, member_email=?, member_password=?, member_phone=?, member_address=?, member_height=?, member_weight=?, member_token=?, member_type=?, token_expiry=? WHERE member_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssddsssi", $name, $email, $password, $phone, $address, $height, $weight, $token, $type, $tokenExpiry, $id);
    return $stmt->execute();
}

function deleteMember($id)
{
    global $conn;
    $sql = "DELETE FROM member WHERE member_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Members</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <!-- <h1>Members</h1> -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Height</th>
                <th>Weight</th>
                <th>Token</th>
                <th>Type</th>
                <th>Token Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $member) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($member['member_id']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_name']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_email']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_phone']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_address']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_height']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_weight']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_token']); ?></td>
                    <td><?php echo htmlspecialchars($member['member_type']); ?></td>
                    <td><?php echo htmlspecialchars($member['token_expiry']); ?></td>
                    <td>
                        <button class="btn btn-primary" onclick="viewDetails(<?php echo $member['member_id']; ?>)">View Full Detail</button>
                        <button class="btn btn-warning" onclick="updateMember(<?php echo $member['member_id']; ?>)">Update</button>
                        <button class="btn btn-danger" onclick="deleteMember(<?php echo $member['member_id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>
        function updateMember(memberId) {
            // Implement logic to update the member
            // This could involve redirecting to an update page or showing a form to update the member's details
            alert('Updating member ID: ' + memberId);
        }

        function deleteMember(memberId) {
            // Implement logic to delete the member
            // This could involve sending a request to the server to delete the member
            alert('Deleting member ID: ' + memberId);
        }

        function viewDetails(memberId) {
            // Implement logic to view full details of the member
            // This could involve fetching the member's details from the server and displaying them in a modal
            alert('Viewing details for member ID: ' + memberId);
        }
    </script>

</body>

</html>