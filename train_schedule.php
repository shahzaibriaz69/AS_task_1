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

    header("Location: train_schedule.php");
    exit();
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
    $eid = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM train_schedule WHERE id = $eid");
    $editRow = $res->fetch_assoc();
}


$schedules = $conn->query("
    SELECT ts.*, u.name AS driver_name 
    FROM train_schedule ts
    LEFT JOIN smscampaigner_users u ON ts.driver_id = u.id
    ORDER BY ts.date DESC, ts.start_time DESC
");


$drivers = $conn->query("
    SELECT id, name FROM smscampaigner_users 
    WHERE role = 'employee' ORDER BY name ASC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Train Schedules</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>


    <nav class="navbar">
        <div class="navbar-brand">
            <span class="brand-dot"></span>
            Train Management System
        </div>
        <div class="navbar-links">
            <a href="train_schedule.php" class="active">Schedules</a>
        </div>
    </nav>


    <div class="page-wrapper">


        <div class="page-header">
            <h1>Train Schedules</h1>
            <p>Manage all train schedules, routes and drivers</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- ADD / EDIT FORM -->
        <div class="card">
            <div class="card-head">
                <div class="card-head-title">
                    <?= $editRow ? 'Edit Schedule' : 'Add New Schedule' ?>
                </div>
                <?php if ($editRow): ?>
                    <a href="train_schedule.php" class="btn btn-secondary btn-sm">Cancel</a>
                <?php endif; ?>
            </div>
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
                                value="<?= htmlspecialchars($editRow['destination'] ?? '') ?>"
                                placeholder="e.g. Karachi" required>
                        </div>

                        <div class="form-group lg">
                            <label>Driver</label>
                            <select name="driver_id" required>
                                <option value="">Select driver...</option>
                                <?php
                                $drivers->data_seek(0);
                                while ($d = $drivers->fetch_assoc()): ?>
                                    <option value="<?= $d['id'] ?>" <?= (isset($editRow) && $editRow['driver_id'] == $d['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group btn-col">
                            <label>&nbsp;</label>
                            <?php if ($editRow): ?>
                                <button type="submit" name="edit" class="btn btn-warning">Save Changes</button>
                            <?php else: ?>
                                <button type="submit" name="add" class="btn btn-primary">Add Schedule</button>
                            <?php endif; ?>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-title">All Schedules</div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Driver</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($row = $schedules->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['date']) ?></td>
                                <td><?= htmlspecialchars($row['start_time']) ?></td>
                                <td><?= htmlspecialchars($row['end_time']) ?></td>
                                <td><?= htmlspecialchars($row['starting_station']) ?></td>
                                <td><?= htmlspecialchars($row['destination']) ?></td>
                                <td>
                                    <span class="badge">
                                        <?= htmlspecialchars($row['driver_name'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="view_train_schedule.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-info">View</a>
                                        <a href="train_schedule.php?edit=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <a href="train_schedule.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this schedule?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($schedules->num_rows === 0): ?>
                            <tr>
                                <td colspan="8" class="no-data">No schedules found. Add one above.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>