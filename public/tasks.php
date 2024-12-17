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
                    <a type="button" href="addTask.php" class="btn btn-primary-600 radius-8 px-20 py-11">ADD TASK</a>
                </div>

                <div class="jobSwitch">
                    <div class="form-switch switch-primary d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="showCompleted" checked>
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="showCompleted">Show Completed</label>
                    </div>
                    <div class="form-switch switch-primary mt-3 d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="todayOnly">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="todayOnly">Today Only</label>
                    </div>
                </div>

            </div>

            <div class="row gy-2 mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">


                                <table class="table basic-border-table mb-0 table-hover">
                                    <thead>
                                        <tr>
                                            <th>Task#</th>
                                            <th>Task</th>
                                            <th>Completed By</th>
                                            <th>Assign To</th>
                                            <th>Time</th>
                                            <th>Recurring</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="TasksData">
                                    
                                    <thead class="completed">
                                        <tr>
                                            <th class="border_bottom" colspan="7">Recently Completed</th>
                                        </tr>
                                    </thead>
                                    <tbody class="completed" id="recentlyCompletedTable">
                                        <tr>
                                            <td>Loading...</td>
                                        </tr>
                                    </tbody>

                                    <thead>
                                        <tr>
                                            <th class="border_bottom padding_top_30" colspan="7">Pending</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pendingTable">
                                        <tr>
                                            <td>Loading...</td>
                                        </tr>
                                    </tbody>

                                    <thead>
                                        <tr>
                                            <th class="border_bottom padding_top_30" colspan="7">Tomorrow</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tomorrowTable">
                                        <tr>
                                            <td>Loading...</td>
                                        </tr>
                                    </tbody>

                                    <thead>
                                        <tr>
                                            <th class="border_bottom padding_top_30" colspan="7">Recurring</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recurringTable">
                                        <tr>
                                            <td>Loading...</td>
                                        </tr>
                                    </tbody>  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" value="" hidden id="recurringIdInput">
        Are you sure ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary yesButton">Yes</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal End -->

<!-- Modal -->
<div class="modal fade" id="createRecurring" tabindex="-1" aria-labelledby="createRecurringLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createRecurringLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" value="" hidden id="yardDutyIdInput">
        <div class="row align-items-center justify-content-center">
            <p class="fs-5 text-center">Select Days</p>
            <div class="col-sm-10 d-flex gap-10 justify-content-center recurringDaysBox">
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary yesButton">Create</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal End -->

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
<script src="assets/js/tasks.js"></script>