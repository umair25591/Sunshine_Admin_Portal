<?php
require_once '../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){

    $getEmplyeeType = "SELECT * FROM employeeTypes;";
    $runGetEmplyeeType = mysqli_query($conn, $getEmplyeeType);
    $numRowsEmplyeeType = mysqli_num_rows($runGetEmplyeeType);

    $getPayStructure = "SELECT * FROM payStructure;";
    $runPayStructure = mysqli_query($conn, $getPayStructure);
    $numRowPayStructure = mysqli_num_rows($runPayStructure);

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


  <!-- Loader -->
  <div class="modal fade" id="addDriverPageLoader" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addDriverPageLoaderLabel" aria-hidden="true">
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
            <div class="row gy-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Add Driver</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">First Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="firstName" id="firstName" class="form-control" placeholder="Enter First Name">
                                </div>
                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Last Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="lastName" id="lastName" class="form-control" placeholder="Enter Last Name">
                                </div>
                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Mobile</label>
                                <div class="col-sm-10">
                                <input type="number" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile" maxlength="10" oninput="this.value = this.value.slice(0, 10);" pattern="\d{10}" inputmode="numeric">
                                </div>
                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Employee Type</label>

                                <div class="col-sm-10 d-flex align-items-center flex-wrap gap-24">
                                <?php
                                    if($numRowsEmplyeeType > 0){
                                        while ($resultEmployeeType = mysqli_fetch_assoc($runGetEmplyeeType)) {
                                            echo '<div class="bg-primary-50 px-20 py-12 radius-8">
                                                    <span class="form-check checked-primary d-flex align-items-center gap-2">
                                                        <input class="form-check-input" type="radio" name="employeeType" id="employeeType'. $resultEmployeeType['id'] .'" value="'. $resultEmployeeType['id'] .'">
                                                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="employeeType'. $resultEmployeeType['id'] .'">'. $resultEmployeeType['name'] .'</label>
                                                    </span>
                                                </div>';
                                        }
                                    }
                                    ?>
                                </div>

                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Pay Structure</label>

                                <div class="col-sm-10 d-flex align-items-center flex-wrap gap-24">
                                    <?php
                                    if($numRowPayStructure > 0){
                                        while ($resultPayStructure = mysqli_fetch_assoc($runPayStructure)) {
                                            echo '<div class="bg-primary-50 px-20 py-12 radius-8">
                                                    <span class="form-check checked-primary d-flex align-items-center gap-2">
                                                        <input class="form-check-input" type="radio" name="payStructure" id="payStructure'. $resultPayStructure['id'] .'" value="'. $resultPayStructure['id'] .'">
                                                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="payStructure'. $resultPayStructure['id'] .'">'. $resultPayStructure['name'] .'</label>
                                                    </span>
                                                </div>';
                                        }
                                    }
                                    ?>                              
                                </div>

                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Key Pay Unique ID</label>
                                <div class="col-sm-10">
                                    <input type="text" name="uniqueId" id="uniqueId" class="form-control" placeholder="Enter Unique ID">
                                </div>
                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Pin</label>
                                <div class="col-sm-10">
                                    <input type="number" name="pin" id="pin" class="form-control" placeholder="Enter Pin">
                                </div>
                            </div>
                            <div class="row mb-24 gy-3 align-items-center" id="showAlert">
                                
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-light-600 text-dark mx-10" id="cancelButton">Cancel</button>
                                    <button type="submit" class="btn btn-primary-600" id="submitButton">Save</button>
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
<script src="assets/js/addDriver.js"></script>