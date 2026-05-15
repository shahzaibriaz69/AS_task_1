<?php
require("./db.php");


$id = $_GET['id'];

$res = mysqli_query($conn, "SELECT * FROM smsCampaigner_train_schedule WHERE id='$id'");
$train = mysqli_fetch_assoc($res);

if (isset($_POST['add_passenger'])) {
    $u_id = $_POST['user_id'];
    mysqli_query($conn, "INSERT INTO smsCampaigner_train_passengers (train_id, user_id) VALUES ('$id', '$u_id')");
    echo "<script>window.location.href='view_train_schedule.php?id=$id';</script>";
}
?>

<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon2-information"></i>
            </span>
            <h3 class="kt-portlet__head-title">
                Train Schedule Details
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <button type="button" class="btn btn-brand btn-elevate btn-icon-sm" data-toggle="modal"
                    data-target="#addPassengerModal">
                    <i class="la la-plus"></i> Add Passenger
                </button>
            </div>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="row mb-4">
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Starting Station</th>
                        <td><?php echo $train['starting_station']; ?></td>
                    </tr>
                    <tr>
                        <th>Destination</th>
                        <td><?php echo $train['destination']; ?></td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td><?php echo $train['date']; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Departure Time</th>
                        <td><?php echo $train['start_time']; ?></td>
                    </tr>
                    <tr>
                        <th>Arrival Time</th>
                        <td><?php echo $train['end_time']; ?></td>
                    </tr>
                    <tr>
                        <th>Driver ID</th>
                        <td><?php echo $train['driver_id']; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <h3>Passengers List</h3>
        <div class="table-responsive">
            <table class="table table-striped- table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $plist = mysqli_query($conn, "SELECT u.* FROM smsCampaigner_users u JOIN smsCampaigner_train_passengers p ON u.id = p.user_id WHERE p.train_id = '$id'");
                    while ($p = mysqli_fetch_assoc($plist)) {
                        echo "<tr>
                            <td>{$p['name']}</td>
                            <td>{$p['email']}</td>
                            <td>{$p['phone']}</td>
                            <td>{$p['address']}</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addPassengerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Passenger to Schedule</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select User</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">-- Choose User --</option>
                        <?php
                        $users = mysqli_query($conn, "SELECT id, name FROM smsCampaigner_users");
                        while ($u = mysqli_fetch_assoc($users)) {
                            echo "<option value='{$u['id']}'>{$u['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_passenger" class="btn btn-primary">Save Passenger</button>
            </div>
        </form>
    </div>
</div>