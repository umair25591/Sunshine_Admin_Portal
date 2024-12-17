var driversData;
var jobsData;
var driversForCallData;
var vehicleAndStatusData;

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
        formattedTime = `${hours} h ${minutes} min`;
    } else if (minutes > 0) {
        formattedTime = `${minutes} min`;
    } else {
        formattedTime = `${seconds} sec`;
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



function showGetDriversData(driverList){
        var driverTable = $("#driversData");
        driverTable.empty();

        if(driverList.length > 0){

        driverList.forEach(element => {

            let addFlashing = "";
            let removeBg = `style="background: rgba(0, 0, 0, 0);"`;
            let callableCellColor = '';
            let busCellColor = '';

            var clockoff = "-";
            var startsIn = "-";
            var job = "-";
            var allocatedBus = "-";
            var drivingBus = "-";
            var callable = "-";
            var status = "-";
    
            if(element.clockStatus === "Active"){
                clockoff = "Clocked On";
                status = "Active"
                callable = "Active Job"
            }
            else{
                const clockOutStr = element.clockOut;
                const clockOutDate = new Date(clockOutStr);
                const currentDateTime = new Date();
                const TimeDiff = currentDateTime - clockOutDate
                const formatTime = formatTimeDifference(TimeDiff);
                var totalHours = TimeDiff / (1000 * 60 * 60);
                const clockOuthours = clockOutDate.getHours().toString().padStart(2, '0');
                const clockOutminutes = clockOutDate.getMinutes().toString().padStart(2, '0');
                const clockOutHHMM = `${clockOuthours}:${clockOutminutes}`;
                const totalDays = Math.floor(totalHours / 24);
    
                if(totalHours >= 7){
                    callable = "Yes"
                }
                
                if(totalHours > 48){
                    clockoff = `${totalDays} days`;
                }
                else{
                    clockoff = `${clockOutHHMM} / ${formatTime}`;
                }
            }
    
            if(element.jobStart != null){
                const startDate = element.jobDate;
                const startTime = element.jobStart;
                const jobDateTime = `${startDate}T${startTime}`;
                const jobDateTimeConvert = new Date(jobDateTime)
                const currentTime = new Date();
                const TimeDiffrence = jobDateTimeConvert - currentTime;
                const formatTime = formatTimeDifference(TimeDiffrence);
                const totalMinutes = Math.floor(TimeDiffrence / (1000 * 60));
                const millisecondsPerHour = 1000 * 60 * 60;
                const timeDifferenceHours = TimeDiffrence / millisecondsPerHour;
                const hours = Math.floor(totalMinutes / 60);

    
                if(element.clockStatus !== "Active"){

                    driverList.forEach(e => {
                        if (element.jobVehicle!= null && element.jobVehicle === e.jobVehicle && element !== e) {
                            callableCellColor = `style="background : orange; color: black;"`;
                            busCellColor = `style="background : orange; color: black;"`;

                            status = "Caution";
                            if(totalHours >= 7){
                                callable = "Yes - Conflict"
                            }
                            else{
                                callable = "Conflict";
                            }
                        }
                    });

                    if(element.jobVehicleStatus === "Workshop"){
                        callableCellColor = `style="background : yellow; color: black;"`;
                        status = "Caution";
                        if(totalHours >= 7){
                            callable = "Yes - Conflict"
                        }
                        else{
                            callable = "Conflict";
                        }
                    }
                        if(timeDifferenceHours < 2){
                            status = "Watch";
                            if(totalMinutes < 5){
                                status = "Warn";
                                if(TimeDiffrence < 0){
                                    status = "Alert";
                                }
                            }
                        }
                        else if(hours > 16){
                            status = "Ok";
                        }
                }
    
                startsIn = formatTime;
    
                job = element.run
    
                allocatedBus = element.jobVehicle;
            
            }
            else{
                if(element.clockStatus !== "Active"){
                    status = "Ok"
                }
                else{
                    callable = "Clocked on - No Active job"
                }
            }
    
            if(element.vehicleDriving != null){
                drivingBus = element.vehicleDriving;
            }
    
            const allocatedDrivingBus = (element.vehicleDriving === null && element.jobVehicle === null) ? `-` : `${allocatedBus} / ${drivingBus}`;
    
            let driverStatus;
            switch (status) {
                case "Active":
                    driverStatus = '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">ACTIVE</span>';
                    break;
                case "Watch":
                    driverStatus = '<span class="bg-focus text-dark px-24 py-4 rounded-pill fw-medium text-sm" style="background: #feff67;">WATCH</span>';
                    break;
                case "Ok":
                    driverStatus = '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">OK</span>';
                    break;
                case "DayOff":
                    driverStatus = '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">DAY OFF</span>';
                    break;
                case "Caution":
                    driverStatus = '<span class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">CAUTION</span>';
                    break;
                case "Warn":
                    driverStatus = '<span class="bg-focus text-white px-24 py-4 rounded-pill fw-medium text-sm" style="background: #d90073;">WARN</span>';
                    break;
                case "Alert":
                    driverStatus = '<span class="bg-focus text-white px-24 py-4 rounded-pill fw-medium text-sm" style="background: #6a00ff;">ALERT</span>';
                    break;
                default:
                    driverStatus = '<span class="bg-muted text-muted px-24 py-4 rounded-pill fw-medium text-sm">-</span>';
                    break;
            }


    
            
            if (status === "Alert") {
                addFlashing = "flash-red-black";
                busCellColor = `style="background: rgba(0, 0, 0, 0);"`;
                callableCellColor = `style="background: rgba(0, 0, 0, 0);"`;
            }
    
            const driverTemplate = `
                <tr class="${addFlashing}">
                    <td ${removeBg}>${element.firstName} ${element.lastName}</td>
                    <td ${removeBg}>${clockoff}</td>
                    <td ${removeBg}>${startsIn}</td>
                    <td ${removeBg}>${job}</td>
                    <td ${busCellColor}>${allocatedDrivingBus}</td>
                    <td ${callableCellColor}>${callable}</td>
                    <td ${removeBg}>${driverStatus}</td>
                </tr>`;
    
          driverTable.append(driverTemplate);
    
        });

    }
}
function getDriverDataAjax() {
    $.ajax({
        url: 'PHP/getDriversData.php',
        method: 'POST',
        dataType: 'json',
        success: function(response){
            // console.log(response);
            if(response.status === "success"){
                driversData =  response.data 
            }
            else if(response.status === "warning"){
                var driverTable = $("#driversData");
                driverTable.empty();
            }
        },
        error: function(err){
            console.error(err);
        }
    });
}



function showJobsData(jobsList){
    var jobTable = $("#jobsTable");
    jobTable.empty();

    jobsList.forEach(element => {
        var customer = element.customerName;
        var run = element.run;
        var day = "-";
        var start = "-";
        var finish = "-";
        var hours = "-";
        var driver = "-";
        var bus = "-";
        var status = "-";
        var startsIn = "-"

        if (element.jobDate != null) {
            const jobDate = new Date(element.jobDate);
            const currentDate = new Date();
            currentDate.setHours(0, 0, 0, 0);
            jobDate.setHours(0, 0, 0, 0)
        
            const tomorrow = new Date(currentDate);
            const yesterday = new Date(currentDate);
        
            tomorrow.setDate(currentDate.getDate() + 1);
            yesterday.setDate(currentDate.getDate() - 1);
        
            if (jobDate.getTime() === currentDate.getTime()) {
                day = "Today";
            } 
            else if (jobDate.getTime() === tomorrow.getTime()) {
                day = "Tomorrow";
            } 
            else if (jobDate.getTime() === yesterday.getTime()) {
                day = "Yesterday";
            } 
            else {
                // console.log("The job date is neither today, tomorrow, nor yesterday.");
            }
        }

        const jobStartHHMM = element.jobStart.split(':').slice(0, 2).join(':');
        const jobFinishHHMM = element.jobFinish.split(':').slice(0, 2).join(':');

        const startInMinutes = convertToMinutes(jobStartHHMM);
        const finishInMinutes = convertToMinutes(jobFinishHHMM);

        let durationInMinutes = finishInMinutes - startInMinutes;

        if (durationInMinutes < 0) {
            durationInMinutes += 24 * 60;
        }

        const durationHours = Math.floor(durationInMinutes / 60);
        const durationMinutes = durationInMinutes % 60;
        
        start = jobStartHHMM;
        finish = jobFinishHHMM;
        hours = `${durationHours} h ${durationMinutes} min`;
        driver = `${element.driverFirstName} ${element.driverLastName}`;
        if(element.jobVehicle != null){
            bus = element.vehicleNumberPlate;
        }

        const jobStartDate = element.jobDate;
        const jobStartTime = element.jobStart;
        const jobDateTime = `${jobStartDate}T${jobStartTime}`; 

        const jobDateTimeConvert = new Date(jobDateTime);

        const currentTime = new Date();

        const timeDifference = jobDateTimeConvert - currentTime;
        const totalMinutes = Math.floor(timeDifference / (1000 * 60));
        const formatTimeDiffrence = formatTimeDifference(timeDifference);

        startsIn = formatTimeDiffrence;
        

            if(element.jobStatus == "Pending"){
                if(element.jobDriver != null && element.jobVehicle != null){
                    if(timeDifference < 0){
                        status = "Late";
                    }
                    else if(totalMinutes < 5){
                        status = "Warn";
                    }
                    else if (totalMinutes < 30) {
                        status = "Caution";
                    }
                    else{
                        status = "Ok";
                    }
                }
                else{
                    status = "Awaiting"
                }
                

            }
            else if(element.jobStatus == "Active"){
                status = "Active";
            }

            // console.log(status);


            let jobStatus;
        switch (status) {
            case "Active":
                jobStatus = '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">ACTIVE</span>';
                break;
            case "Ok":
                jobStatus = '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">OK</span>';
                break;
            case "Caution":
                jobStatus = '<span class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">CAUTION</span>';
                break;
            case "Warn":
                jobStatus = '<span class="bg-focus text-white px-24 py-4 rounded-pill fw-medium text-sm" style="background: #d90073;">WARN</span>';
                break;
            case "Late":
                jobStatus = '<span class="bg-danger-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm">LATE</span>';
                break;
            case "Awaiting":
                jobStatus = '<span class="bg-focus text-dark px-24 py-4 rounded-pill fw-medium text-sm" style="background: #e2c900;">AWAITING ALLOCATION</span>';
                break;
            default:
                jobStatus = '<span class="bg-muted text-muted px-24 py-4 rounded-pill fw-medium text-sm">-</span>';
                break;
        }


            const jobTemplate = `<tr>
                <td>${customer}</td>
                <td>${run}</td>
                <td>${day}</td>
                <td>${start}</td>
                <td>${finish}</td>
                <td>${hours}</td>
                <td>${driver}</td>
                <td>${bus}</td>
                <td>${jobStatus}</td>
                <td>yes</td>
                <td>${startsIn}</td>
            </tr>`


            jobTable.append(jobTemplate);
    });
}
function getJobsDataAjax() {
    $.ajax({
        url: 'PHP/getJobsData.php',
        method: 'POST',
        dataType: 'json',
        success: function(response){
            console.log(response);
            if(response.status === "success"){
                jobsData = response.data;
            }
            else if(response.status === "warning"){
                var jobTable = $("#jobsTable");
                jobTable.empty();
            }
        },
        error: function(err){
            console.error(err);
        }
    });
}



function showDriverForCall(driverForCallList) {
    var callableTable = $("#callableData");
    callableTable.empty();

    let driversWithJobTimes = [];

    driverForCallList.forEach(element => {
        if (element.shiftStatus == "Inactive") {
            const clockOut = new Date(element.clockOut);
            const currentTime = new Date();

            const restTimeMillis = currentTime - clockOut;
            const formatedRestTime = formatTimeDifference(restTimeMillis);
            const restTimeHours = restTimeMillis / (1000 * 60 * 60);

            const totalMinutes = Math.floor(restTimeMillis / (1000 * 60));
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;

            const restTimeFormatted = `${String(hours).padStart(2, '0')}H ${String(minutes).padStart(2, '0')}M`;

            let timeLeftHours = null;

            if (element.jobId != null) {
                const jobDate = element.jobDate;
                const jobStartTime = element.jobStartTime;
                const nextJobDateTime = new Date(`${jobDate}T${jobStartTime}`);
                const timeLeftMillis = nextJobDateTime - currentTime;
                timeLeftHours = timeLeftMillis / (1000 * 60 * 60);
            }

            driversWithJobTimes.push({
                driver: `${element.firstName} ${element.lastName}`,
                timeLeftHours: timeLeftHours,
                restTimeFormatted: formatedRestTime,
                restTimeHours: restTimeHours,  // renamed to 'restTimeHours' for clarity
                mobile: element.mobile
            });
        }
    });

    // Sort by restTimeHours in descending order
    driversWithJobTimes.sort((a, b) => b.restTimeHours - a.restTimeHours);

    driversWithJobTimes.forEach(driverData => {
        const driver = driverData.driver;
        const restTimeFormatted = driverData.restTimeFormatted;
        const mobile = driverData.mobile;
        const restHours = driverData.restTimeHours;

        if (restHours > 7) {
            const callableTemplate = `
            <tr>
                <td>${driver}</td>
                <td>${restTimeFormatted}</td>
                <td>${mobile}</td>
            </tr>`;

            callableTable.append(callableTemplate);
        }
    });
}

function getDriverForCallAjax() {
    $.ajax({
        url: 'PHP/getDriversForCallable.php',
        method: 'POST',
        dataType: 'json',
        success: function(response){
            // console.log(response);
            if(response.status == "success") {
                driversForCallData = response.data
            }
            else if(response.status === "warning"){
                var callableTable = $("#callableData");
                callableTable.empty();
            }
        },
        error: function(err){
            console.error(err);
        }
    });
}



function showVehicleAndStatus(vehicleAndStatus) {
    var vehicleTable = $("#vehiclesData");
    vehicleTable.empty();

    vehicleAndStatus.sort((a, b) => {
        const regoA = a.numberPlate.match(/(\d+)|(\D+)/g);
        const regoB = b.numberPlate.match(/(\d+)|(\D+)/g);

        for (let i = 0; i < Math.max(regoA.length, regoB.length); i++) {
            if (regoA[i] && regoB[i]) {
                const numA = parseInt(regoA[i], 10);
                const numB = parseInt(regoB[i], 10);

                if (!isNaN(numA) && !isNaN(numB)) {
                    if (numA !== numB) return numB - numA;
                }
                else if (isNaN(numA) && isNaN(numB)) {
                    if (regoA[i] !== regoB[i]) return regoA[i].localeCompare(regoB[i]);
                }
                else {
                    return isNaN(numA) ? 1 : -1;
                }
            }
        }
        return 0;
    });

    vehicleAndStatus.forEach(element => {
        var rego = element.numberPlate;
        var status = "-";
        var etr = "-";
        var eto = "-";

        if(element.vehicleStatus == "Workshop"){
            status = "Workshop";
        }
        else{
            if(element.allocationStatus == "Active"){
                status = `${element.allocationDriverFirstName} ${element.allocationDriverLastName}`;
            }
            else if(element.futureJobStatus == "Pending"){
                status = `Depot - ${element.futureJobDriverFirstName} ${element.futureJobDriverLastName}`;
            }
            else{
                status = "Depot";
            }
        }

        if (element.currentJobDate && element.currentJobStartTime && element.currentJobFinishTime) {
            const dueReturnDateTime = new Date(`${element.currentJobDate}T${element.currentJobFinishTime}`);
            const currentTime = new Date();
            const timeUntilReturnMillis = dueReturnDateTime - currentTime;
            etr = formatTimeDifference(timeUntilReturnMillis);
        }

        if (element.futureJobDate && element.futureJobStartTime) {
            const dueGoOutDateTime = new Date(`${element.futureJobDate}T${element.futureJobStartTime}`);
            const currentTime = new Date();
            const timeUntilGoOutMillis = dueGoOutDateTime - currentTime;
            eto = formatTimeDifference(timeUntilGoOutMillis);
        }

        let formattedETRETO = `${etr} / ${eto}`;
        if(etr == "-" && eto == "-"){
            formattedETRETO = "-";
        }

        let addYellow = "";
            let removeBg = ""
            if (status === "Workshop") {
                addYellow = "showYellow";
                removeBg = `style="background: rgba(0, 0, 0, 0); color: black;"`
            }

        const vehicleTemplate = `
            <tr class="${addYellow}">
                <td ${removeBg}>${rego}</td>
                <td ${removeBg}>${status}</td>
                <td ${removeBg}>${formattedETRETO}</td>
            </tr>
        `;

        vehicleTable.append(vehicleTemplate);
    });
}
function getVehicleAndStatusAjax(){
    $.ajax({
        url: 'PHP/getVehicleAndthierStatus.php',
        method: 'POST',
        dataType: 'json',
        success: function(response){
            // console.log(response);
            if(response.status == "success"){
                vehicleAndStatusData = response.data
            }
            else if(response.status === "warning"){
                var vehicleTable = $("#vehiclesData");
                vehicleTable.empty();
            }
        },
        error: function(err){
            console.error(err);
        }
    });
}

$(document).ready(function(){


 

    setInterval(getDriverDataAjax, 2000);
    getDriverDataAjax();

    setInterval(() => {
        if(driversData){
            showGetDriversData(driversData);
        }
    }, 1000);


    setInterval(getJobsDataAjax, 2000);
    getJobsDataAjax();

    setInterval(() => {
        if(jobsData){
            showJobsData(jobsData);
        }
    }, 1000);

    setInterval(getDriverForCallAjax, 2000);
    getDriverForCallAjax();

    setInterval(() => {
        if(driversForCallData){
            showDriverForCall(driversForCallData);
        }
    }, 1000);

    setInterval(getVehicleAndStatusAjax, 2000);
    getVehicleAndStatusAjax();

    setInterval(() => {
        if(vehicleAndStatusData){
            showVehicleAndStatus(vehicleAndStatusData);
        }
    }, 1000);



});




