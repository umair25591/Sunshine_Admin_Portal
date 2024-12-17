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


function convertToMinutes(time) {
    const [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
}

function checkDate(date) {
    const jobDate = date;
    const currentDate = new Date();
    const jobDateObj = new Date(jobDate);
    const formatDate = date => date.toISOString().split('T')[0];
    const today = formatDate(currentDate);
    const tomorrow = formatDate(new Date(currentDate.getTime() + 24 * 60 * 60 * 1000));
    const yesterday = formatDate(new Date(currentDate.getTime() - 24 * 60 * 60 * 1000));

    let displayDate;

    if (jobDate === today) {
        displayDate = 'Today';
    } 
    else if (jobDate === tomorrow) {
        displayDate = 'Tomorrow';
    } 
    else if (jobDate === yesterday) {
        displayDate = 'Yesterday';
    } 
    else {
        displayDate = jobDate;
    }

    return displayDate;
}


$(document).ready(function() {
    
    function getSelectedFilters() {
        return {
            showCompleted: $('#showCompleted').is(':checked'),
            todayOnly: $('#todayOnly').is(':checked'),
            unallocatedDriverOnly: $('#unallocatedDriverOnly').is(':checked'),
            unallocatedVehicleOnly: $('#unallocatedVehicleOnly').is(':checked'),
        };
    }
    

    function fetchAndShowJobs() {
        const filters = getSelectedFilters();
        
        $.ajax({
            url: 'PHP/getJobsAll.php',
            method: 'POST',
            dataType: 'json',
            data: filters,
            success: function(response) {
                console.log(response);
                showFilteredJobs(response.data, filters);
            },
            error: function(err) {
                console.error(err);
            }
        });
    }

    function showFilteredJobs(data, filters) {

        var jobsTable = $("#jobsData");
        jobsTable.empty();

        data.forEach(element => {

            let shouldShow = false;

            const anyFilterActive =
                filters.showCompleted ||
                filters.todayOnly ||
                filters.unallocatedDriverOnly ||
                filters.unallocatedVehicleOnly;

            if (!anyFilterActive && element.status === "Pending") {
                shouldShow = true;
            } 
            else {
                if (filters.showCompleted && element.status === "Done") {
                    // Check if todayOnly is also selected
                    if (filters.todayOnly) {
                        if (checkDate(element.date) === 'Today') {
                            shouldShow = true;
                        }
                    } else {
                        shouldShow = true;
                    }

                    $("#unallocatedDriverOnly").prop('checked', false);
                    $("#unallocatedVehicleOnly").prop('checked', false);
                }

                // Check todayOnly with unallocatedDriverOnly
                if (filters.unallocatedDriverOnly && element.driver === null) {
                    if (filters.todayOnly) {
                        if (checkDate(element.date) === 'Today') {
                            shouldShow = true;
                        }
                    } else {
                        shouldShow = true;
                    }

                    $("#showCompleted").prop('checked', false);
                }

                // Check todayOnly with unallocatedVehicleOnly
                if (filters.unallocatedVehicleOnly && element.vehicle === null) {
                    if (filters.todayOnly) {
                        if (checkDate(element.date) === 'Today') {
                            shouldShow = true;
                        }
                    } else {
                        shouldShow = true;
                    }

                    $("#showCompleted").prop('checked', false);
                }

                // If todayOnly is selected without completed, unallocated driver, or vehicle
                if (!shouldShow && filters.todayOnly && checkDate(element.date) === 'Today') {
                    if (!filters.showCompleted && !filters.unallocatedDriverOnly && !filters.unallocatedVehicleOnly) {
                        shouldShow = true;
                    }
                }
            }

            

            console.log(filters);

            if (shouldShow) {

                const jobDay = checkDate(element.date);
                const jobStartHHMM = element.start.split(':').slice(0, 2).join(':');
                const jobFinishHHMM = element.finish.split(':').slice(0, 2).join(':');
                const startInMinutes = convertToMinutes(jobStartHHMM);
                const finishInMinutes = convertToMinutes(jobFinishHHMM);
                let durationInMinutes = finishInMinutes - startInMinutes;
                let durationInMillis = durationInMinutes * 60000;
                const formatTime = formatTimeDifference(durationInMillis);
                const driverName = element.firstName + " " + element.lastName;

                let status;
                switch (element.status) {
                    case 'Done':
                        status = '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Done</span>';
                        break;
                    case 'Canceled':
                        status = '<span class="bg-danger-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm">Canceled</span>';
                        break;
                    case 'Active':
                        status = '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>';
                        break;
                    case 'Pending':
                        status = '<span class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">Pending</span>';
                        break;
                }

                const template = `<tr>
                                    <td>${element.id}</td>
                                    <td>${element.customerName}</td>
                                    <td>${element.run}</td>
                                    <td>${jobDay}</td>
                                    <td>${jobStartHHMM}</td>
                                    <td>${jobFinishHHMM}</td>
                                    <td>${formatTime}</td>
                                    <td>${element.numberPlate}</td>
                                    <td>${driverName}</td>
                                    <td>${status}</td>
                                    <td>
                                        <button type="button" style="width: 100px;" class="btn btn-danger-600 radius-8 px-14 py-6 text-sm">Cancel</button>
                                        <button type="button" style="width: 100px;" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm">View</button>
                                    </td>
                                </tr>`;
                jobsTable.append(template);
            }
        });
    }

    fetchAndShowJobs();

    $('#showCompleted, #todayOnly, #unallocatedDriverOnly, #unallocatedVehicleOnly').on('change', function() {
        fetchAndShowJobs();
    });
});
