<?php
require_once '../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){

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

<body>

    <?php include_once './assets/components/_sidebar.php' ?>

    <main class="dashboard-main">

        <?php include_once './assets/components/_navbar.php' ?>



        <div class="dashboard-main-body">
            <div class="d-flex justify-content-between">

                <div class="addJobButton d-flex align-items-center">
                    <a type="button" href="addJob.php" class="btn btn-primary-600 radius-8 px-20 py-11">ADD JOB</a>
                </div>

                <div class="jobSwitch">
                    <div class="form-switch switch-primary d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="showCompleted">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="showCompleted">Show Completed</label>
                    </div>
                    <div class="form-switch switch-primary mt-3 d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="todayOnly">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="todayOnly">Today Only</label>
                    </div>
                    <div class="form-switch switch-primary mt-3 d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="unallocatedDriverOnly">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="unallocatedDriverOnly">Unallocated Driver Only</label>
                    </div>
                    <div class="form-switch switch-primary mt-3 d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="unallocatedVehicleOnly">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="unallocatedVehicleOnly">Unallocated Vehicle Only</label>
                    </div>
                </div>

            </div>

            <div class="row gy-2 mt-2">
                <!-- Job Table start -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table basic-border-table mb-0 table-hover">
                                    <thead>
                                        <tr>
                                            <th>Job ID</th>
                                            <th>Customer</th>
                                            <th>Run</th>
                                            <th>Day</th>
                                            <th>Start</th>
                                            <th>Finish</th>
                                            <th>Hours</th>
                                            <th>Vehicle</th>
                                            <th>Driver</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="jobsData">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Job Table end -->
            </div>
        </div>
    </main>

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

<!-- main js -->
<script src="assets/js/app.js"></script>
<script src="assets/js/job.js"></script>