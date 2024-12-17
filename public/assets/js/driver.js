function formatTimeDifference(ms) {
    const isNegative = ms < 0;
    ms = Math.abs(ms);

    const totalSeconds = Math.floor(ms / 1000);
    const totalMinutes = Math.floor(totalSeconds / 60);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    const seconds = totalSeconds % 60;
    const days = Math.floor(hours / 24);

    let formattedTime = "";

    if (days > 2) {
        formattedTime = `${days} days`;
    } else if (days === 1) {
        formattedTime = "1 day";
    } else if (hours > 0) {
        formattedTime = `${hours}H ${minutes}M`;
    } else if (minutes > 0) {
        formattedTime = `${minutes}M`;
    } else {
        formattedTime = `${seconds}S`;
    }

    if (isNegative) {
        formattedTime = `-${formattedTime}`;
    }

    return formattedTime;
}


$(document).ready(function(){
    function getDrivers(terminated) {
        $.ajax({
            url: 'PHP/getDriverAll.php',
            method: 'POST',
            data: { showTerminated: terminated ? 'true' : 'false' },
            dataType: 'json',
            success: function(response){
                console.log(response)
                if(response.status === "success"){
                    var driverBox = $("#driversData");
                    driverBox.empty();

                    response.data.forEach(element => {
                        var firstName = element.firstName;
                        var lastName = element.lastName;
                        var clockOff = "-";

                        if(element.shiftStatus === "Inactive"){
                            const clockOut = element.clockOut;
                            const clockOutTime  = new Date(clockOut);
                            const currentTime = new Date();
                            const timeDiff = currentTime - clockOutTime;
                            const formattedTime = formatTimeDifference(timeDiff);
                            clockOff = formattedTime;
                        }
                        else if(element.shiftStatus === "Active"){
                            clockOff = "Clock On";
                        }

                        const template = `<tr>
                                            <td>${firstName}</td>
                                            <td>${lastName}</td>
                                            <td>${clockOff}</td>
                                            <td> 
                                                <a type="button" href="editDriver.php?driverId=${element.id}" style="width: 100px;" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm">Edit</a>
                                            </td>
                                        </tr>`;
                        driverBox.append(template);
                    });
                } else {
                    console.warn(response.message);
                    $("#driversData").empty().append('<tr><td colspan="4">No Records Found</td></tr>');
                }
            },
            error: function(err){
                console.error(err);
            }
        });
    }

    $('#showTerminated').on('change', function() {
        const isChecked = $(this).is(':checked');
        getDrivers(isChecked);
        $("#driversData").empty();
        const template  = `<tr>
                              <td colspan="4" class="text-center">Loading...</td>
                           </tr>`;
        $("#driversData").append(template);
    });

    getDrivers($('#showTerminated').is(':checked'));
});


