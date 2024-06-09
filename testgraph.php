<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Data Chart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #sensorChart {
            width: 100%;
            height: 400px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
</head>
<body>
    <div class="container">
        <h1>Sensor Data Chart</h1>
        <canvas id="sensorChart"></canvas>
    </div>

    <script>
	let sensorChart;
function fetchDataAndUpdateChart() {
        fetch('fetch_graph.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log(data); // Add this line for debugging
                const labels = data.map(entry => entry.created_at);
                const values = data.map(entry => entry.sensor_value);

 		if (sensorChart) {
                        sensorChart.destroy();
                    }

                const ctx = document.getElementById('sensorChart').getContext('2d');
                sensorChart = new Chart(ctx, {
                    type: 'line', // Change to the type of chart you need
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Sensor Values',
                            data: values,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    unit: 'minute'
                                },
                                title: {
                                    display: true,
                                    text: 'Timestamp'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Sensor Value'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching sensor data:', error));
}
	fetchDataAndUpdateChart();
        setInterval(fetchDataAndUpdateChart, 60000);
    </script>
</body>
</html>
