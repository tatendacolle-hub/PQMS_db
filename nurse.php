<?php
session_start();

// 1. Security Guard: Prevent unauthorized access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'nurse') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nurse Dashboard</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { display: flex; justify-content: space-between; background: #007bff; color: white; padding: 15px; border-radius: 8px; }
        .logout-btn { color: white; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nurse Dashboard</h1>
        <p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> | <a href="logout.php" class="logout-btn">Logout</a></p>
    </div>

    <h2>Patient Registration</h2>
    <form action="process_patient.php" method="POST">
        <input type="text" name="name" placeholder="Patient Name" required>
        <input type="number" name="age" placeholder="Age" required>
        <input type="text" name="contact" placeholder="Contact Number" required>
        <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px; border-radius: 4px;">Add to Queue</button>
    </form>
</body>
</html>