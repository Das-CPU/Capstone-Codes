<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Harvesting Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
        }

        header {
            background-color: #3b5998;
            color: white;
            padding: 20px;
            text-align: center;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #3b5998;
            padding: 10px 0;
        }

        nav a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            text-align: center;
        }

        nav a:hover {
            background-color: #4c70ba;
        }

        .container {
            padding: 20px;
        }

        .button {
            background-color: #3b5998;
            color: white;
            border: none;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        .button:hover {
            background-color: #4c70ba;
        }

        .footer {
            background-color: #3b5998;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
 	.container_graph {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
	table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
 	#sensorChartContainer {
            display: none; /* Initially hide the chart container */
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

<header>
    <h1>Energy Harvesting Statistics</h1>
</header>

<nav>
    <a href="#w_usage">Water Usage</a>
    <a href="#p_gen">Power Generated</a>
    <a href="#p_usage">Power Usage</a>
    <a href="#contact">Contact</a>
</nav>

 <div class="container">
        <h1>Water Usage Chart</h1>
        <button class="button" id="showChartButton">Show Chart</button>
        <div id="sensorChartContainer">
            <canvas id="sensorChart"></canvas>
        </div>
    </div>

    <script>
        let sensorChart = null; // Ensure sensorChart is globally defined
        // Function to fetch data and create the chart
        function fetchDataAndCreateChart() {
            fetch('fetch_graph.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const labels = data.map(entry => entry.created_at);
                    const values = data.map(entry => entry.sensor_value);

                    // If the chart already exists, destroy it before creating a new one
                    if (sensorChart) {
                        sensorChart.destroy();
                    }
		
                    // Create the chart
                    const ctx = document.getElementById('sensorChart').getContext('2d');
                    sensorChart = new Chart(ctx, {
                        type: 'line',
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
                                        unit: 'minute',
                                        tooltipFormat: 'PPpp'
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

                    // Show the chart container
                    document.getElementById('sensorChartContainer').style.display = 'block';
                })
                .catch(error => console.error('Error fetching sensor data:', error));
        }
function Refresh() {
	setInterval(fetchDataAndCreateChart, 20000);
}

        // Add event listener to the button
        document.getElementById('showChartButton').addEventListener('click', fetchDataAndCreateChart);
	document.getElementById('showChartButton').addEventListener('click', Refresh);
    </script>

<div class="container" id="p_gen">
    <h2>Power Generated</h2>

    <button class="button" onclick="alert('Services button clicked!')">Show Chart</button>
</div>

<div class="container" id="p_usage">
    <h2>Power Usage</h2>
    <p>This is the about section of the website.</p>
    <button class="button" onclick="alert('About button clicked!')">Click Me!</button>
</div>

<div class="container" id="contact">
    <h2>Contact</h2>
    <p>This is the contact section of the website.</p>
    <button class="button" onclick="alert('Contact button clicked!')">Click Me!</button>
</div>

<div class="footer">
    <p>&copy; 2024 My Website</p>
</div>

</body>
</html>