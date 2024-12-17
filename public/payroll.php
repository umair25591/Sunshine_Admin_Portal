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


 <!-- Loader -->
 <div class="modal fade" id="payrollLoader" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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


 <div class="modal fade" id="alertModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
            <div>
                <div class="alert alert-success bg-success-100 text-success-600 border-success-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between" role="alert">Driver Edits Lock Successfully</div>
            </div>
            <div class="d-flex justify-content-end">
            </div>
        </div>
      </div>
    </div>
  </div>

    <?php include_once './assets/components/_sidebar.php' ?>

    <main class="dashboard-main">

        <?php include_once './assets/components/_navbar.php' ?>



        <div class="dashboard-main-body">
            <div class="d-flex justify-content-between">

                <div class="weekDisplay d-flex">
                    <div class="backwordIcon d-flex align-items-center"><iconify-icon icon="ic:round-play-arrow"></iconify-icon></div>
                    <h5 class="showDate m-0 d-flex align-items-center">Monday 20 November - Sunday 26 November</h5>
                    <div class="forwordIcon align-items-center"><iconify-icon icon="ic:round-play-arrow"></iconify-icon></div>
                </div>

                <div class="addJobButton d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 hide" id="lockDriverEdits">Lock Driver Edits</button>
                    <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11" id="ExportData">Export Time Summary</button>
                </div>

            </div>

            <div class="showAlert"></div>
            


            <div class="row gy-2 mt-3">
                <div class="col-12 p-0">
                    <div class="row gy-2">
                        <!-- Payroll Table start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table basic-border-table mb-0 table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Drivers</th>
                                                    <th>Monday</th>
                                                    <th>Tuesday</th>
                                                    <th>Wednesday</th>
                                                    <th>Thursday</th>
                                                    <th>Friday</th>
                                                    <th>Saturday</th>
                                                    <th>Sunday</th>
                                                    <th>Ordinary</th>
                                                    <th>Sat</th>
                                                    <th>Sun</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="shiftsTableBody">
                                                <tr>
                                                    <td colspan="12">Loading...</td>
                                                </tr>
                                                <!-- <tr>
                                                    <td class="font-14">William Williams</td>
                                                    <td>
                                                        <p class="m-0 font-14">7.5H - Shift</p>
                                                        <p class="m-0 font-14">0.5H - Breaks</p>
                                                        <p class="m-0 font-14">7.0H - Paid</p>
                                                    </td>
                                                    <td>
                                                        <p class="m-0 font-14">7.5H - Shift</p>
                                                        <p class="m-0 font-14">0.5H - Breaks</p>
                                                        <p class="m-0 font-14">7.0H - Paid</p>
                                                    </td>
                                                    <td>
                                                        <p class="m-0 font-14">7.5H - Shift</p>
                                                        <p class="m-0 font-14">0.5H - Breaks</p>
                                                        <p class="m-0 font-14">7.0H - Paid</p>
                                                    </td>
                                                    <td>
                                                        <p class="m-0 font-14">7.5H - Shift</p>
                                                        <p class="m-0 font-14">0.5H - Breaks</p>
                                                        <p class="m-0 font-14">7.0H - Paid</p>
                                                    </td>
                                                    <td>
                                                        <p class="m-0 font-14">7.5H - Shift</p>
                                                        <p class="m-0 font-14">0.5H - Breaks</p>
                                                        <p class="m-0 font-14">7.0H - Paid</p>
                                                    </td>
                                                    <td>
                                                        <p class="m-0 font-14">7.5H - Shift</p>
                                                        <p class="m-0 font-14">0.5H - Breaks</p>
                                                        <p class="m-0 font-14">7.0H - Paid</p>
                                                    </td>
                                                    <td>
                                                        <p class="m-0 font-14">7.5H - Shift</p>
                                                        <p class="m-0 font-14">0.5H - Breaks</p>
                                                        <p class="m-0 font-14">7.0H - Paid</p>
                                                    </td>
                                                    <td class="font-14">22.5</td>
                                                    <td class="font-14">4</td>
                                                    <td class="font-14">0</td>
                                                    <td>
                                                    <button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm">Review</button>
                                                    <button type="button" class="btn btn-success-600 radius-8 px-14 py-6 text-sm">Accept</button>
                                                    <button type="button" class="btn btn-danger-600 radius-8 px-14 py-6 text-sm">Reject</button>

                                                    </td>
                                                </tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Payroll Table end -->
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
<script src="assets/js/payroll.js"></script>