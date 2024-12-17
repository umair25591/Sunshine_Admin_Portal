$(document).ready(function () {

    function fetchComments(defectId) {
        $('#showComments').empty().html('Loading...');

        $.ajax({
            url: 'PHP/getComments.php',
            method: 'POST',
            data: { defectId: defectId },
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    $('#showComments').empty();

                    // Sort comments to show public first, then private
                    let sortedComments = response.data.sort((a, b) => {
                        if (a.type === "Public" && b.type === "Private") return -1;
                        if (a.type === "Private" && b.type === "Public") return 1;
                        return 0;
                    });

                    sortedComments.forEach(comment => {
                        let commentHTML = `<div class="commentMainBox">
                                                <div class="${comment.type}">${comment.type}</div>
                                                <div class="${comment.type}Comment">${comment.note}</div>
                                            </div>`;
                        $('#showComments').append(commentHTML);
                    });
                } else {
                    $('#showComments').html('<p>No comments found for this defect.</p>');
                }
            },
            error: function (xhr, status, error) {
                alert('Error fetching comments: ' + xhr.responseText);
            }
        });
    }

    function fetchDefectData() {
        var selectedVehicle = $('#vehicleSelect').val();
        var selectedDriver = $('#driverSelect').val();
    
        var showCurrent = $('#showCurrentSwitch').is(':checked') ? 1 : 0;
        var showResolved = $('#showResolvedSwitch').is(':checked') ? 1 : 0;
        var showUnresolvable = $('#showUnresolvableSwitch').is(':checked') ? 1 : 0;
    
        $.ajax({
            url: 'PHP/getDefectAll.php',
            method: 'POST',
            dataType: 'json',
            data: {
                vehicleId: selectedVehicle != 'null' ? selectedVehicle : null,
                driverId: selectedDriver != 'null' ? selectedDriver : null,
                showCurrent: showCurrent,
                showResolved: showResolved,
                showUnresolvable: showUnresolvable
            },
            success: function (response) {
                console.log(response);
                if (response.status === 'error') {
                    $('#defectdata').html('<tr><td colspan="7">' + response.message + '</td></tr>');
                    return;
                }
    
                if (response.data.length > 0) {
                    let defectRows = '';
    
                    response.data.forEach(defect => {
                        var defectNumber = defect.id;
                        var vehicle = defect.numberPlate;
                        var reportDateTime = defect.createdAt;
                        var reportDate = reportDateTime.split(' ')[0];
                        var reportedBy = defect.driverName;
                        var defectCategory = defect.faultType;
                        var fault = defect.fault;
                        var driverDefineFault = defect.driverDefineFault ? defect.driverDefineFault : '-';
                        var status = defect.status;
                        var addClass = status == 'Will Fixed Next' ? 'class="backgroundColor"' : '';
    
                        if (defect.faultType != null) {
                            defectRows += 
                            `<tr ${addClass}>
                                <td class="transparentRow">${defectNumber}</td>
                                <td class="transparentRow">${vehicle}</td>
                                <td class="transparentRow">${reportDate}</td>
                                <td class="transparentRow">${reportedBy}</td>
                                <td class="transparentRow">${defectCategory}</td>
                                <td class="transparentRow">${fault}</td>
                                <td class="transparentRow">${driverDefineFault}</td>
                                <td class="transparentRow"><button class="btn btn-sm btn-primary action-btn" data-defect-id="${defectNumber}" data-status="${status}">Click to open action box</button></td>
                            </tr>`;
                        }
                    });
    
                    $('#defectdata').html(defectRows);
    
                    $('.action-btn').click(function () {
                        var defectId = $(this).data('defect-id');
                        var defectStatus = $(this).data('status');
                    
                        fetchComments(defectId);
                        $('#defectId').val(defectId);
                        $('#defectIdDisplay').text(defectId);
                        $('#defectIdDisplay2').text(defectId);
                    
                        // Enable/disable buttons based on the defect's status
                        if (defectStatus !== "Active") {
                            $('.status-action-btn').prop('disabled', true);
                        } else {
                            $('.status-action-btn').prop('disabled', false);
                        }
                    
                        $('#exampleModal').modal('show');
                    });
                    
                } else {
                    $('#defectdata').html('<tr><td colspan="8">No defects found.</td></tr>');
                }
            },
            error: function (xhr, status, error) {
                console.log(error)
                $('#defectdata').html('<tr><td colspan="8">An error occurred while fetching data.</td></tr>');
            }
        });
    }
    

    fetchDefectData();

    $('#vehicleSelect, #driverSelect, #showCurrentSwitch, #showResolvedSwitch, #showUnresolvableSwitch').change(function () {
        fetchDefectData();
    });    

    $('#addPublicCommentBtn').on('click', function () {
        const defectId = $('#defectId').val();
        $('#commentDefectId').val(defectId);
        $('#addCommentModal').modal('show');
    });

    $('#addCommentForm').on('submit', function (e) {
        e.preventDefault();

        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).text('Submitting...');

        const formData = {
            defectId: $('#commentDefectId').val(),
            commentText: $('#commentText').val(),
            type: 'Public'
        };

        $.ajax({
            url: 'PHP/addPublicComment.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    $('#addCommentModal').modal('hide');
                    $('#commentText').val('');
                    fetchComments($('#commentDefectId').val()); // Fetch latest comments
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Error submitting comment: ' + xhr.responseText);
            },
            complete: function () {
                submitButton.prop('disabled', false).text('Add Comment');
            }
        });
    });

    $('#addPrivateCommentBtn').on('click', function () {
        const defectId = $('#defectId').val();
        $('#commentPrivateDefectId').val(defectId);
        $('#addPrivateCommentModal').modal('show');
    });

    $('#addPrivateCommentForm').on('submit', function (e) {
        e.preventDefault();

        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).text('Submitting...');

        const formData = {
            defectId: $('#commentPrivateDefectId').val(),
            commentText: $('#commentPrivateText').val(),
            type: 'Private'
        };

        $.ajax({
            url: 'PHP/addPrivateComment.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    $('#addPrivateCommentModal').modal('hide');
                    $('#commentPrivateText').val('');
                    fetchComments($('#commentPrivateDefectId').val()); // Fetch latest comments
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Error submitting comment: ' + xhr.responseText);
            },
            complete: function () {
                submitButton.prop('disabled', false).text('Add Comment');
            }
        });
    });



    $('.status-action-btn').on('click', function () {
        const defectId = $('#defectId').val(); 
        const status = $(this).data('status'); 

        $.ajax({
            url: 'PHP/updateDefectStatus.php',
            method: 'POST',
            data: { defectId: defectId, status: status },
            dataType: 'json',
            success: function (response) {
                console.log("button Clicked")
                if (response.status === "success") {
                    $("#showAlertMessage").html(`<div class="alert alert-success" role="alert">Submitted Successfully</div>`)
                    setTimeout(() => {
                        fetchDefectData();
                        $('#exampleModal').modal('hide');
                        $("#showAlertMessage").empty();
                    }, 1000);
                    
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Error updating status: ' + xhr.responseText);
            },
        });
    });


});
