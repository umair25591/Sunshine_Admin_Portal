$(document).ready(function() { 
    getVehiclesData();
});




$('.toggle-workshop-status').click(function() {
    const button = $(this);
    const vehicleId = button.data('id');
    const currentStatus = button.siblings('.workshopStatus').text();
    const newStatus = (currentStatus === 'Enabled') ? 'Disabled' : 'Enabled';
    const newLocation = newStatus === 'Enabled' ? 'Workshop' : 'Depot';  

    $.ajax({
        url: 'PHP/toggleWorkshopStatus.php',
        method: 'POST',
        dataType: 'json',
        data: { id: vehicleId, workshopStatus: newStatus === 'Enabled' ? 1 : 0 },
        success: function(response) {
            if (response.success) {
                button.siblings('.workshopStatus').text(newStatus);
                button.closest('tr').find('.vehicle-location').text(newLocation); 
            } else {
                alert('Failed to update the workshop status.');
            }
        },
        error: function() {
            alert('An error occurred while updating the workshop status.');
        }
    });
});


$(document).on('click', '.statusToggle', function() {
    const vehicleId = $(this).data('id');
    const status = $(this).data('status');
    statusToggle(status, vehicleId);
});


function statusToggle(status, vehicleId) {
    $.ajax({
        url: 'PHP/toggleVehicleStatus.php',
        method: 'POST',
        dataType: 'json',
        data: {
            status : status,
            vehicleId : vehicleId
        },
        success: function(response) {
            console.log(response);
            if(response.status == "success"){
                getVehiclesData();
            }
        },
        error: function(e) {
            console.log(e);
        }
    });
}



function getVehiclesData() {
    $.ajax({
        url: 'PHP/getVehicleAll.php', 
        method: 'POST',
        dataType: 'json', 
        success: function(data) {
            console.log(data); 
            
            if (data.status === 'error') {
                $('#VehiclesData').html('<tr><td colspan="7">' + data.message + '</td></tr>');
                return;
            }
            
            if (data.length > 0) {

                let rego = '-';
                let status = '-';
                let defectStatus = '-';
                let workshop = '-';
                let enableDisableVehicle = '-';
                let lockOutStatus = '-';


                let vehicleRows = '';
                
                data.forEach(vehicle => {
                    
                    rego = vehicle.numberPlate;

                    if(vehicle.status == 'Inactive'){
                        status = 'Disabled';
                    }
                    else if(vehicle.workshopStatus == 1){
                        status = 'Workshop';
                    }
                    else if(vehicle.allocationStatus == 'Active'){
                        status = vehicle.allocatedDriver;
                    }
                    else{
                        status = vehicle.location;
                    }

                    defectStatus = vehicle.defectStatus == 'Active' ? `<button class="btn btn-sm btn-danger w-144-px defect${vehicle.defectStatus} mx-10">View Defect</button>` : `<button class="btn btn-sm btn-warning w-144-px mx-10">View Defect</button>`;
                    workshop = vehicle.workshopStatus == 0 ? `<button class="btn btn-sm btn-primary w-200-px mx-10">Assign To Workshop</button>` : `<button class="btn btn-sm w-200-px btn-warning mx-10">Release From Workshop</button>`;
                    enableDisableVehicle = vehicle.status == 'Active' ? `<button class="btn btn-sm btn-danger w-100-px mx-10 statusToggle" data-id="${vehicle.id}" data-status="Inactive">Disable</button>` : `<button class="btn btn-sm btn-success w-100-px mx-10 statusToggle" data-id="${vehicle.id}" data-status="Active">Enable</button>`;
                    lockOutStatus = vehicle.lockoutStatus == 'Not Lockout' ? `<button class="btn btn-sm lockOutColor mx-10">Do Not Start</button>` : `<button class="btn btn-sm btn-danger mx-10">Do Not Start</button>`;


                    vehicleRows += `
                        <tr>
                            <td>${rego}</td>
                            <td>${status}</td>
                            <td>${defectStatus}${workshop} <a href="vehicleHistory.php?vehicleId=${vehicle.id}" class="btn btn-sm btn-primary mx-10">View Usage</a> ${enableDisableVehicle}${lockOutStatus}</td>
                        </tr>
                    `;
                });

                $('#VehiclesData').html(vehicleRows);

            } else {
                $('#VehiclesData').html('<tr><td colspan="7">No vehicles found.</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.log('Error:', error);
            console.log('Response:', xhr.responseText); 
            $('#VehiclesData').html('<tr><td colspan="7">An error occurred while fetching data.</td></tr>');
        }
    });
}


function toggleDefectButtonText() {
    const buttons = document.querySelectorAll('.defectActive');

    buttons.forEach(button => {
        if (button.textContent === 'View Defect') {
            button.textContent = 'Active Defect';
        } else {
            button.textContent = 'View Defect';
        }
    });
}


setInterval(() => {
    toggleDefectButtonText();
}, 1000);