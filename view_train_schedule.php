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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <title>View Train Schedule</title>
</head>

<body>

    <div class="container">
        <a href="train_schedule.php" class="back-link">← Back to Schedules</a>

        <div class="page-title">
            <span>🚆</span> Train Schedule Details
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>">
                <?= $msgType === 'success' ? '✅' : '❌' ?>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <span><span class="header-icon">📋</span> Schedule Information</span>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">📅 Date</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['date']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">🕐 Start Time</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['start_time']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">🕔 End Time</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['end_time']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">📍 From</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['starting_station']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">🏁 Destination</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['destination']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">👤 Driver</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['driver_name'] ?? 'N/A') ?>
                    </div>
                </div>
            </div>

        </div>


        <div class="card">
            <div class="card-header">
                <span><span class="header-icon">👥</span> Passenger List</span>
                <button class="btn btn-sm btn-success" onclick="openModal()">➕ Add Passenger</button>
            </div>

            <div class="table-wrapper">
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
                                <td>
                                    <?= $i++ ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['name']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['email']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['phone']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['address']) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($passengers->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="no-data">No passengers yet. Click "Add Passenger" to add one.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="modal-overlay" id="passengerModal">
            <div class="modal-box">
                <div class="modal-title">👤 Add Passenger</div>

                <form method="POST">
                    <div class="form-group">
                        <label>Select Passenger</label>
                        <select name="user_id" required>
                            <option value="">-- Select User --</option>
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

    </div>

</body>

</html>