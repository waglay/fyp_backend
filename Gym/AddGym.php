<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

include('../database/Database.php');

function addGym($conn, $data)
{
    // Adjust the query to include the gym_price column
    $stmt = $conn->prepare("INSERT INTO gym (gym_name, gym_address, gym_phone, gym_email, owner_id, gym_price) VALUES (?, ?, ?, ?, ?, ?)");
    // Bind the new parameter for gym_price
    $stmt->bind_param("ssssss", $data['gym_name'], $data['gym_address'], $data['gym_phone'], $data['gym_email'], $data['owner_id'], $data['gym_price']);
    $stmt->execute();

    return $stmt->affected_rows > 0;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gym_name = $_POST['gym_name'];
    $gym_address = $_POST['gym_address'];
    $gym_phone = $_POST['gym_phone'];
    $gym_email = $_POST['gym_email'];
    $gym_price = $_POST['gym_price'];
    $gym_owner = $_POST['gym_owner'];

    $data = [
        'gym_name' => $gym_name,
        'gym_address' => $gym_address,
        'gym_phone' => $gym_phone,
        'gym_email' => $gym_email,
        'owner_id' => $gym_owner,
        'gym_price' => $gym_price
    ];

    if (addGym($conn, $data)) {
        http_response_code(200);
        echo json_encode(["message" => "Gym added successfully!"],);
    } else {
        http_response_code(204);
        echo json_encode(["message" => "Failed to add gym."]);
    }
    // $upload_dir = 'uploads/';
    // if (isset($_FILES['gym_photos'])) {
    //     $file_name = $_FILES['gym_photos']['name'];
    //     $file_tmp = $_FILES['gym_photos']['tmp_name'];
    //     $file_path = $upload_dir . $gym_name . '_' . time() . '.' . pathinfo($file_name, PATHINFO_EXTENSION);

    //     move_uploaded_file($file_tmp, $file_path);

    //     if (addGym($conn, $data, $file_path)) {
    //         echo json_encode(["message" => "Gym added successfully!"]);
    //     } else {
    //         echo json_encode(["message" => "Failed to add gym."]);
    //     }
    // } else {
    //     echo json_encode(["message" => "No image uploaded."]);
    // }
    exit();
}
