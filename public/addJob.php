<?php
require_once '../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){

// Query for customers
$getCustomer = "SELECT * FROM customers WHERE status = 'Active';";
$runCustomer = mysqli_query($conn, $getCustomer);
$numRowsGetCustomer = mysqli_num_rows($runCustomer);

// Query for drivers
$getDriver = "SELECT * FROM drivers WHERE status = 'Active' AND isDeleted = 0;";
$runDriver = mysqli_query($conn, $getDriver);
$numRowsGetDriver = mysqli_num_rows($runDriver);

// Query for vehicles
$getVehicle = "SELECT * FROM vehicles WHERE status = 'Active' AND isDeleted = 0;";
$runVehicle = mysqli_query($conn, $getVehicle);
$numRowsGetVehicle = mysqli_num_rows($runVehicle);


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
    <div class="modal fade" id="addDriverPageLoader" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addDriverPageLoaderLabel" aria-hidden="true">
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
                            <h5 class="card-title mb-0">Add Job</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Select customer</label>

                                <div class="col-sm-10 d-flex align-items-center flex-wrap gap-24">
                                    <select class="form-select" name="customer">
                                        <option selected>Open this select menu</option>
                                        <?php
                                            if($numRowsGetCustomer > 0){
                                                while ($resultCustomer = mysqli_fetch_assoc($runCustomer)) {
                                                    echo '<option value="' . $resultCustomer['id'] . '">' . $resultCustomer['name'] . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>

                                </div>

                            </div>
                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Run</label>
                                <div class="col-sm-10">
                                    <input type="text" name="run" id="run" class="form-control" placeholder="Enter Run">
                                </div>
                            </div>

                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Date</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" />
                                </div>
                            </div>

                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Start</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" />
                                </div>
                            </div>

                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Finish</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" />
                                </div>
                            </div>

                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Select Driver</label>

                                <div class="col-sm-10 d-flex align-items-center flex-wrap gap-24">
                                    <select class="form-select" name="driver">
                                        <option selected>Open this select menu</option>
                                        <?php
                                            if($numRowsGetDriver > 0){
                                                while ($resultDriver = mysqli_fetch_assoc($runDriver)) {
                                                    echo '<option value="' . $resultDriver['id'] . '">' . $resultDriver['firstName'] . ' ' . $resultDriver['lastName'] . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>

                                </div>

                            </div>


                            <div class="row mb-24 gy-3 align-items-center">
                                <label class="form-label mb-0 col-sm-2">Select Vehicle</label>

                                <div class="col-sm-10 d-flex align-items-center flex-wrap gap-24">
                                    <select class="form-select" name="vehicle">
                                        <option selected>Open this select menu</option>
                                        <?php
                                            if($numRowsGetVehicle > 0){
                                                while ($resultVehicle = mysqli_fetch_assoc($runVehicle)) {
                                                    echo '<option value="' . $resultVehicle['id'] . '">' . $resultVehicle['numberPlate'] . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>

                                </div>

                            </div>

                            <div class="row mb-24 gy-3 align-items-center" id="showAlert">

                            </div>
                            <div class="row justify-content-end">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-light-600 text-dark mx-10"
                                        id="cancelButton">Cancel</button>
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
<script>
function getCurrentTime() {
    const now = new Date();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0'); // Month is zero-indexed
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    return `${year}:${month}:${day} ${hours}:${minutes}:${seconds}`;
}

$(Operations Portal).ready(function() {
    $('#submitButton').click(function(e) {
        $('#submitButton').prop('disabled', true);
        e.preventDefault(); // Prevent form from submitting normally

        // Collect form data
        let customer = $('select[name="customer"]').val();
        let run = $('#run').val();
        let date = $('input[type="date"]').val();
        let startTime = $('input[type="time"]').eq(0).val() + ':00'; // First time input (Start time)
        let finishTime = $('input[type="time"]').eq(1).val() + ':00'; // Second time input (Finish time)
        let driver = $('select[name="driver"]').val();
        let vehicle = $('select[name="vehicle"]').val();

        // Perform basic validation if needed
        if (customer === '' || run === '' || date === '' || startTime === '' || finishTime === '' ||
            driver === '' || vehicle === '') {
            $('#showAlert').html('<div class="alert alert-danger">All fields are required.</div>');
            return;
        }

        // Clear any previous alerts
        $('#showAlert').html('');

        console.log('Customer: ', customer);
        console.log('Run: ', run);
        console.log('Date: ', date);
        console.log('Start Time: ', startTime);
        console.log('Finish Time: ', finishTime);
        console.log('Driver: ', driver);
        console.log('Vehicle: ', vehicle);

        $.ajax({
            url: 'PHP/addJob.php',
            type: 'POST',
            data: {
                customer: customer,
                run: run,
                date: date,
                start_time: startTime,
                finish_time: finishTime,
                driver: driver,
                vehicle: vehicle,
                currentTime : getCurrentTime()
            },
            success: function(response) {
                $('#showAlert').html(
                    '<div class="alert alert-success">Job added successfully </div>');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
            },
            error: function(xhr, status, error) {
                $('#submitButton').prop('disabled', false);
                $('#showAlert').html(
                    '<div class="alert alert-danger">There was an error. Please try again.</div>'
                    );
            }
        });
    });
});
</script>