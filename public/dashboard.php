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
<style>
        @keyframes flashRedBlack {
            0% { color: white; background-color: red; }
            50% { color: white; background-color: black; }
            100% { color: black; background-color: red; }
        }

        #driversData tr.flash-red-black {
            animation: flashRedBlack 1s infinite !important;
        }

        #vehiclesData tr.showYellow{
            background-color : yellow;
        }
</style>
<body>

    <?php include_once './assets/components/_sidebar.php' ?>

    <main class="dashboard-main">

        <?php include_once './assets/components/_navbar.php' ?>



        <div class="dashboard-main-body">

            <div class="row gy-2">

                <div class="col-9 p-0">
                    <div class="row gy-2">
                        <!-- Drivers Table start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0 table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Drivers</th>
                                                    <th>Clock off</th>
                                                    <th>Starts In</th>
                                                    <th>Job</th>
                                                    <th>Allocated / Driving Bus</th>
                                                    <th>Callable</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="driversData">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Drivers Table end -->

                        <!-- Jobs Table start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0 table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>Run</th>
                                                    <th>Day</th>
                                                    <th>Start</th>
                                                    <th>Finish</th>
                                                    <th>Hours</th>
                                                    <th>Driver</th>
                                                    <th>Bus</th>
                                                    <th>Status</th>
                                                    <th>Driver Split?</th>
                                                    <th>Starts in</th>
                                                </tr>
                                            </thead>
                                            <tbody id="jobsTable">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Jobs Table end -->
                    </div>
                </div>
                <div class="col-3 p-0 px-1">
                <div class="row gy-2">
                        <!-- Vehicles Table start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0 table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Rego</th>
                                                    <th>Status</th>
                                                    <th>ETR / ETO</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vehiclesData">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Vehicles Table end -->

                        <!-- Callable Table start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0 table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Drivers</th>
                                                    <th>Rest</th>
                                                    <th>Phone</th>
                                                </tr>
                                            </thead>
                                            <tbody id="callableData">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Callable Table end -->
                    </div>
                </div>



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
<script src="assets/js/dashboard.js"></script>