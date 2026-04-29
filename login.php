<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password']; // Plain text as per your setup
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // 1. Check if user exists
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // --- SIGN IN LOGIC ---
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on the role FOUND in the database
            header("Location: " . $user['role'] . ".php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        // --- AUTO-REGISTER LOGIC ---
        $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $password, $role);
        
        if ($insert->execute()) {
            $_SESSION['user_id'] = $insert->insert_id; // Using the ID just created
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect based on the role SELECTED in the form
            header("Location: " . $role . ".php");
            exit();
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PQMS Access</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        input, select, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; cursor: pointer; font-weight: bold; }
        .err { color: red; font-size: 13px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hospital Access</h2>
        <?php if($error) echo "<p class='err'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <label style="font-size: 12px; color: #666;">Role (New users only):</label>
            <select name="role">
                <option value="nurse">Nurse</option>
                <option value="doctor">Doctor</option>
            </select>
            <button type="submit">Continue</button>
        </form>
    </div>
</body>
</html>