<?php
session_start();
require_once('../database/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Prepare the SQL statement
    $sql = "SELECT * FROM member WHERE member_email = '$email'";

    // Execute the SQL statement
    $result = $conn->query($sql);
    // echo "done";

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userPasswordHash = $row['member_password'];


        // header("Location: ../dashboard.php");
        // Verify the password
        if (password_verify($password, $userPasswordHash)) {
            $_SESSION['id'] = $row['member_id'];
            $_SESSION['type'] = $row['member_type'];
            $_SESSION['user'] = $row['member_name'];
            $_SESSION['user_detail'] = $row;
            echo "Login successful. Redirecting..."; // Debugging statement
            header("Location: ../dashboard.php");
            exit();
        }
    }

    header("Location: ../index.php");
    exit();
}
