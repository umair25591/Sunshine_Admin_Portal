function showAlert(message, id, status) {
    const template = `<div class="alert alert-${status} bg-${status}-100 text-${status}-600 border-${status}-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between" role="alert">
                        ${message}
                    </div>`;
    const box = $(id);
    box.empty();
    box.append(template);

    setTimeout(() => {
        box.find('.alert').fadeOut(1000, function () {
            $(this).remove();
        });
    }, 1000);
}


$(document).ready(function () {
    function validateForm() {
        let isValid = true;

        $('#firstName, #lastName, #mobile, #uniqueId, #pin').each(function () {
            if ($(this).val() === "" || $(this).val() == null || $(this).val() == undefined) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        return isValid;
    }

    $('#submitButton').on('click', function (event) {
        event.preventDefault();
        var employeeType = $('input[name="employeeType"]:checked').val();
        var paystructure = $('input[name="payStructure"]:checked').val();

        if (validateForm() && employeeType && paystructure) {

            var loader = $("#addDriverPageLoader");
            loader.modal('show');

            const formData = {
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                mobile: $('#mobile').val(),
                employeeType: employeeType,
                payStructure: paystructure,
                uniqueId: $('#uniqueId').val(),
                pin: $("#pin").val()
            };
            console.log(formData);

            setTimeout(() => {
                console.log("Submitting Driver Data")
                $.ajax({
                    url: 'PHP/submitDriverData.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        loader.modal('hide');
    
                        if (response.status == "success") {
                            showAlert("Added successfully", "#showAlert", "success");
    
                            $('#firstName').val('');
                            $('#lastName').val('');
                            $('#mobile').val('');
                            $('#uniqueId').val('');
                            $('#pin').val('');
    
                            $('input[name="employeeType"]').prop('checked', false);
                            $('input[name="payStructure"]').prop('checked', false);
    
                        } else {
                            showAlert("Failed! Please try again", "#showAlert", "danger");
                        }
                    },
                    error: function (xhr, status, error) {
                        loader.modal('hide');
                        console.error('AJAX Error: ' + status + error);
                        showAlert("An error occurred. Please try again later.", "#showAlert", "danger");
                    }
                });
            }, 2000);

        }
        else {
            showAlert("All fields are required", "#showAlert", "danger");
        }
    });



    $("#cancelButton").click(function () {
        window.location.href = 'driver.php';
    });


});
