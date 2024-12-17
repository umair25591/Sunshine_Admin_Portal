const urlParams = new URLSearchParams(window.location.search);
const vehicleId = urlParams.get('vehicleId');


function convertToShort(data) {
    if (data == 'Yes') {
        return 'Y';
    }
    else if (data == 'No') {
        return 'N';
    }
    else if (data == 'Not Needed') {
        return 'NR'
    }
}


function getVehicleData(vehicleId, callback) {
    $.ajax({
        url: 'PHP/getVehicleHistory.php',
        method: 'POST',
        data: { vehicleId: vehicleId },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            if (response.status == 'success') {

                var lastOdometerReading = null;
                let finalData = [];

                let sortedData = response.data.sort((a, b) => b.vehicleAllocationId - a.vehicleAllocationId);

                console.log(sortedData);

                sortedData.forEach(element => {

                    let driverName = element.driverName;
                    let start = element.start;
                    let finish = element.finish;
                    let fuel = 0;
                    let toilet = '-';
                    let vaccum = '-';
                    let washed = '-';
                    let windscreen = '-';
                    let tyres = '-';
                    let rims = '-';
                    let acFilter = '-';
                    let washerFluid = '-';
                    let averageConsumption = '-';
                    let fuelLitres = '-';

                    if (start && finish) {

                        let startTime = new Date(start);
                        let finishTime = new Date(finish);
                        let diffMs = finishTime - startTime;

                        let totalMinutes = Math.floor(diffMs / (1000 * 60));
                        let totalHours = Math.floor(totalMinutes / 60);
                        minutes = totalMinutes % 60;

                        hours = totalHours > 0 ? `${totalHours} hr` : '';
                        minutes = minutes > 0 || totalHours > 0 ? `${minutes} min` : '';

                        let startFormattedDate = startTime.toLocaleDateString(); // e.g., "2024-11-15"
                        let startFormattedTime = startTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); // e.g., "16:54"

                        let finishFormattedDate = finishTime.toLocaleDateString();
                        let finishFormattedTime = finishTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                        var startFormatted = `${startFormattedDate}<br>${startFormattedTime}`;
                        var finishFormatted = `${finishFormattedDate}<br>${finishFormattedTime}`;
                    }

                    

                    let odometer = element.lastOdometerReading ? element.lastOdometerReading : '-';
                    let mileage = element.lastOdometerReading;

                    for (let i = 0; i < sortedData.length; i++) {
                        let secondElement = sortedData[i];
                        
                        // Ensure we are comparing with a previous vehicle allocation
                        if (element.vehicleAllocationId > secondElement.vehicleAllocationId && secondElement.lastOdometerReading) {
                            console.log("previous : " + secondElement.lastOdometerReading + " Current : " + odometer);
                
                            // Calculate mileage
                            mileage = odometer - secondElement.lastOdometerReading;
                            console.log("mileage : " + mileage);
                            break; // Stop after finding the first previous reading
                        }
                    }
                    // if (lastOdometerReading !== null && odometer !== '-') {
                    //     mileage = lastOdometerReading - odometer;
                    // }
                    // else{
                    //     mileage = odometer;
                    // }

                    // if (odometer !== '-') {
                    //     lastOdometerReading = odometer;
                    // }

                    let timeDifference = (hours || minutes) ? `${hours} ${minutes}`.trim() : '-';

                    // console.log(`Driver: ${driverName}, Time: ${timeDifference}, Mileage: ${mileage} Odometer: ${odometer}`);

                    element.vehicleDetails.forEach(detail => {

                        if (detail.returnChecklist === '1') {
                            fuel = detail.otherValue;
                            fuelLitres = detail.fuelLitres;
                        }
                        if (detail.returnChecklist === '2') {
                            washed = convertToShort(detail.value);
                        }
                        if (detail.returnChecklist === '3') {
                            toilet = convertToShort(detail.value);
                        }
                        if (detail.returnChecklist === '4') {
                            vaccum = convertToShort(detail.value);
                        }
                        if (detail.returnChecklist === '5') {
                            windscreen = convertToShort(detail.value);
                        }
                        if (detail.returnChecklist === '6') {
                            tyres = convertToShort(detail.value);
                        }
                        if (detail.returnChecklist === '7') {
                            rims = convertToShort(detail.value);
                        }
                        if (detail.returnChecklist === '8') {
                            acFilter = convertToShort(detail.value);
                        }
                        if (detail.returnChecklist === '9') {
                            washerFluid = convertToShort(detail.value);
                        }
                    });

                    let vehicleAverageString = element.averageVehicleEfficiency;
                    let vehicleAverage = parseFloat(vehicleAverageString);
                    let driverAverage = element.averageDriverEfficiency;

                    averageConsumption = (fuelLitres / mileage).toFixed(2);

                    console.log(averageConsumption);

                    let consumptionDifference = ((vehicleAverage - averageConsumption) / vehicleAverage * 100).toFixed(2);
                    consumptionDifference = consumptionDifference > 0 ? `+${consumptionDifference}` : consumptionDifference;



                    finalData.push({
                        start: startFormatted,
                        finish: finishFormatted,
                        driverName: driverName,
                        fuel: fuel,
                        toilet: toilet,
                        vaccum: vaccum,
                        washed: washed,
                        windscreen: windscreen,
                        tyres: tyres,
                        rims: rims,
                        acFilter: acFilter,
                        washerFluid: washerFluid,
                        odometer: odometer,
                        mileage: mileage,
                        time: timeDifference,
                        driverAverage: driverAverage,
                        vehicleAverage: vehicleAverageString,
                        consumptionPercentage: consumptionDifference
                    });

                });

                callback(finalData);

            }
            else {
                const tableBody = $('#VehiclesData');
                tableBody.empty();
                const row = `<tr>
                                <td colspan="16">${response.message}</td>
                            </tr>`;
                tableBody.append(row);

            }
        },
        error: function (err) {
            console.error(err);
        }
    });
}



