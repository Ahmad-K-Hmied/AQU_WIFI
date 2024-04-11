<?php
// Set header to indicate the content type is JSON with UTF-8 encoding
header('Content-Type: application/json; charset=utf-8');

// Database connection settings from your telemetry_settings.php
$MySql_username = 'aa5c4e_ahmad';
$MySql_password = '0569TEMP@temp';
$MySql_hostname = 'MYSQL9001.site4now.net';
$MySql_databasename = 'db_aa5c4e_ahmad';
$MySql_port = '3306';

// Establish a database connection using PDO with UTF-8 encoding
try {
    $pdo = new PDO("mysql:host=$MySql_hostname;dbname=$MySql_databasename;port=$MySql_port;charset=utf8mb4", $MySql_username, $MySql_password);
    // Set the PDO error mode to exception for better error handling
    $pdo->exec("set names utf8mb4");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Decode the JSON received from the AJAX call
    $data = json_decode(file_get_contents('php://input'), true);
    $locationName = $data['locationName'];

    // Prepare the SQL query to fetch the latest data for the given location
    // Make sure to replace 'your_table_name' with the actual name of your table
    $stmt = $pdo->prepare("SELECT `dl`, `ul`, `timestamp` FROM speedtest_users WHERE `location` = :locationName ORDER BY `timestamp` DESC LIMIT 1");
    $stmt->bindParam(':locationName', $locationName, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Data found, encode it to JSON and return
        echo json_encode([
            'downloadSpeed' => $result['dl'],
            'uploadSpeed' => $result['ul'],
            'timestamp' => $result['timestamp']
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // No data found for the location, return an appropriate message
        echo json_encode(['error' => 'No data found for the specified location.'], JSON_UNESCAPED_UNICODE);
    }

} catch(PDOException $e) {
    // Database connection failed, encode the error message to JSON and return
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

?>
