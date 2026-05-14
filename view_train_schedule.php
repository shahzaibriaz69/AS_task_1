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

    $check = $conn->query("SELECT id FROM smscampaigner_train_passengers 
                           WHERE train_id=$id AND user_id=$user_id");
    if ($check->num_rows > 0) {
        $message = "This passenger is already added to this schedule.";
        $msgType = "error";
    } else {
        $conn->query("INSERT INTO smscampaigner_train_passengers (train_id, user_id) 
                      VALUES ($id, $user_id)");
        $message = "Passenger added successfully.";
        $msgType = "success";
    }
}


$result = $conn->query("
    SELECT ts.*, u.name AS driver_name 
    FROM train_schedule ts
    LEFT JOIN smscampaigner_users u ON ts.driver_id = u.id
    WHERE ts.id = $id
");
if ($result->num_rows === 0) {
    header("Location: train_schedule.php");
    exit;
}
$schedule = $result->fetch_assoc();


$passengers = $conn->query("
    SELECT u.name, u.email, u.phone, u.address
    FROM smscampaigner_train_passengers tp
    JOIN smscampaigner_users u ON tp.user_id = u.id
    WHERE tp.train_id = $id
    ORDER BY u.name ASC
");


$users = $conn->query("SELECT id, name FROM smsCampaigner_users WHERE role = 'passenger' ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>


    <nav class="navbar">
        <div class="navbar-brand">
            <span class="brand-dot"></span>
            Train Management System
        </div>
        <div class="navbar-links">
            <a href="train_schedule.php">Schedules</a>
        </div>
    </nav>

    <div class="page-wrapper">

        <a href="train_schedule.php" class="back-link">← Back to Schedules</a>

        <div class="page-header">
            <h1>Schedule Details</h1>
            <p>Viewing schedule #<?= $id ?></p>
        </div>


        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-head">
                <div class="card-head-title">Schedule Information</div>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Date</div>
                    <div class="info-value"><?= htmlspecialchars($schedule['date']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Start Time</div>
                    <div class="info-value"><?= htmlspecialchars($schedule['start_time']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">End Time</div>
                    <div class="info-value"><?= htmlspecialchars($schedule['end_time']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">From</div>
                    <div class="info-value"><?= htmlspecialchars($schedule['starting_station']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Destination</div>
                    <div class="info-value"><?= htmlspecialchars($schedule['destination']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Driver</div>
                    <div class="info-value"><?= htmlspecialchars($schedule['driver_name'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-head">
                <div class="card-head-title">Passenger List</div>
                <button class="btn btn-primary btn-sm" onclick="openModal()">Add Passenger</button>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($p = $passengers->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= htmlspecialchars($p['phone']) ?></td>
                                <td><?= htmlspecialchars($p['address']) ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($passengers->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="no-data">No passengers added yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <div class="modal-overlay" id="passengerModal">
        <div class="modal-box">
            <div class="modal-title">Add Passenger</div>
            <form method="POST">
                <div class="form-group">
                    <label>Select User</label>
                    <select name="user_id" required>
                        <option value="">Select a user...</option>
                        <?php while ($u = $users->fetch_assoc()): ?>
                            <option value="<?= $u['id'] ?>">
                                <?= htmlspecialchars($u['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_passenger" class="btn btn-primary">Add Passenger</button>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>

</body>

</html>