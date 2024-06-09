<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost"; // MySQL server
$username = "Turbine_ESP32"; // MySQL username
$password = ' E5p1@3$S^'; // MySQL password
$dbname = "Turbine"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the sensors table
$sql = "SELECT sensor_value, created_at FROM test";
$result = $conn->query($sql);

$sensor_data = [];

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $sensor_data[] = $row;
    }
} else {
    echo "No data found";
}

// Close connection
$conn->close();

// Convert data to JSON format
echo json_encode($sensor_data);
?>