function showData(arr) {
    const tableBody = $('#VehiclesData');
    tableBody.empty(); // Clear previous table rows

    arr.forEach((item) => {
        const row = `<tr>
                        <td>${item.start}</td>
                        <td>${item.finish}</td>
                        <td>${item.driverName}</td>
                        <td>${item.fuel}%</td>
                        <td>${item.toilet}</td>
                        <td>${item.vaccum}</td>
                        <td>${item.washed}</td>
                        <td>${item.windscreen}</td>
                        <td>${item.tyres}</td>
                        <td>${item.rims}</td>
                        <td>${item.acFilter}</td>
                        <td>${item.washerFluid}</td>
                        <td>${item.odometer}</td>
                        <td>${item.mileage}</td>
                        <td>${item.time}</td>
                        <td>${item.vehicleAverage}<br>${item.driverAverage}<br>${item.consumptionPercentage}%</td>
                    </tr>`;
        tableBody.append(row); // Append the row to the table
    });
}

$(document).ready(function () {
    getVehicleData(vehicleId, function (finalData) {
        showData(finalData);
        console.log("Original Data:", finalData);

        // let sortedDataAscStart = sortData(finalData, 'start', 'asc');
        // console.log("Sorted by start (asc):", sortedDataAscStart);

        // let sortedDataDescFinish = sortData(finalData, 'finish', 'desc');
        // console.log("Sorted by finish (desc):", sortedDataDescFinish);
    });
});





function sortData(data, field, order) {
    return data.sort((a, b) => {
        let dateA = new Date(a[field]);
        let dateB = new Date(b[field]);

        if (order === 'asc') {
            return dateA - dateB;
        } else if (order === 'desc') {
            return dateB - dateA
        } else {
            return 0;
        }
    });
}