<?php
// 1. Temporary Error Reporting (Remove this once it works!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if database connection exists
    if (!$conn) {
        die("Database connection failed. Check your db.php file.");
    }

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    // Use a fallback role if none is provided in the form
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : 'nurse';

    // 2. Check if the user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // --- BRANCH A: SIGN IN (User Exists) ---
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Direct Redirect
            $target = ($user['role'] === 'doctor') ? "doctor.php" : "nurse.php";
            header("Location: " . $target);
            exit();
        } else {
            header("Location: login.php?error=Incorrect password");
            exit();
        }
    } else {
        // --- BRANCH B: AUTO-REGISTER (New User) ---
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $hashed_password, $role);
        
        if ($insert->execute()) {
            $_SESSION['user_id'] = $insert->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Direct Redirect
            $target = ($role === 'doctor') ? "doctor.php" : "nurse.php";
            header("Location: " . $target);
            exit();
        } else {
            // If the query fails, this will tell you why instead of a blank page
            die("Execution failed: " . $insert->error);
        }
    }
} else {
    // If someone tries to visit process_reg.php directly in the browser
    header("Location: login.php");
    exit();
}
?>