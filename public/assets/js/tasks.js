function extractTimeFromDateTime(dateTimeString) {
    const date = new Date(dateTimeString);

    let hours = date.getHours();
    let minutes = date.getMinutes();

    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;

    const time = `${hours}:${minutes}`;
    return time;
}

function isToday(dateTimeString) {
    const inputDate = new Date(dateTimeString);
    const today = new Date();

    return inputDate.getDate() === today.getDate() &&
           inputDate.getMonth() === today.getMonth() &&
           inputDate.getFullYear() === today.getFullYear();
}

$(document).ready(function(){
    $.ajax({
        url: 'PHP/getTasks.php',
        method: 'POST',
        dataType: 'json',
        success: function(response){
            console.log(response);
            if (response.status == "success") {
                var recentlyCompletedTable = $("#recentlyCompletedTable");
                var pendingTable = $("#pendingTable");
                recentlyCompletedTable.empty();
                pendingTable.empty();
            
                var hasCompletedJobs = false;
                var hasPendingJobs = false;
            
                response.data.forEach(element => {
                    if (element.yardDutyStatus == "Done") {
                        var taskId = element.yardDutyID;
                        var task = element.taskName;
                        var completedBy = "-";
                        var assignTo = "-";
                        var time = "-";
                        var recurring = element.recurringTaskMatch;
                        var vehicleNumberPlate = element.vehicleNumberPlate ? element.vehicleNumberPlate : '';
            
                        if (element.driverName) {
                            assignTo = element.driverName;
                            completedBy = element.driverName;
                        }
            
                        if (element.yardDutyUpdatedAt) {
                            const updatedAtTime = element.yardDutyUpdatedAt;
            
                            if (isToday(updatedAtTime)) {
                                const formattedTime = extractTimeFromDateTime(updatedAtTime);
                                time = formattedTime;
            
                                const recentTemplate = `<tr>
                                                            <td>${taskId}</td>
                                                            <td>${vehicleNumberPlate}${task}</td>
                                                            <td>${completedBy}</td>
                                                            <td>${assignTo}</td>
                                                            <td>${time}</td>
                                                            <td>${recurring}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="relistYardDuty" data-yarddutyid="${element.yardDutyID}">Relist</button>
                                                                ${recurring === "No" ? `<button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="pendingCreateRecurringButton" data-yarddutyid="${element.yardDutyID}">Create Recurring</button>` : ''}
                                                            </td>
                                                        </tr>`;
                                recentlyCompletedTable.append(recentTemplate);
                                hasCompletedJobs = true;
                            }
                        }
                    } else if (element.yardDutyStatus == "Pending") {
                        var taskId = element.yardDutyID;
                        var task = element.taskName;
                        var completedBy = "-";
                        var assignTo = "-";
                        var time = "-";
                        var recurring = element.recurringTaskMatch;
                        var vehicleNumberPlate = element.vehicleNumberPlate ? element.vehicleNumberPlate + ' - ' : '';
            
                        if (element.driverName) {
                            assignTo = element.driverName;
                        }
                        const pendingTemplate = `<tr>
                                <td>${taskId}</td>
                                <td>${vehicleNumberPlate}${task}</td>
                                <td>${completedBy}</td>
                                <td>${assignTo}</td>
                                <td>${time}</td>
                                <td>${recurring}</td>
                                <td>
                                    ${recurring === "No" ? `<button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="pendingCreateRecurringButton" data-yarddutyid="${element.yardDutyID}">Create Recurring</button>` : ''}
                                    ${recurring === "Yes" ? `<button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm">Edit</button>` : ''}
                                    <button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="pendingCancelButton" data-yarddutyid="${element.yardDutyID}">Cancel</button>
                                </td>
                            </tr>`;

                        pendingTable.append(pendingTemplate);
                        hasPendingJobs = true;
                    }
                });
            
                if (!hasCompletedJobs) {
                    recentlyCompletedTable.append('<tr><td colspan="7">No recently completed task</td></tr>');
                }
            
                if (!hasPendingJobs) {
                    pendingTable.append('<tr><td colspan="7">No pending task</td></tr>');
                }
            }
            
            else{

            }
        },
        error: function(err){
            console.error(err);
        }
    });




    $.ajax({
        url: 'PHP/getTasksForRecurring.php',
        method: 'POST',
        dataType: 'json',
        success: function(response){
            console.log(response);
            if (response.status == "success") {

                var tomorrowTable = $("#tomorrowTable");
                var recurringTable = $("#recurringTable");
            
                tomorrowTable.empty();
                recurringTable.empty();
            
                var hasTomorrowTasks = false;
                var hasRecurringTasks = false;
            
                response.data.forEach(element => {
                    if (element.recurringTaskDate) {
                        var taskId = element.recurringTaskTaskID;
                        var task = element.taskName;
                        var completedBy = "-";
                        var assignTo = "-";
                        var time = "-";
                        var recurring = "No";
            
                        if (element.driverName) {
                            assignTo = element.driverName;
                        }
            
                        var recurringDate = new Date(element.recurringTaskDate);
            
                        var today = new Date();
                        var tomorrow = new Date(today);
                        tomorrow.setDate(today.getDate() + 1);
            
                        tomorrow.setHours(0, 0, 0, 0);
                        recurringDate.setHours(0, 0, 0, 0);
            
                        if (recurringDate.getTime() === tomorrow.getTime()) {
                            const tomorrowTemplate = `<tr>
                                <td>${taskId}</td>
                                <td>${task}</td>
                                <td>${completedBy}</td>
                                <td>${assignTo}</td>
                                <td>${time}</td>
                                <td>${recurring}</td>
                                <td>
                                    <button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="tomorrowCancelButton" data-recurringid="${element.recurringTaskID}">Cancel</button>
                                    <a href="editTask.php?taskId=${element.recurringTaskID}" type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm">Edit</a>
                                </td>
                            </tr>`;
                            tomorrowTable.append(tomorrowTemplate);
                            hasTomorrowTasks = true;
                        }
                    } else if (element.recurringTaskDays) {
                        var taskId = element.recurringTaskTaskID;
                        var task = element.taskName;
                        var completedBy = "-";
                        var assignTo = "-";
                        var time = "-";
                        var recurring = "Yes";
            
                        if (element.driverName) {
                            assignTo = element.driverName;
                        }
            
                        const recurringTemplate = `<tr>
                            <td>${taskId}</td>
                            <td>${task}</td>
                            <td>${completedBy}</td>
                            <td>${assignTo}</td>
                            <td>${time}</td>
                            <td>${recurring}</td>
                            <td>
                                <a href="editTask.php?taskId=${element.recurringTaskID}" type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm">Edit</a>
                                ${element.recurringTaskStatus == "Active" ? 
                                    `<button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="recurrringPauseButton" data-recurringid="${element.recurringTaskID}">Pause</button>`
                                     : 
                                    `<button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="recurrringActiveButton" data-recurringid="${element.recurringTaskID}">Active</button>`
                                    }
                                <button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="recurrringDeleteButton" data-recurringid="${element.recurringTaskID}">Delete</button>
                                <button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm" id="recurrringListNowButton" data-recurringid="${element.recurringTaskID}">List Now</button>
                            </td>
                        </tr>`;
                        recurringTable.append(recurringTemplate);
                        hasRecurringTasks = true;
                    }
                });
            
                if (!hasTomorrowTasks) {
                    tomorrowTable.append('<tr><td colspan="7">No tasks for tomorrow</td></tr>');
                }
            
                if (!hasRecurringTasks) {
                    recurringTable.append('<tr><td colspan="7">No recurring tasks</td></tr>');
                }
            }
            
        },
        error: function(err){
            console.error(err);
        }
    });




});





$(document).ready(function() {
    $('#showCompleted').change(function() {
        if ($(this).is(':checked')) {
            $('.completed').show();
        } 
        else {
            $('.completed').hide();
        }
    });

    if ($('#showCompleted').is(':checked')) {
        $('.completed').show();
    } 
    else {
        $('.completed').hide();
    }


    $(document).on('click', '#recurrringPauseButton', function(){
        const recurringId = $(this).data('recurringid');
        console.log(recurringId);
        if(recurringId){
            $("#confirmModalLabel").html('Pause');
            $("#recurringIdInput").val(recurringId);
            $("#confirmModal").modal('show');
            $("#confirmModal").find('.yesButton').attr('id', 'pauseButton');
        }
    });

    $(document).on('click', '#recurrringActiveButton', function(){
        const recurringId = $(this).data('recurringid');
        console.log(recurringId);
        if(recurringId){
            $("#confirmModalLabel").html('Active');
            $("#recurringIdInput").val(recurringId);
            $("#confirmModal").modal('show');
            $("#confirmModal").find('.yesButton').attr('id', 'activeButton');
        }
    });

    $(document).on('click', '#recurrringDeleteButton', function(){
        const recurringId = $(this).data('recurringid');
        console.log(recurringId);
        if(recurringId){
            $("#confirmModalLabel").html('Delete');
            $("#recurringIdInput").val(recurringId);
            $("#confirmModal").modal('show');
            $("#confirmModal").find('.yesButton').attr('id', 'deleteButton');
        }
    });

    $(document).on('click', '#recurrringListNowButton', function(){
        const recurringId = $(this).data('recurringid');
        console.log(recurringId);
        if(recurringId){
            $("#confirmModalLabel").html('List Now');
            $("#recurringIdInput").val(recurringId);
            $("#confirmModal").modal('show');
            $("#confirmModal").find('.yesButton').attr('id', 'listNowButton');
        }
    });

    $(document).on('click', '#pendingCancelButton', function(){
        const yardDutyId = $(this).data('yarddutyid');
        console.log(yardDutyId);
        if(yardDutyId){
            $("#confirmModalLabel").html('Cancel');
            $("#recurringIdInput").val(yardDutyId);
            $("#confirmModal").modal('show');
            $("#confirmModal").find('.yesButton').attr('id', 'cancelButton');
        }
    });

    $(document).on('click', '#pendingCreateRecurringButton', function(){
        const yardDutyId = $(this).data('yarddutyid');
        console.log(yardDutyId);
        if(yardDutyId){
            $("#createRecurringLabel").html('Create Recurring');
            $("#yardDutyIdInput").val(yardDutyId);
            $("#createRecurring").modal('show');
            $("#createRecurring").find('.yesButton').attr('id', 'createButton');
        }
    });

    $(document).on('click', '#relistYardDuty', function(){
        const yardDutyId = $(this).data('yarddutyid');
        console.log(yardDutyId);
        if(yardDutyId){
            $("#confirmModalLabel").html('Relist');
            $("#recurringIdInput").val(yardDutyId);
            $("#confirmModal").modal('show');
            $("#confirmModal").find('.yesButton').attr('id', 'relistButton');
        }
    });

    $(document).on('click', '#tomorrowCancelButton', function(){
        const yardDutyId = $(this).data('recurringid');
        console.log(yardDutyId);
        if(yardDutyId){
            $("#confirmModalLabel").html('Cancel');
            $("#recurringIdInput").val(yardDutyId);
            $("#confirmModal").modal('show');
            $("#confirmModal").find('.yesButton').attr('id', 'tomorrowCancelComfirmButton');
        }
    });






    $(document).on('click', '#pauseButton', function(){
        const recurringIdInputValue = $("#recurringIdInput").val();
        if(recurringIdInputValue){
            $.ajax({
                url: 'PHP/pauseRecurringTask.php',
                method: 'POST',
                data: {
                    recurringId : recurringIdInputValue
                },
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status == "success"){
                        window.location.reload();
                    }
                },
                error: function(err){
                    console.error(err);
                }
            });
        }
    });

    $(document).on('click', '#activeButton', function(){
        const recurringIdInputValue = $("#recurringIdInput").val();
        if(recurringIdInputValue){
            $.ajax({
                url: 'PHP/activeRecurringTask.php',
                method: 'POST',
                data: {
                    recurringId : recurringIdInputValue
                },
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status == "success"){
                        window.location.reload();
                    }
                },
                error: function(err){
                    console.error(err);
                }
            });
        }
    });

    $(document).on('click', '#deleteButton', function(){
        const recurringIdInputValue = $("#recurringIdInput").val();
        if(recurringIdInputValue){
            $.ajax({
                url: 'PHP/deleteRecurringTask.php',
                method: 'POST',
                data: {
                    recurringId : recurringIdInputValue
                },
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status == "success"){
                        window.location.reload();
                    }
                },
                error: function(err){
                    console.error(err);
                }
            });
        }
    });

    $(document).on('click', '#listNowButton', function(){
        const recurringIdInputValue = $("#recurringIdInput").val();
        if(recurringIdInputValue){
            $.ajax({
                url: 'PHP/listNowRecurringTask.php',
                method: 'POST',
                data: {
                    recurringId : recurringIdInputValue
                },
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status == "success"){
                        window.location.reload();
                    }
                },
                error: function(err){
                    console.error(err);
                }
            });
        }
    });

    $(document).on('click', '#cancelButton', function(){
        const yardDutyIdInputValue = $("#recurringIdInput").val();
        if(yardDutyIdInputValue){
            $.ajax({
                url: 'PHP/cancelPendingTask.php',
                method: 'POST',
                data: {
                    yardDutyId : yardDutyIdInputValue
                },
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status == "success"){
                        window.location.reload();
                    }
                },
                error: function(err){
                    console.error(err);
                }
            });
        }
    });

    $(document).on('click', '#createButton', function() {
        const yardDutyIdInputValue = $("#yardDutyIdInput").val();
        
        const checkedDays = [];
        $(".recurringDaysBox .form-check-input:checked").each(function() {
            checkedDays.push($(this).val());
        });
    
        if (yardDutyIdInputValue && Array.isArray(checkedDays) && checkedDays.length > 0) {
            $.ajax({
                url: 'PHP/createRecurringTask.php',
                method: 'POST',
                data: {
                    yardDutyId: yardDutyIdInputValue,
                    recurringDays: checkedDays
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.status == "success") {
                        window.location.reload();
                    }
                },
                error: function(err) {
                    console.error(err);
                }
            });
        }
        else{
            console.log("No Days Selected");
        }
    });
    
    $(document).on('click', '#relistButton', function(){
        const yardDutyIdInputValue = $("#recurringIdInput").val();
        if(yardDutyIdInputValue){
            $.ajax({
                url: 'PHP/relistYardTask.php',
                method: 'POST',
                data: {
                    yardDutyId : yardDutyIdInputValue
                },
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status == "success"){
                        window.location.reload();
                    }
                },
                error: function(err){
                    console.error(err);
                }
            });
        }
    });

    $(document).on('click', '#tomorrowCancelComfirmButton', function(){
        const recurringTaskIdInputValue = $("#recurringIdInput").val();
        if(recurringTaskIdInputValue){
            $.ajax({
                url: 'PHP/cancelTomorrowRecurringTask.php',
                method: 'POST',
                data: {
                    recurringTaskId : recurringTaskIdInputValue
                },
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    if(response.status == "success"){
                        window.location.reload();
                    }
                },
                error: function(err){
                    console.error(err);
                }
            });
        }
    });


});