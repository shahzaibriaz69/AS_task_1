<?php require("./db.php");

if (isset($_POST['create_package'])) {
    $actionId = escape($_POST['actionId']);

    $date = escape($_POST['date']);
    $start_time = escape($_POST['start_time']);
    $end_time = escape($_POST['end_time']);
    $starting_station = escape($_POST['starting_station']);
    $destination = escape($_POST['destination']);
    $driver_id = escape($_POST['driver_id']);

    if ($actionId == "") {
        $id = generateRandomString();
        $actionId = $id;
        $query = "insert into smsCampaigner_train_schedule set id='$id' , date='$date', start_time='$start_time', end_time='$end_time', starting_station='$starting_station', destination='$destination', driver_id='$driver_id', timeAdded='$timeAdded', userId='$session_userId' ";
    } else {
        $query = "update smsCampaigner_train_schedule set id='$actionId' , date='$date', start_time='$start_time', end_time='$end_time', starting_station='$starting_station', destination='$destination', driver_id='$driver_id' where id='$actionId'";
    }
    runQuery($query);

    header("Location: ?" . generateUrlParams_return(["m" => "Data was saved successfully!", "type" => "success"]));
    exit();
}

if (isset($_GET['delete-record'])) {
    $id = escape($_GET['delete-record']);
    $query = "delete from smsCampaigner_train_schedule where id='$id'";
    runQuery($query);
}


?>
<!DOCTYPE html>


<html lang="en">

<head>
    <? require("./includes/views/head.php") ?>
</head>


<body class="<? echo $g_body_class ?>">
    <? require("./includes/views/header.php") ?>

    <div class="kt-grid kt-grid--hor kt-grid--root">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                <? require("./includes/views/topmenu.php") ?>
                <? require("./includes/views/leftmenu.php") ?>


                <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch"
                    id="kt_body">
                    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">


                        <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                            <? if (isset($_GET['m'])) { ?>
                                <div class="alert alert-info"><? echo $_GET['m'] ?></div><? } ?>

                            <div class="kt-portlet kt-portlet--mobile">
                                <div class="kt-portlet__head kt-portlet__head--lg">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">Train schedule</h3>
                                    </div>
                                    <div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">
                                                <a href="#" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#create_record_modal">
                                                    <i class="fa fa-plus"></i>New Record
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <form action="" method="post">
                                        <table
                                            class="table table-striped- table-bordered table-hover table-checkable add-search">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Start time</th>
                                                    <th>End time</th>
                                                    <th>Starting station</th>
                                                    <th>Destination</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $query = "select * from smsCampaigner_train_schedule t order by t.timeAdded desc";
                                                $results = getAll($con, $query);
                                                foreach ($results as $row) { ?>
                                                    <tr>
                                                        <td><?php echo $row['date'] ?></td>
                                                        <td><?php echo $row['start_time'] ?></td>
                                                        <td><?php echo $row['end_time'] ?></td>
                                                        <td><?php echo $row['starting_station'] ?></td>
                                                        <td><?php echo $row['destination'] ?></td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="#" class="btn btn-warning" data-toggle="modal"
                                                                    data-target="#create_record_modal"
                                                                    data-mydata='<?php echo htmlspecialchars(json_encode($row, JSON_UNESCAPED_UNICODE)); ?>'>Edit</a>
                                                                <a href="#" class="btn btn-danger" data-toggle="modal"
                                                                    data-target="#delete_record"
                                                                    data-url="?<? echo generateUrlParams() ?>delete-record=<?php echo $row['id'] ?>">Delete</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <? require("./includes/views/footer.php") ?>
            </div>
        </div>
    </div>
    <? require("./includes/views/footerjs.php") ?>
</body>

<!-- end::Body -->

<div class="modal fade" id="create_record_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelTitle">Insert</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">

                <form class="kt-form" action="?<? echo generateUrlParams() ?>" method="Post"
                    enctype="multipart/form-data">
                    <div class="kt-portlet__body">
                        <!-- MODAL FIELD CODE-->

                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Start time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>End time</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Starting station</label>
                            <input type="text" name="starting_station" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Destination</label>
                            <input type="text" name="destination" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Select Driver</label>
                            <select name="driver_id" class="form-control" required>
                                <option value="">-- Choose Driver --</option>
                                <?php

                                $res = mysqli_query($conn, "SELECT id, name FROM smsCampaigner_users WHERE role='employee'");

                                while ($row = mysqli_fetch_array($res)) {

                                    $selected = ($row['id'] == $driver_id) ? "selected" : "";
                                    echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <input type="text" name="actionId" value="" hidden>

                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <input type="submit" name="create_record_package" value="Submit" class="btn btn-primary">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- MODAL EDIT SCRIPT CODE-->
<script>    $(document).ready(function () {

        $("#create_record_modal").on('show.bs.modal', function (e) {
            var mydata = $(e.relatedTarget).data('mydata');
            console.log("mydata->", mydata);
            $("input[type='checkbox']").prop('checked', false);
            if (mydata != null) {
                $("#modelTitle").html("Update");
                $("input[name='date']").val(mydata['date'])
                $("input[name='start_time']").val(mydata['start_time'])
                $("input[name='end_time']").val(mydata['end_time'])
                $("input[name='starting_station']").val(mydata['starting_station'])
                $("input[name='destination']").val(mydata['destination'])

                $("input[name='actionId']").val(mydata['id'])
            } else {
                $("#modelTitle").html("Insert");
                $("input[name='date']").val("")
                $("input[name='start_time']").val("")
                $("input[name='end_time']").val("")
                $("input[name='starting_station']").val("")
                $("input[name='destination']").val("")

                $("input[name='actionId']").val("")
            }
        });
    })
</script>

</html>