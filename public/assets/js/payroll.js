$(document).ready(function() {

let dataStorageArray = [];
let storeShiftId = [];

const showDateElement = $('.showDate');
let dateRange = '';
let currentWeekStart = new Date();

function initializeWeek() {
    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1);
}

function updateWeekDisplay() {
    const weekEnd = new Date(currentWeekStart);
    weekEnd.setDate(currentWeekStart.getDate() + 6);

    // Check if the calculated weekEnd date is greater than today's date
    if (weekEnd > new Date()) {
        weekEnd.setTime(new Date().getTime()); // Set weekEnd to today if it's beyond today
    }

    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    const startDateStr = currentWeekStart.toLocaleDateString('en-US', options);
    const endDateStr = weekEnd.toLocaleDateString('en-US', options);

    // Get today's date to compare with weekEnd
    const today = new Date();
    const todayDay = today.toLocaleDateString('en-US', options);

    if (weekEnd.toDateString() === today.toDateString()) {
        console.log("The end date is today!");
        $(".forwordIcon").css("display", "none");
    }
    else {
        console.log("The end date is not today.");
        $(".forwordIcon").show();
    }

    showDateElement.text(`${startDateStr} - ${endDateStr}`);
    dateRange = `${startDateStr} - ${endDateStr}`;

    fetchWeekData(currentWeekStart, weekEnd);
}


function getWeekday(dateString) {
    const options = { weekday: 'long' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

function changeWeek(days) {

    dataStorageArray = [];

    const newWeekStart = new Date(currentWeekStart);
    newWeekStart.setDate(newWeekStart.getDate() + days);

    if (newWeekStart > new Date()) {
        newWeekStart.setTime(new Date().getTime() - 6 * 24 * 60 * 60 * 1000);
    }

    currentWeekStart = newWeekStart;
    updateWeekDisplay();
}

// Fetch data based on the start and end dates
function fetchWeekData(startDate, endDate) {
    const formattedStartDate = startDate.toISOString().split('T')[0];
    const formattedEndDate = endDate.toISOString().split('T')[0];

    $.ajax({
        url: 'PHP/getDriversWeekDataForPayroll.php',
        type: 'POST',
        data: {
            start_date: formattedStartDate,
            end_date: formattedEndDate
        },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            const structuredData = structureShiftData(response);
            console.log(structuredData);
            populateTable(structuredData, startDate, endDate);
        },
        error: function() {
            console.error('Failed to fetch week data');
        }
    });
}


initializeWeek();
updateWeekDisplay();

$('.backwordIcon').on('click', function() {
    changeWeek(-7);
});
$('.forwordIcon').on('click', function() {
    changeWeek(7);
});


    function populateTable(data, startDate, endDate) {
        const tbody = $('#shiftsTableBody');
        tbody.empty();
    
        const daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        const dayOffsets = daysOfWeek.reduce((acc, day, index) => {
            const dayDate = new Date(startDate);
            dayDate.setDate(startDate.getDate() + index);
            acc[day] = dayDate;
            return acc;
        }, {});

        const hasData = Object.keys(data).length > 0;

        if (!hasData) {
            tbody.append('<tr><td colspan="12">No data found for this week</td></tr>');
            return;
        }
    
        for (const driverId in data) {
            const driverData = data[driverId];
            const driverName = driverData.driver_name;
    
            const row = $('<tr></tr>');
            row.append(`<td class="font-14 drivername" data-driverid="${driverId}">${driverName}</td>`);
    
            let totalOrdinaryHours = 0;
            let totalSaturdayHours = 0;
            let totalSundayHours = 0;
    
            daysOfWeek.forEach(day => {
                const date = dayOffsets[day];
                const formattedDate = formatDate(date);
    
                const shiftHours = driverData.shifts[formattedDate] || 0;
                const breakHours = driverData.breaks[formattedDate] || 0;
                const paidHours = driverData.breaksPaid[formattedDate] || 0;
    
                const cellContent = `
                    <p class="m-0 font-14">${shiftHours.toFixed(1)}H - Shift</p>
                    <p class="m-0 font-14">${breakHours.toFixed(1)}H - Breaks</p>
                    <p class="m-0 font-14">${paidHours.toFixed(1)}H - Paid</p>
                `;
    
                row.append(`<td>${cellContent}</td>`);
    
                if (day === "Saturday") {
                    totalSaturdayHours += shiftHours;
                } else if (day === "Sunday") {
                    totalSundayHours += shiftHours;
                } else {
                    totalOrdinaryHours += shiftHours;
                }
            }); 
    
            row.append(`<td class="font-14 ordinary">${totalOrdinaryHours.toFixed(1)}</td>`);
            row.append(`<td class="font-14 saturday">${totalSaturdayHours.toFixed(1)}</td>`);
            row.append(`<td class="font-14 sunday">${totalSundayHours.toFixed(1)}</td>`);
            row.append(`
                <td>
                    <a href="payrollReview.php?driverId=${driverId}" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm">Review</a>
                    <button type="button" class="btn btn-success-600 radius-8 px-14 py-6 text-sm acceptButton">Accept</button>
                </td>
            `);
    
            tbody.append(row);
        }
    }


    $(document).on('click', '.acceptButton', function() {
        handleButtonClick($(this).closest('tr'));
        $(this).addClass('hide');
        $(this).closest('td').append('<button type="button" class="btn btn-danger-600 radius-8 px-14 py-6 text-sm rejectButton">Reject</button>');
    });

    $(document).on('click', '.rejectButton', function() {
        const row = $(this).closest('tr');
        const rowData = {
            driverId: row.find('.drivername').data('driverid'),
            driverName: row.find('.drivername').text().trim(),
            totalOrdinaryHours: row.find('.ordinary').text().trim(),
            totalSaturdayHours: row.find('.saturday').text().trim(),
            totalSundayHours: row.find('.sunday').text().trim()
        };

        dataStorageArray = dataStorageArray.filter(item => 
            !(item.driverId === rowData.driverId &&
            item.driverName === rowData.driverName &&
            item.totalOrdinaryHours === rowData.totalOrdinaryHours &&
            item.totalSaturdayHours === rowData.totalSaturdayHours &&
            item.totalSundayHours === rowData.totalSundayHours)
        );

        $(this).remove();
        row.find('.acceptButton').removeClass('hide');

        console.log('Data Removed:', dataStorageArray);
    });

    function handleButtonClick(row) {
        const rowData = {
            driverId: row.find('.drivername').data('driverid'),
            driverName: row.find('.drivername').text().trim(),
            totalOrdinaryHours: row.find('.ordinary').text().trim(),
            totalSaturdayHours: row.find('.saturday').text().trim(),
            totalSundayHours: row.find('.sunday').text().trim()
        };

        dataStorageArray.push(rowData);
        console.log('Data Stored:', dataStorageArray);
    }



    function formatDate(date) {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', weekday: 'long' };
        return date.toLocaleDateString('en-CA', options).replace(',', '');
    }

    function getHoursDifference(start, end) {
        const msInHour = 1000 * 60 * 60;
        const hours = (end - start) / msInHour;
    
        const [whole, fraction] = hours.toString().split('.');
    
        const formattedFraction = (fraction || '').padEnd(1, '0').slice(0, 1);
        const formattedHours = `${whole}.${formattedFraction}`;
    
        return parseFloat(formattedHours);
    }
    
    function structureShiftData(shifts) {
        const structuredData = {};
        storeShiftId = [];
        let hideButton = true; 
    
        shifts.forEach(shift => {
            storeShiftId.push(shift.shift_id)
            const driverId = shift.driver_id;
            const driverName = `${shift.firstName} ${shift.lastName}`;
            const clockIn = new Date(shift.clockIn);
            const clockOut = shift.clockOut ? new Date(shift.clockOut) : null;

            if (shift.lockEdit != "1") {
                console.log();
                hideButton = false;
            }
    
            if (!structuredData[driverId]) {
                structuredData[driverId] = {
                    driver_name: driverName,
                    shifts: {},
                    breaks: {},
                    breaksPaid:{},
                };
            }
    
            let currentDate = new Date(clockIn);
            let endDate = clockOut ? new Date(clockOut) : null;
    
            while (endDate && currentDate <= endDate) {
                const nextDate = new Date(currentDate);
                nextDate.setHours(24, 0, 0, 0);
    
                const start = currentDate;
                const end = nextDate < endDate ? nextDate : endDate;
                const formattedDate = formatDate(currentDate);
    
                const hoursWorked = getHoursDifference(start, end);
    
                if (!structuredData[driverId].shifts[formattedDate]) {
                    structuredData[driverId].shifts[formattedDate] = hoursWorked;
                } else {
                    structuredData[driverId].shifts[formattedDate] += hoursWorked;
                }
    
                currentDate = new Date(nextDate);
            }
    
            const breakStart = shift.break_start ? new Date(shift.break_start) : null;
            const breakEnd = shift.break_finish ? new Date(shift.break_finish) : null;
    
            if (breakStart && breakEnd) {
                currentDate = new Date(breakStart);
                endDate = new Date(breakEnd);
    
                while (currentDate <= endDate) {
                    const nextDate = new Date(currentDate);
                    nextDate.setHours(24, 0, 0, 0);
    
                    const start = currentDate;
                    const end = nextDate < endDate ? nextDate : endDate;
                    const formattedDate = formatDate(currentDate);
    
                    const breakDuration = getHoursDifference(start, end);
    
                    if (!structuredData[driverId].breaks[formattedDate]) {
                        structuredData[driverId].breaks[formattedDate] = breakDuration;
                    } else {
                        structuredData[driverId].breaks[formattedDate] += breakDuration;
                    }

                    const break_is_paid = shift.break_is_paid;

                    if (break_is_paid == "1") {
                        if (!structuredData[driverId].breaksPaid[formattedDate]) {
                            structuredData[driverId].breaksPaid[formattedDate] = breakDuration;
                        } else {
                            structuredData[driverId].breaksPaid[formattedDate] += breakDuration;
                        }
                    }
    
                    currentDate = new Date(nextDate);
                }
            }
        });

        if (hideButton) {
            $("#lockDriverEdits").hide();
        }
        else {
            $("#lockDriverEdits").show();
        }
    
        console.log(storeShiftId);
        return structuredData;
    }


    $('#ExportData').on('click', function() {
        $.ajax({
            url: 'PHP/exportPayrollPDF.php',
            type: 'POST',
            data: {
                data: JSON.stringify(dataStorageArray),
                dateRange: dateRange
            },
            dataType: 'json',
            success: function(response) {
                console.log(response)
                if (response.file) {
                    window.open('public/' + response.file, '_blank');
                }
                else {
                    console.error('Failed to get file URL');
                }
            },
            error: function() {
                console.error('Failed to export data');
            }
        });
    });


    
    $('#lockDriverEdits').on('click', function() {
        $("#payrollLoader").modal('show');

        (async function() {
            try {
                let response = await new Promise((resolve, reject) => {
                    $.ajax({
                        url: 'PHP/lockDriverEdits.php',
                        type: 'POST',
                        data: {
                            shiftIds: JSON.stringify(storeShiftId)
                        },
                        dataType: 'json',
                        success: function(response) {
                            resolve(response);
                        },
                        error: function() {
                            reject('Failed to export data');
                        }
                    });
                });
        
                console.log(response);
                
                await hideModal("#payrollLoader");
        
                await showModal("#alertModal");
        
                await new Promise(resolve => setTimeout(resolve, 2000));

                await hideModal("#alertModal");
        
                $("#lockDriverEdits").hide();
        
            }
            catch (error) {
                console.error(error);
                await hideModal("#payrollLoader");
            }
        })();
        
        function showModal(modalId) {
            return new Promise(resolve => {
                $(modalId).modal('show');
                resolve();
            });
        }
        
        function hideModal(modalId) {
            return new Promise(resolve => {
                $(modalId).modal('hide');
                resolve();
            });
        }
    });
    

});