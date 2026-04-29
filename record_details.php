<?php
// 1. Session start must be at the absolute top
session_start();

// 2. Security Guard: Only allow Doctors to record medical data
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}

// 3. Include database connection
include 'db.php';

// 4. Logic to save the record when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $p_id = $_POST['patient_id'];
    $doc_id = $_SESSION['user_id'];
    $diag = $conn->real_escape_string($_POST['diagnosis']);
    $treat = $conn->real_escape_string($_POST['treatment']);

    // Insert into the medical_records table [cite: 105]
    $sql = "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment) 
            VALUES ('$p_id', '$doc_id', '$diag', '$treat')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Medical Record Saved Successfully!'); window.location.href='doctor.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// 5. Get the patient ID from the URL (from the 'Call Patient' link)
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    // Fetch patient name for the header
    $p_res = $conn->query("SELECT name FROM patients WHERE id = $patient_id");
    $p_data = $p_res->fetch_assoc();
} else {
    header("Location: doctor.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PQMS - Record Medical Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; padding: 20px; }
        .form-container { max-width: 600px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { color: #007bff; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        textarea { width: 100%; height: 120px; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; resize: vertical; }
        .btn-save { background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; margin-top: 20px; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Medical Consultation</h2>
    <p><strong>Patient:</strong> <?php echo $p_data['name']; ?></p>
    <hr>
    
    <form method="POST">
        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
        
        [cite_start]<label for="diagnosis">Diagnosis Details [cite: 42, 105]</label>
        <textarea name="diagnosis" id="diagnosis" placeholder="Enter findings and diagnosis..." required></textarea>
        
        [cite_start]<label for="treatment">Treatment Plan [cite: 42, 105]</label>
        <textarea name="treatment" id="treatment" placeholder="Enter prescribed medication or next steps..." required></textarea>
        
        <button type="submit" class="btn-save">Save to Electronic Health Record</button>
    </form>

    <a href="doctor.php" class="btn-cancel">Cancel and Return</a>
</div>

</body>
</html>