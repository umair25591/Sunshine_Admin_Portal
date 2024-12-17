$(document).ready(function() {

    $("#assignToPerson").change(function() {
        if ($(this).is(':checked')) {
            $("#selectPerson").show();
        } 
        else {
            $("#selectPerson").hide();
        }
    });

    $("#assignToPerson").change();
});



function showAlert(message, id, color) {
    const template = `<div class="alert alert-${color} bg-${color}-100 text-${color}-600 border-${color}-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between" role="alert">
                        ${message}
                    </div>`;
    const box = $(id);
    box.empty()
    box.append(template);
}

function getTomorrowDateTime() {
    let now = new Date();

    now.setDate(now.getDate() + 1);

    let year = now.getFullYear();
    let month = String(now.getMonth() + 1).padStart(2, '0');
    let day = String(now.getDate()).padStart(2, '0');
    let hours = String(now.getHours()).padStart(2, '0');
    let minutes = String(now.getMinutes()).padStart(2, '0');
    let seconds = String(now.getSeconds()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}


$(document).ready(function() {
    $('input[name="when"]').change(function() {
        if ($('#recurring').is(':checked')) {
            $('.recurringBox').removeClass('hide').show();
        } else {
            $('.recurringBox').addClass('hide').hide();
        }
    });

    $('#assignToPerson').change(function() {
        if ($(this).is(':checked')) {
            $('#selectPerson').removeClass('hide').show();
        } else {
            $('#selectPerson').addClass('hide').hide();
        }
    });

    $('#saveButton').click(function() {
        var taskName = $('#taskName').val();
        var whenValue = $('input[name="when"]:checked').val();
        var recurringDaysChecked = $('.recurringBox input[type="checkbox"]:checked').length;

        if (!taskName) {
            showAlert("Task name is required", "#showAlert", "danger")
            return false;
        }
        if (!whenValue) {
            showAlert("Please select when do you want this task to be completed", "#showAlert", "danger")
            return false;
        }
        if (whenValue === 'recurring' && recurringDaysChecked === 0) {
            showAlert("Please select days", "#showAlert", "danger")
            return false;
        }

        var formData = {
            task: taskName,
            when: whenValue,
            recurringDays: [],
            assignToPerson: $('#assignToPerson').is(':checked') ? $('#selectPerson').val() : null,
            cancelIfNotCompletedBy: $('#cancelIfNotCompletedBy').is(':checked') ? 1 : 0,
            priority: $('#selectPriority').val(),
            tomorrowDateTime: getTomorrowDateTime(),
            taskId: $("#taskId").val(),
            recurringTaskId: $("#recurringTaskId").val()
        };

        if (whenValue === 'recurring') {
            $('.recurringBox input[type="checkbox"]:checked').each(function() {
                formData.recurringDays.push($(this).val());
            });
        }

        $("#showAlert").empty();
        console.log(formData)

        $.ajax({
            url: 'PHP/editRecurringTask.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if(response.status === "success"){
                    showAlert("Task edited successfully", "#showAlert", "success")
                    setInterval(() => {
                        window.location.href = 'tasks.php';
                    }, 1000);
                }
            },
            error: function(xhr, status, error) {
                console.log('An error occurred: ' + error);
            }
        });
    });
});

