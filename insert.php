<?php
$servername = "localhost";
$username = "Turbine_ESP32";
$password = ' E5p1@3$S^';
$dbname = "Turbine";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sensor_value = $_POST['sensor_value'];

if (empty($sensor_value)) {
    die("No sensor value received");
}

$stmt = $conn->prepare("INSERT INTO test (sensor_value) VALUES (?)");
$stmt->bind_param("s", $sensor_value);

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>