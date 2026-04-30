<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host   = "127.0.0.1";
$user   = "root";
$pass   = "";
$dbname = "PQMS_db";
$port   = 3307;

try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // It's helpful to keep the error message during development
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
