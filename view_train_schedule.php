<?php
include 'db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header("Location: train_schedule.php");
    exit;
}

$message = "";
$msgType = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_passenger'])) {
    $user_id = intval($_POST['user_id']);

    // Duplicate check
    $check = $conn->query("SELECT id FROM smsCampaigner_train_passengers 
                           WHERE train_id=$id AND user_id=$user_id");
    if ($check->num_rows > 0) {
        $message = "This passenger is already added to this schedule.";
        $msgType = "error";
    } else {
        $conn->query("INSERT INTO smsCampaigner_train_passengers (train_id, user_id) 
                      VALUES ($id, $user_id)");
        $message = "Passenger added successfully.";
        $msgType = "success";
    }
}


$result = $conn->query("
    SELECT ts.*, u.name AS driver_name 
    FROM train_schedule ts
    LEFT JOIN smsCampaigner_users u ON ts.driver_id = u.id
    WHERE ts.id = $id
");

if ($result->num_rows === 0) {
    header("Location: train_schedule.php");
    exit;
}
$schedule = $result->fetch_assoc();


$passengers = $conn->query("
    SELECT u.name, u.email, u.phone, u.address
    FROM smsCampaigner_train_passengers tp
    JOIN smsCampaigner_users u ON tp.user_id = u.id
    WHERE tp.train_id = $id
    ORDER BY u.name ASC
");


$users = $conn->query("SELECT id, name FROM smsCampaigner_users ORDER BY name ASC");
?>