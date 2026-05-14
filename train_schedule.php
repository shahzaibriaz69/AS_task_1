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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Train Schedule</title>
</head>

<body>
    <div class="container">
        <div class="page-title">
            <span>🚆</span> Train Schedule Management
        </div>
        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>">
                <?= $msgType === 'success' ? '✅' : '❌' ?>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <form method="POST">

                <?php if ($editRow): ?>
                    <input type="hidden" name="id" value="<?= $editRow['id'] ?>">
                <?php endif; ?>

                <div class="form-row">

                    <div class="form-group md">
                        <label>Date</label>
                        <input type="date" name="date" value="<?= $editRow['date'] ?? '' ?>" required>
                    </div>

                    <div class="form-group sm">
                        <label>Start Time</label>
                        <input type="time" name="start_time" value="<?= $editRow['start_time'] ?? '' ?>" required>
                    </div>

                    <div class="form-group sm">
                        <label>End Time</label>
                        <input type="time" name="end_time" value="<?= $editRow['end_time'] ?? '' ?>" required>
                    </div>

                    <div class="form-group lg">
                        <label>Starting Station</label>
                        <input type="text" name="starting_station"
                            value="<?= htmlspecialchars($editRow['starting_station'] ?? '') ?>"
                            placeholder="e.g. Lahore" required>
                    </div>

                    <div class="form-group lg">
                        <label>Destination</label>
                        <input type="text" name="destination"
                            value="<?= htmlspecialchars($editRow['destination'] ?? '') ?>" placeholder="e.g. Karachi"
                            required>
                    </div>

                    <div class="form-group lg">
                        <label>Driver</label>
                        <select name="driver_id" required>
                            <option value="">-- Select Driver --</option>
                            <?php
                            $drivers->data_seek(0);
                            while ($d = $drivers->fetch_assoc()): ?>
                                <option value="<?= $d['id'] ?>" <?= (isset($editRow) && $editRow['driver_id'] == $d['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group sm" style="min-width:unset;">
                        <?php if ($editRow): ?>
                            <label>&nbsp;</label>
                            <button type="submit" name="edit" class="btn btn-warning">💾 Update</button>
                        <?php else: ?>
                            <label>&nbsp;</label>
                            <button type="submit" name="add" class="btn btn-primary">➕ Add</button>
                        <?php endif; ?>
                    </div>

                    <?php if ($editRow): ?>
                        <div class="form-group sm" style="min-width:unset;">
                            <label>&nbsp;</label>
                            <a href="train_schedule.php" class="btn btn-secondary">✕ Cancel</a>
                        </div>
                    <?php endif; ?>

                </div>
            </form>
        </div>

    </div>

</body>

</html>