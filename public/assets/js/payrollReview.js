$(document).ready(function() {

    const urlParams = new URLSearchParams(window.location.search);
    let driverId = urlParams.get('driverId');
        
    const showDateElement = $('.showDate');
    let dateRange = '';
    let currentWeekStart = new Date();
    
    function initializeWeek() {
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1);
    }
    
    function updateWeekDisplay() {
        const weekEnd = new Date(currentWeekStart);
        weekEnd.setDate(currentWeekStart.getDate() + 6);
    
        if (weekEnd > new Date()) {
            weekEnd.setTime(new Date().getTime());
        }
    
        const options = { weekday: 'long', day: 'numeric', month: 'long' };
        const startDateStr = currentWeekStart.toLocaleDateString('en-US', options);
        const endDateStr = weekEnd.toLocaleDateString('en-US', options);
    
        const today = new Date();
    
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
    
        fetchWeekData(currentWeekStart, weekEnd, driverId);
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
    
    function fetchWeekData(startDate, endDate, driverId) {
        const formattedStartDate = startDate.toISOString().split('T')[0];
        const formattedEndDate = endDate.toISOString().split('T')[0];
    
        $.ajax({
            url: 'PHP/getDriverPayweekData.php',
            type: 'POST',
            data: {
                start_date: formattedStartDate,
                end_date: formattedEndDate,
                driverId: driverId
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                const sortedData = sortData(response);

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


    function populateTable() {
        const tableBody = $('#driverDataTable');
        tableBody.empty();

        const hasData = Object.values(daysOfWeek).some(dayArray => dayArray.length > 0);

        if (!hasData) {
            tableBody.append('<tr><td colspan="8">No data found for this week</td></tr>');
            return; 
        }
    
        Object.keys(daysOfWeek).forEach(day => {
            let isFirstRow = true;
        
            daysOfWeek[day].forEach((shiftData, index) => {
                const shift = shiftData.shift;
                const breaks = shiftData.breaks;
        
                let row = '<tr>';
        
                if (isFirstRow) {
                    row += `<td rowspan="${daysOfWeek[day].length}" class="sideHeading">${day}</td>`;
                    isFirstRow = false;
                }
        
                row += `<td class="clockInTd" data-shiftid="${shift.shift_id}" >${shift.clockIn}</td>`;
        
                if (breaks.length === 0) {
                    row += `<td>No Break</td>`;
                    row += `<td>No Break</td>`;
                }
                else {
                    const breakStart = breaks.map(breakItem => `
                        <p class="m-0 font-14" data-breakid="${breakItem.break_id}">${breakItem.break_start}</p>
                    `).join('');
                    const breakEnd = breaks.map(breakItem => `
                        <p class="m-0 font-14">${breakItem.break_finish}</p>
                    `).join('');
                    row += `<td class="breakStartTd">${breakStart}</td>`;
                    row += `<td class="breakFinishTd">${breakEnd}</td>`;
                }
        
                row += `<td class="clockOutTd">${shift.clockOut}</td>`;
        
                if (shift.jobId != null) {
                    console.log("job is not null");
                    row += `
                        <td class="vehicleTd">${shift.vehicleNumberPlate || ''}</td>
                        <td class="jobTd">${shift.run || 'N/A'}</td>`;
                } else {
                    console.log("job is null");
                    row += `
                        <td class="vehicleTd">No Vehicle</td>
                        <td class="jobTd">No Job</td>`;
                }
        
                row += `<td class="durationTd">${shift.shiftDuration}</td>`;

                row += `<td><button type="button" class="btn btn-primary-600 radius-8 px-14 py-6 text-sm editTable">Edit</button></td>`;
        
                row += '</tr>';
        
                tableBody.append(row);
            });
        });

        $('.datetimepicker-input').on('keydown paste', function(e) {
            e.preventDefault();
        });
        
    }


    function initializeDateTimePickers() {

        $('#clockIn, #clockOut').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            icons: {
                time: 'fa-solid fa-clock',
                date: 'fa-solid fa-calendar',
                up: 'fa-solid fa-chevron-up',
                down: 'fa-solid fa-chevron-down',
                previous: 'fa-solid fa-chevron-left',
                next: 'fa-solid fa-chevron-right',
                today: 'fa-solid fa-calendar-check',
                clear: 'fa-solid fa-trash',
                close: 'fa-solid fa-times'
            }
        });
        
    
        $('.break-time').each(function() {
            $(this).datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                showClose: true,
                showClear: true,
                showTodayButton: true,
                icons: {
                    time: 'fa-solid fa-clock',
                    date: 'fa-solid fa-calendar',
                    up: 'fa-solid fa-chevron-up',
                    down: 'fa-solid fa-chevron-down',
                    previous: 'fa-solid fa-chevron-left',
                    next: 'fa-solid fa-chevron-right',
                    today: 'fa-solid fa-calendar-check',
                    clear: 'fa-solid fa-trash',
                    close: 'fa-solid fa-times'
                }
            });
        });
    }

    $(document).on('click', '.editTable', function() {
        var row = $(this).closest('tr');
        
        var clockIn = row.find('.clockInTd').text();
        var breakStart = row.find('.breakStartTd').find('p').map(function() {
            return {
                text: $(this).text(),
                id: $(this).data('breakid')
            }
        }).get();
        var breakFinish = row.find('.breakFinishTd').find('p').map(function() {
            return $(this).text();
        }).get();
        var clockOut = row.find('.clockOutTd').text();
        var vehicleNumber = row.find('.vehicleTd').text();
        var job = row.find('.jobTd').text();
        var shiftDuration = row.find('.durationTd').text();
        var shiftId = row.find('.clockInTd').data('shiftid');

        
        $('#editClockIn').val(clockIn);
        $('#editClockOut').val(clockOut);
        $('#editShiftId').val(shiftId);
        $('#vehicle').val(vehicleNumber);
        $('#job').val(job);
        $('#hours').val(shiftDuration);

        console.log(vehicleNumber)
        
        var combinedBreakHtml = '';
        breakStart.forEach((data, index) => {

            const breakFinishTime = breakFinish[index] || ''; 
        
            combinedBreakHtml += `
                <div class="subBreakContainer d-flex justify-content-between">
                    <div class="mb-3">
                        <label for="editBreakStart${index}" class="form-label">Break Start ${index + 1}</label>
                        <div class="input-group date break-time" id="breakStart${index}" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" id="editBreakStart${index}" data-breakid="${data.id}" data-target="#breakStart${index}" value="${data.text}">
                            <div class="input-group-append" data-target="#breakStart${index}" data-toggle="datetimepicker">
                                <div class="input-group-text" style="font-size: 30px;"><i class="fa-solid fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editBreakEnd${index}" class="form-label">Break End ${index + 1}</label>
                        <div class="input-group date break-time" id="breakEnd${index}" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" id="editBreakEnd${index}" data-target="#breakEnd${index}" value="${breakFinishTime}">
                            <div class="input-group-append" data-target="#breakEnd${index}" data-toggle="datetimepicker">
                                <div class="input-group-text" style="font-size: 30px;"><i class="fa-solid fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#breakContainer').html(combinedBreakHtml);
        

        initializeDateTimePickers();

        $('#editRowId').val(row.data('id'));
        
        $('#editModal').modal('show');
    });

    function calculateDuration(clockIn, clockOut) {
        var clockInDate = new Date(clockIn);
        var clockOutDate = new Date(clockOut);
        var durationMs = clockOutDate - clockInDate;
        if (durationMs < 0) {
            return 'Error: Clock-Out time is before Clock-In time';
        }
        var totalMinutes = Math.floor(durationMs / (1000 * 60));
        var hours = Math.floor(totalMinutes / 60);
        var minutes = totalMinutes % 60;
        var formattedHours = hours < 10 ? '0' + hours : hours;
        var formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
        var duration = formattedHours + ':' + formattedMinutes;
        return duration;
    }

    var generatedQuery = [];

    var counter = 1;

    $('#editForm').on('click', function(e) {
        e.preventDefault();

        const showEditDataTable = $("#showEditedData");
    
        var shiftId = $('#editShiftId').val();
        var clockIn = $('#editClockIn').val();
        var clockOut = $('#editClockOut').val();
        var vehicle = $('#vehicle').val();
        var job = $('#job').val();
        var hours = $('#hours').val();
        
        var breakData = [];
        $('.subBreakContainer').each(function(index) {
            var breakStart = $(this).find('#editBreakStart' + index).val();
            var breakFinish = $(this).find('#editBreakEnd' + index).val();
            var breakId = $(this).find('#editBreakStart' + index).data('breakid');
        
            breakData.push({
                break_id: breakId,
                break_start: breakStart,
                break_end: breakFinish
            });
        });
        

        var template = '<tr>';

        const duration = calculateDuration(clockIn, clockOut);

        if (duration.startsWith('Error:')) {
            alert('Invalid Time');
            return; 
        }

        template += `<td>${counter}</td>`;
        counter++;

        template += `<td>${clockIn}</td>`;

        const generateQuery = `UPDATE driverShifts SET clockIn = '${clockIn}', clockOut = '${clockOut}', duration = '${duration}' WHERE id = ${shiftId};`;

        if(breakData.length > 0){
            breakData.forEach(e => {
                const breakDuration = calculateDuration(e.break_start, e.break_end);
                const generateBreakQuery = `UPDATE breaks SET start = '${e.break_start}', finish = '${e.break_end}', duration = '${breakDuration}' WHERE id = ${e.break_id};`;
                generatedQuery.push(generateBreakQuery);
            });
            const breakStart = breakData.map(breakItem => `
                <p class="m-0 font-14" data-breakid="${breakItem.break_id}">${breakItem.break_start}</p>
            `).join('');
            const breakEnd = breakData.map(breakItem => `
                <p class="m-0 font-14">${breakItem.break_end}</p>
            `).join('');
            template += `<td>${breakStart}</td>`;
            template += `<td>${breakEnd}</td>`;
        }
        else{
            template += `<td>No Break</td>`;
            template += `<td>No Break</td>`;
        }

        generatedQuery.push(generateQuery);

        template += `<td>${clockOut}</td>`;
        template += `<td>${vehicle}</td>`;
        template += `<td>${job}</td>`;
        template += `<td>${duration}</td>`;

        template += '</tr>'

        showEditDataTable.append(template);
        $(".showEditedMainBox").removeClass('hide');

        console.log(generatedQuery);
        $("#editModal").modal('hide');
    
        // $.ajax({
        //     url: 'PHP/submitEditedPayroll.php',
        //     method: 'POST',
        //     data: {
        //         shift_id: shiftId,
        //         clockIn: clockIn,
        //         clockOut: clockOut,
        //         breaks: JSON.stringify(breakData)
        //     },
        //     success: function(response) {
        //         console.log(response);
        //     },
        //     error: function(xhr, status, error) {
        //         console.error(error);
        //     }
        // });
    });



    $(document).on('click', '#saveButton', function(){
        $("#payrollReviewLoader").modal('show');
         $.ajax({
            url: 'PHP/runGeneratedQueryForPayroll.php',
            method: 'POST',
            data: {
                query: JSON.stringify(generatedQuery)
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if(response.status == "success"){
                    window.location.href = 'payroll.php';
                }
            },
            error: function(xhr, status, error) {
                $("#payrollReviewLoader").modal('hide');
                console.error(error);
            }
        });
    });
    
    



    const daysOfWeek = {
        Sunday: [],
        Monday: [],
        Tuesday: [],
        Wednesday: [],
        Thursday: [],
        Friday: [],
        Saturday: []
    };
    
    function getDayOfWeek(dateString) {
        const date = new Date(dateString);
        const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        return dayNames[date.getDay()];
    }

    function sortData(data) {

        Object.keys(daysOfWeek).forEach(day => {
            daysOfWeek[day] = [];
        });

        let shifts = {};
    
        data.forEach(item => {
            const dayOfWeek = getDayOfWeek(item.clockIn);
        
            if (!shifts[item.shift_id]) {
                shifts[item.shift_id] = {
                    shift: {
                        clockIn: item.clockIn,
                        clockOut: item.clockOut,
                        driver_id: item.driver_id,
                        driver_status: item.driver_status,
                        firstName: item.firstName,
                        lastName: item.lastName,
                        shift_id: item.shift_id,
                        shift_status: item.shift_status,
                        jobId: item.job,
                        run: item.run,
                        vehicleNumberPlate: item.numberPlate,
                        shiftDuration: item.shift_duration
                    },
                    breaks: []
                };
        
                daysOfWeek[dayOfWeek].push(shifts[item.shift_id]);
            }
            if(item.break_id != null){
                shifts[item.shift_id].breaks.push({
                    break_id: item.break_id,
                    break_start: item.break_start,
                    break_finish: item.break_finish,
                    break_duration: item.break_duration,
                    break_is_paid: item.break_is_paid,
                    break_status: item.break_status,
                });
            }
        });
        
        console.log(daysOfWeek);
        shifts = {};
        populateTable();
     
    }


 

});