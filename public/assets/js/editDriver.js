function showAlert(message, id, status) {
    const template = `<div class="alert alert-${status} bg-${status}-100 text-${status}-600 border-${status}-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between" role="alert">
                        ${message}
                    </div>`;
    const box = $(id);
    box.empty();
    box.append(template);

    setTimeout(() => {
        box.find('.alert').fadeOut(1000, function() {
            $(this).remove();
        });
    }, 1000);
}

function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

$(document).ready(function(){
    function validateForm() {
        let isValid = true;

        $('#firstName, #lastName, #mobile, #uniqueId').each(function(){
            if ($(this).val() === "" || $(this).val() == null || $(this).val() == undefined) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        return isValid;
    }

    $('#submitButton').on('click', function(event){

        const driverId = getQueryParam('driverId');

        event.preventDefault(); 
        var employeeType = $('input[name="employeeType"]:checked').val();
        var paystructure = $('input[name="payStructure"]:checked').val();
        
        if (validateForm() && employeeType && paystructure){
    
            var loader = $("#addDriverPageLoader");
            loader.modal('show');
    
            const formData = {
                driverId: driverId,
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                mobile: $('#mobile').val(),
                employeeType: employeeType,
                payStructure: paystructure,
                uniqueId: $('#uniqueId').val(),
                pin: $("#pin").val()
            };
            console.log(formData);
    
            $.ajax({
                url: 'PHP/submitEditedDriverData.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    window.location.href = 'driver.php';

                },
                error: function(xhr, status, error) {
                    loader.modal('hide');
                    console.error('AJAX Error: ' + status + error);
                    showAlert("An error occurred. Please try again later.", "#showAlert", "danger");
                }
            });
        }
        else {
            showAlert("All fields are required", "#showAlert", "danger");
        }
    });
    


    $("#cancelButton").click(function(){
        window.location.href = 'driver.php';
    });


});
