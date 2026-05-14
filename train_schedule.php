<?php
include 'db.php';

$message = "";
$msgType = "";


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM train_schedule WHERE id = $id");
    $message = "Schedule deleted successfully.";
    $msgType = "success";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $date = $conn->real_escape_string($_POST['date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $starting = $conn->real_escape_string($_POST['starting_station']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $driver_id = intval($_POST['driver_id']);

    $conn->query("INSERT INTO train_schedule 
        (date, start_time, end_time, starting_station, destination, driver_id) 
        VALUES ('$date','$start_time','$end_time','$starting','$destination','$driver_id')");
    $message = "Schedule added successfully.";
    $msgType = "success";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $date = $conn->real_escape_string($_POST['date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $starting = $conn->real_escape_string($_POST['starting_station']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $driver_id = intval($_POST['driver_id']);

    $conn->query("UPDATE train_schedule SET
        date='$date', start_time='$start_time', end_time='$end_time',
        starting_station='$starting', destination='$destination', driver_id='$driver_id'
        WHERE id=$id");
    $message = "Schedule updated successfully.";
    $msgType = "success";
}


$editRow = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM train_schedule WHERE id = $id");
    $editRow = $result->fetch_assoc();
}


$schedules = $conn->query("
    SELECT ts.*, u.name AS driver_name 
    FROM train_schedule ts
    LEFT JOIN smsCampaigner_users u ON ts.driver_id = u.id
    ORDER BY ts.date DESC
");


$drivers = $conn->query("SELECT id, name FROM smsCampaigner_users WHERE role = 'employee' ORDER BY name ASC");
?>