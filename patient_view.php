<?php
session_start();

// Security Guard: Only allow Patients to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// In this demo, we assume the logged-in user is linked to a patient record.
// For the assignment, we'll fetch the first patient record associated with this session.
$user_id = $_SESSION['user_id'];
$patient_query = $conn->query("SELECT * FROM patients LIMIT 1"); // Simplification for demo
$patient = $patient_query->fetch_assoc();
$patient_id = $patient['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 15px; }
        .header { background: #007bff; color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .status-box { text-align: center; border: 2px solid #007bff; padding: 15px; border-radius: 10px; background: #e7f1ff; }
        .queue-number { font-size: 48px; font-weight: bold; color: #007bff; display: block; }
        .history-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .history-item:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #555; }
        .btn { display: block; text-align: center; background: #6c757d; color: white; padding: 10px; border-radius: 5px; text-decoration: none; margin-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Welcome, <?php echo $patient['name']; ?></h1>
    <p>Your Health, Our Priority</p>
</div>

<div class="card">
    <h3>Current Queue Status</h3>
    <?php
    $q_res = $conn->query("SELECT queue_number, status FROM queue WHERE patient_id = $patient_id AND status != 'completed' LIMIT 1");
    if ($q_res->num_rows > 0) {
        $q_data = $q_res->fetch_assoc();
        echo "<div class='status-box'>";
        echo "Your Number: <span class='queue-number'>" . $q_data['queue_number'] . "</span>";
        echo "Current Status: <strong>" . strtoupper($q_data['status']) . "</strong>";
        echo "</div>";
    } else {
        echo "<p>You are not currently in the queue.</p>";
    }
    ?>
    <a href="queue.php" class="btn" style="background: #007bff;">View Full Live Queue</a>
</div>

<div class="card">
    <h3>My Medical Records</h3>
    <?php
    $records = $conn->query("SELECT * FROM medical_records WHERE patient_id = $patient_id ORDER BY created_at DESC");
    if ($records->num_rows > 0) {
        while($r = $records->fetch_assoc()) {
            echo "<div class='history-item'>";
            echo "<span class='label'>Date:</span> " . date('M d, Y', strtotime($r['created_at'])) . "<br>";
            echo "<span class='label'>Diagnosis:</span> " . $r['diagnosis'] . "<br>";
            echo "<span class='label'>Treatment:</span> " . $r['treatment'];
            echo "</div>";
        }
    } else {
        echo "<p>No medical records found.</p>";
    }
    ?>
</div>

<a href="logout.php" class="btn">Logout</a>

</body>
</html>