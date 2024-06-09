<?php
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
$sql = "SELECT id, sensor_value, created_at FROM test";
$result = $conn->query($sql);

// Start output buffering
ob_start();
?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Sensor Value</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["sensor_value"] . "</td><td>" . $row["created_at"] . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No data found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
// Close connection
$conn->close();

// End output buffering and return content
$content = ob_get_clean();
echo $content;
?>