<?php
// 1. Force errors to show if the connection fails (prevents blank page)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 2. Use '127.0.0.1' instead of 'localhost' to force Port 3307 usage
$host   = "127.0.0.1"; 
$user   = "root";   
$pass   = "";       
$dbname = "PQMS_db";   
$port   = 3307; 

try {
    // 3. Establish connection
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    // 4. Success check (Silent)
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // 5. If it fails, it will now tell you WHY instead of a blank page
    die("Database Connection Error: " . $e->getMessage());
}