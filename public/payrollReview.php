<?php
require_once '../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_GET['driverId'])){
        $driverId = $_GET['driverId'];
        $getDriverName = "SELECT firstName, lastName FROM drivers WHERE id = '$driverId';";
        $runGetDriverName = mysqli_query($conn, $getDriverName);
        $resultGetDriver = mysqli_fetch_assoc($runGetDriverName);
    }
}
else{
  header("Location: index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operations Portal</title>
    <link rel="icon" type="image/png" href="assets/images/logo-icon.png" sizes="16x16">
    <!-- remix icon font css  -->
    <link rel="stylesheet" href="assets/css/remixicon.css">
    <!-- BootStrap css -->
    <link rel="stylesheet" href="assets/css/lib/bootstrap.min.css">
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="assets/css/lib/apexcharts.css">
    <!-- Data Table css -->
    <link rel="stylesheet" href="assets/css/lib/dataTables.min.css">
    <!-- Text Editor css -->
    <link rel="stylesheet" href="assets/css/lib/editor-katex.min.css">
    <link rel="stylesheet" href="assets/css/lib/editor.atom-one-dark.min.css">
    <link rel="stylesheet" href="assets/css/lib/editor.quill.snow.css">
    <!-- Date picker css -->
    <link rel="stylesheet" href="assets/css/lib/flatpickr.min.css">
    <!-- Calendar css -->
    <link rel="stylesheet" href="assets/css/lib/full-calendar.css">
    <!-- Vector Map css -->
    <link rel="stylesheet" href="assets/css/lib/jquery-jvectormap-2.0.5.css">
    <!-- Popup css -->
    <link rel="stylesheet" href="assets/css/lib/magnific-popup.css">

    <link rel="stylesheet" href="assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">

    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">

    <!-- Slick Slider css -->
    <link rel="stylesheet" href="assets/css/lib/slick.css">
    <!-- prism css -->
    <link rel="stylesheet" href="assets/css/lib/prism.css">
    <!-- file upload css -->
    <link rel="stylesheet" href="assets/css/lib/file-upload.css">

    <link rel="stylesheet" href="assets/css/lib/audioplayer.css">
    <!-- main css -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<style>
   .card-body {
    display: flex;
    flex-direction: column;
}

.mainBox {
    display: flex;
    flex-direction: column;
}

.header, .data-box {
    display: flex;
    width: 100%;
}

.header-item, .data-item {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center; /* Center the text for better appearance */
}

.header-item {
    font-weight: bold;
    background-color: #f4f4f4;
}

.data-box {
    flex-direction: column; /* Adjust to column to fit the day heading and data rows properly */
}

.day-heading {
    font-weight: bold;
    padding: 10px;
    border: 1px solid #ddd;
    background-color: #f4f4f4; /* Match the background color with the header for consistency */
}

.data-rows {
    display: flex;
    flex-direction: column; /* Stack rows vertically */
}

.data-row {
    display: flex;
    width: 100%;
}

.data-item {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    box-sizing: border-box;
}

.bootstrap-datetimepicker-widget {
    font-size: 18px;
}
.bootstrap-datetimepicker-widget {
    width: 100% !important; /* Adjust to your desired width */
}


</style>
<body>

  <!-- Loader -->
  <div class="modal fade" id="payrollReviewLoader" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
            <div class="d-flex justify-content-center">
                <div class="loader mt-3 mb-3"></div>
            </div>
            <div class="d-flex justify-content-end">
            </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Loader End -->


    <?php include_once './assets/components/_sidebar.php' ?>

    <main class="dashboard-main">

        <?php include_once './assets/components/_navbar.php' ?>



        <div class="dashboard-main-body">
            <div class="d-flex justify-content-between align-items-center">

                <div class="weekDisplay d-flex align-items-center">
                    <div class="backwordIcon d-flex align-items-center"><iconify-icon icon="ic:round-play-arrow"></iconify-icon></div>
                    <h5 class="showDate m-0 d-flex align-items-center">Monday 20 November - Sunday 26 November</h5>
                    <div class="forwordIcon align-items-center"><iconify-icon icon="ic:round-play-arrow"></iconify-icon></div>
                </div>

                <h5 class="m-0 fs-1"><?php echo $resultGetDriver['firstName'] . ' ' . $resultGetDriver['lastName'] ?></h5>

                <div class="addJobButton d-flex align-items-center gap-3">
                    <a href="payroll.php" type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Cancel</a>
                    <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11" id="saveButton">Save</button>
                </div>

            </div>

<!-- 
            <div class="row gy-2 mt-3">
                <div class="col-12 p-0">
                    <div class="row gy-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-0 border border-danger">
                                    <div class="mainBox">
                                        <div class="header d-flex">
                                            <div class="header-item">Day</div>
                                            <div class="header-item">Clock In</div>
                                            <div class="header-item">Break Start</div>
                                            <div class="header-item">Break End</div>
                                            <div class="header-item">Clock Out</div>
                                            <div class="header-item">Vehicle</div>
                                            <div class="header-item">Job</div>
                                            <div class="header-item">Hours</div>
                                        </div>
                                        <div class="data-box d-flex">
                                            <div class="day-heading">Monday</div>
                                            <div class="data-rows">
                                                <div class="data-row">
                                                    <div class="data-item">Clock In</div>
                                                    <div class="data-item">Break Start</div>
                                                    <div class="data-item">Break End</div>
                                                    <div class="data-item">Clock Out</div>
                                                    <div class="data-item">Vehicle</div>
                                                    <div class="data-item">Job</div>
                                                    <div class="data-item">Hours</div>
                                                </div>
                                                <div class="data-row">
                                                    <div class="data-item">Clock In</div>
                                                    <div class="data-item">Break Start</div>
                                                    <div class="data-item">Break End</div>
                                                    <div class="data-item">Clock Out</div>
                                                    <div class="data-item">Vehicle</div>
                                                    <div class="data-item">Job</div>
                                                    <div class="data-item">Hours</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->




            <div class="row gy-2 mt-3">
                <div class="col-12 p-0">
                    <div class="row gy-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0 table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Day</th>
                                                    <th>Clock In</th>
                                                    <th>Break Start</th>
                                                    <th>Break End</th>
                                                    <th>Clock Out</th>
                                                    <th>Vehicle</th>
                                                    <th>Job</th>
                                                    <th>Hours</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="driverDataTable">
                                                <tr>
                                                    <td colspan="8">Loading...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row showEditedMainBox hide gy-2 mt-3">
                <div class="col-12 p-0">
                    <div class="row gy-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header" style="padding: 10px 15px;">
                                    <h6 class="m-0">Edited</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0 table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Clock In</th>
                                                    <th>Break Start</th>
                                                    <th>Break End</th>
                                                    <th>Clock Out</th>
                                                    <th>Vehicle</th>
                                                    <th>Job</th>
                                                    <th>Hours</th>
                                                </tr>
                                            </thead>
                                            <tbody id="showEditedData">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>


    <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="editShiftId" name="shift_id">
                    <input type="hidden" id="vehicle" name="job">
                    <input type="hidden" id="job" name="vehicle">
                    <input type="hidden" id="hours" name="hours">
                    <div class="mb-3">
                        <label for="editClockIn" class="form-label">Clock In</label>
                        <div class="input-group date" id="clockIn" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" id="editClockIn" data-target="#clockIn"/>
                            <div class="input-group-append" data-target="#clockIn" data-toggle="datetimepicker">
                                <div class="input-group-text" style="font-size: 30px;"><i class="fa-solid fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                    <div id="breakContainer">
                        <!-- Combined start and finish breaks will be inserted here -->
                    </div>
                    <div class="mb-3">
                        <label for="editClockOut" class="form-label">Clock Out</label>
                        <div class="input-group date" id="clockOut" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" id="editClockOut" data-target="#clockOut"/>
                            <div class="input-group-append" data-target="#clockOut" data-toggle="datetimepicker">
                                <div class="input-group-text" style="font-size: 30px;"><i class="fa-solid fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editForm">Save</button>
            </div>
        </div>
    </div>
</div>




</body>

</html>
<!-- jQuery library js -->
<script src="assets/js/lib/jquery-3.7.1.min.js"></script>
<!-- Bootstrap js -->
<script src="assets/js/lib/bootstrap.bundle.min.js"></script>
<!-- Data Table js -->
<script src="assets/js/lib/dataTables.min.js"></script>
<!-- Iconify Font js -->
<script src="assets/js/lib/iconify-icon.min.js"></script>
<!-- jQuery UI js -->
<script src="assets/js/lib/jquery-ui.min.js"></script>
<!-- Vector Map js -->
<script src="assets/js/lib/jquery-jvectormap-2.0.5.min.js"></script>
<script src="assets/js/lib/jquery-jvectormap-world-mill-en.js"></script>
<!-- Popup js -->
<script src="assets/js/lib/magnifc-popup.min.js"></script>
<!-- Slick Slider js -->
<script src="assets/js/lib/slick.min.js"></script>
<!-- prism js -->
<script src="assets/js/lib/prism.js"></script>
<!-- file upload js -->
<script src="assets/js/lib/file-upload.js"></script>
<!-- audioplayer -->
<script src="assets/js/lib/audioplayer.js"></script>
<script src="assets/js/moment.min.js"></script>

<script src="assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>

<!-- main js -->
<script src="assets/js/app.js"></script>
<script src="assets/js/payrollReview.js"></script>