<?php
require_once '../db-connection.php';
session_start();
if (isset($_SESSION['adminId'])) {

    $getVehicle = "SELECT * FROM vehicles WHERE status = 'Active' AND isDeleted = 0;";
    $runGetVehicle = mysqli_query($conn, $getVehicle);
    $numRowsGetVehicle = mysqli_num_rows($runGetVehicle);

    $getDrivers = "SELECT * FROM drivers WHERE status = 'Active' AND isDeleted = 0;";
    $runGetDrivers = mysqli_query($conn, $getDrivers);
    $numRowsGetDrivers = mysqli_num_rows($runGetDrivers);

} else {
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

                <div>
                    <div class="d-flex align-items-center">
                        <select class="form-select" id="vehicleSelect">
                            <option selected value='null'>Filter By Vehicle</option>
                            <?php
                                if($numRowsGetVehicle > 0){
                                    while ($result = mysqli_fetch_assoc($runGetVehicle)) {
                                        echo '<option value="'. $result['id'] .'">'. $result['numberPlate'] .'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <select class="form-select" id="driverSelect">
                            <option selected value='null'>Filter By Driver</option>
                            <?php
                                if($numRowsGetDrivers > 0){
                                    while ($result = mysqli_fetch_assoc($runGetDrivers)) {
                                        echo '<option value="'. $result['id'] .'">'. $result['firstName'].' '. $result['lastName'] .'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="jobSwitch">
                    <div class="form-switch switch-primary d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" id="showCurrentSwitch">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                            for="showCurrentSwitch">Show Current</label>
                    </div>
                    <div class="form-switch switch-primary mt-3 d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" id="showResolvedSwitch">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                            for="showResolvedSwitch">Show Resolved</label>
                    </div>
                    <div class="form-switch switch-primary mt-3 d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" id="showUnresolvableSwitch">
                        <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                            for="showUnresolvableSwitch">Show Unresolvable</label>
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
                                            <th>Defect Number</th>
                                            <th>Vehicle</th>
                                            <th>Date Reported</th>
                                            <th>Reported By</th>
                                            <th>Defect Category</th>
                                            <th>Defect Item</th>
                                            <th>Defect detail</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="defectdata">

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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Action for Defect</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="showAlertMessage"></div>
                    <input type="hidden" id="defectId" name="defectId" value="">
                    <p class="fw-bold">Take action for defect ID: <span id="defectIdDisplay"></span></p>
                    <div class="container-fluid p-0">
                        <div class="d-flex gap-10">
                            <button type="button" class="btn btn-primary" id="addPublicCommentBtn">ADD COMMENT
                                (public)</button>
                            <button type="button" class="btn btn-primary status-action-btn"
                                data-status="Will Fixed Next">WILL FIXED AT NEXT SERVICE INTERVAL</button>
                            <button type="button" class="btn btn-primary status-action-btn"
                                data-status="Not a Fault">NOT A FAULT</button>
                            <button type="button" class="btn btn-primary status-action-btn"
                                data-status="Fixed">FIXED</button>
                            <button type="button" class="btn btn-primary status-action-btn"
                                data-status="Unrepairable">UNREPAIRABLE</button>
                            <button type="button" class="btn btn-primary" id="addPrivateCommentBtn">ADD NOTE
                                (private)</button>
                        </div>
                    </div>
                    <p class="fw-bold mt-16">Comments for defect ID: <span id="defectIdDisplay2"></span></p>
                    <div id="showComments">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Comment (Public) Modal -->
    <div class="modal fade" id="addCommentModal" tabindex="-1" aria-labelledby="addCommentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCommentModalLabel">Add Public Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCommentForm">
                        <input type="hidden" id="commentDefectId" name="defectId" value="">
                        <div class="mb-3">
                            <label for="commentText" class="form-label">Comment</label>
                            <textarea class="form-control" id="commentText" name="commentText" rows="4"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                        <button class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Comment (Public) Modal -->
    <div class="modal fade" id="addPrivateCommentModal" tabindex="-1" aria-labelledby="addPrivateCommentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPrivateCommentModalLabel">Add Private Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPrivateCommentForm">
                        <input type="hidden" id="commentPrivateDefectId" name="defectId" value="">
                        <div class="mb-3">
                            <label for="commentPrivateText" class="form-label">Note</label>
                            <textarea class="form-control" id="commentPrivateText" name="commentText" rows="4"
                                required></textarea>
                        </div>
                        <button type="submit" id="submitPrivateNoteBtn" class="btn btn-primary">Submit Note</button>
                        <button class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </form>
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

<!-- main js -->
<script src="assets/js/app.js"></script>
<script src="assets/js/defect.js"></script>