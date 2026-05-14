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

$users = $conn->query("SELECT id, name FROM smsCampaigner_users WHERE role = 'passenger' ORDER BY name ASC");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>View Train Schedule</title>
</head>

<body>

    <div class="container">
        <a href="train_schedule.php" class="back-link">← Back to Schedules</a>

        <div class="page-title">
            <span><i class="fa-solid fa-train"></i></span> Train Schedule Details
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>">
                <?= $msgType === 'success' ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-regular fa-circle-xmark"></i>' ?>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <span><span class="header-icon"><i class="fa-regular fa-file"></i></span> Schedule Information</span>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                            viewBox="0 0 1024 1024">
                            <path d="M0 0h1024v1024H0z" fill="none" />
                            <path fill="currentColor"
                                d="m960 95.888l-256.224.001V32.113c0-17.68-14.32-32-32-32s-32 14.32-32 32v63.76h-256v-63.76c0-17.68-14.32-32-32-32s-32 14.32-32 32v63.76H64c-35.344 0-64 28.656-64 64v800c0 35.343 28.656 64 64 64h896c35.344 0 64-28.657 64-64v-800c0-35.329-28.656-63.985-64-63.985m0 863.985H64v-800h255.776v32.24c0 17.679 14.32 32 32 32s32-14.321 32-32v-32.224h256v32.24c0 17.68 14.32 32 32 32s32-14.32 32-32v-32.24H960zM736 511.888h64c17.664 0 32-14.336 32-32v-64c0-17.664-14.336-32-32-32h-64c-17.664 0-32 14.336-32 32v64c0 17.664 14.336 32 32 32m0 255.984h64c17.664 0 32-14.32 32-32v-64c0-17.664-14.336-32-32-32h-64c-17.664 0-32 14.336-32 32v64c0 17.696 14.336 32 32 32m-192-128h-64c-17.664 0-32 14.336-32 32v64c0 17.68 14.336 32 32 32h64c17.664 0 32-14.32 32-32v-64c0-17.648-14.336-32-32-32m0-255.984h-64c-17.664 0-32 14.336-32 32v64c0 17.664 14.336 32 32 32h64c17.664 0 32-14.336 32-32v-64c0-17.68-14.336-32-32-32m-256 0h-64c-17.664 0-32 14.336-32 32v64c0 17.664 14.336 32 32 32h64c17.664 0 32-14.336 32-32v-64c0-17.68-14.336-32-32-32m0 255.984h-64c-17.664 0-32 14.336-32 32v64c0 17.68 14.336 32 32 32h64c17.664 0 32-14.32 32-32v-64c0-17.648-14.336-32-32-32" />
                        </svg>
                        Date</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['date']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fa-regular fa-clock"></i> Start Time</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['start_time']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fa-regular fa-clock"></i> End Time</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['end_time']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fa-solid fa-location-pin"></i> From</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['starting_station']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fa-solid fa-arrows-turn-to-dots"></i> Destination</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['destination']) ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fa-regular fa-circle-user"></i> Driver</div>
                    <div class="info-value">
                        <?= htmlspecialchars($schedule['driver_name'] ?? 'N/A') ?>
                    </div>
                </div>
            </div>

        </div>


        <div class="card">
            <div class="card-header">
                <span><span class="header-icon"><i class="fa-solid fa-people-group"></i></span> Passenger List</span>
                <button class="btn btn-sm btn-success" onclick="openModal()"><i class="fa-solid fa-plus"></i> Add
                    Passenger</button>
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
                <div class="modal-title"><i class="fa-solid fa-person"></i> Add Passenger</div>

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
    <script src="script.js"></script>
</body>

</html>