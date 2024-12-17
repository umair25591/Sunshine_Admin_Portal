<?php
require_once '../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){

    $getDrivers = "SELECT * FROM drivers WHERE status = 'Active' AND isDeleted = 0;";
    $runDrivers = mysqli_query($conn, $getDrivers);
    $rowGetDriver  = mysqli_num_rows($runDrivers);

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
            <div class="row gy-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Add Task</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Task</label>
                                <div class="col-sm-10">
                                    <input type="text" name="taskName" class="form-control" placeholder="Enter Task Name" id="taskName">
                                </div>
                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">When</label>
                                <div class="col-sm-10 d-flex align-items-center flex-wrap gap-28">
                                    <div class="form-check checked-primary d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="radio" name="when" id="now" value="now">
                                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="now">Now</label>
                                    </div>
                                    <div class="form-check checked-primary d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="radio" name="when" id="tomorrow" value="tomorrow">
                                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="tomorrow">Tomorrow</label>
                                    </div>
                                    <div class="form-check checked-primary d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="radio" name="when" id="recurring" value="recurring">
                                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="recurring">Recurring</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-24 gy-3 align-items-center recurringBox hide">
                                <label class="form-label mb-0 col-sm-2">Recurring</label>
                                <div class="col-sm-10 d-flex gap-10">
                                    <span>M</span>
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="monday">
                                    <span>T</span>
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="tuesday">
                                    <span>W</span>
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="wednesday">
                                    <span>T</span>
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="thursday">
                                    <span>F</span>
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="friday">
                                    <span>S</span>
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="saturday">
                                    <span>S</span>
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="sunday">
                                </div>
                            </div>

                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Cancel if not completed by 23:59</label>
                                <div class="col-sm-10">
                                <input class="form-check-input border border-neutral-300" type="checkbox" value="cancelIfNotCompletedBy" id="cancelIfNotCompletedBy">
                                </div>
                            </div>

                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Assign to a specific person</label>
                                <div class="col-sm-10 d-flex gap-28 align-items-center">
                                    <input class="form-check-input border border-neutral-300" type="checkbox" value="assignToPerson" id="assignToPerson">
                                    <select class="form-select hide" id="selectPerson">
                                        <option selected>Open this select</option>
                                        <?php
                                        
                                        if($rowGetDriver > 0){
                                            while ($data = mysqli_fetch_assoc($runDrivers)) {
                                                echo '<option value="'. $data['id'] .'">'. $data['firstName'] . ' ' . $data['lastName'] .'</option>';
                                            }
                                        }
                                        
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Priority</label>
                                <div class="col-sm-10 d-flex gap-28 align-items-center">
                                    <select class="form-select" id="selectPriority">
                                        <option value="Normal" selected>Normal</option>
                                        <option value="High">High</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
                            </div>

                            
                            <div class="row mb-24 gy-3 align-items-center" id="showAlert">
                                
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-12 d-flex justify-content-end">
                                    <a href="tasks.php" type="submit" class="btn btn-light-600 text-dark mx-10">Cancel</a>
                                    <button type="submit" class="btn btn-primary-600" id="saveButton">Save</button>
                                </div>
                            </div>
                            
                        </div>
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
<script src="assets/js/addTask.js"></script>