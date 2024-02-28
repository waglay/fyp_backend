<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

include('../database/Database.php');

function addGym($conn, $data) {
    $stmt = $conn->prepare("INSERT INTO gym (gym_name, gym_address, gym_phone, gym_email, gym_photos) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $data['gym_name'], $data['gym_address'], $data['gym_phone'], $data['gym_email'], $data['gym_photos']);
    $stmt->execute();

    return $stmt->affected_rows >   0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gym_name = $_POST['gym_name'];
    $gym_address = $_POST['gym_address'];
    $gym_phone = $_POST['gym_phone'];
    $gym_email = $_POST['gym_email'];
    $gym_photos = json_encode($_FILES['gym_photos']['name']);

    $data = [
        'gym_name' => $gym_name,
        'gym_address' => $gym_address,
        'gym_phone' => $gym_phone,
        'gym_email' => $gym_email,
        'gym_photos' => $gym_photos
    ];

    $upload_dir = 'uploads/';
    for ($i =   0; $i < count($_FILES['gym_photos']['name']); $i++) {
        $file_name = $_FILES['gym_photos']['name'][$i];
        $file_tmp = $_FILES['gym_photos']['tmp_name'][$i];
        $file_path = $upload_dir . uniqid() . '.' . pathinfo($file_name, PATHINFO_EXTENSION);

        move_uploaded_file($file_tmp, $file_path);
    }

    if (addGym($conn, $data)) {
        echo json_encode(["message" => "Gym added successfully!"]);
    } else {
        echo json_encode(["message" => "Failed to add gym."]);
    }
    exit();
}
