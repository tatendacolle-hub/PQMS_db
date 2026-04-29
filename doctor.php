<?php
// 1. Security Check & Session
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}

// 2. Database Connection
require_once 'db.php';

// 3. Action Handler (Call / Done)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $action = $_GET['action'];
    $newStatus = ($action === 'call') ? 'in consultation' : (($action === 'done') ? 'completed' : null);

    if ($newStatus) {
        $stmt = $conn->prepare("UPDATE queue SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();
    }
    
    header("Location: doctor.php");
    exit();
}

// 4. Data Fetching
$sql = "SELECT q.id as queue_id, q.queue_number, q.status, p.id as patient_id, p.name 
        FROM queue q 
        JOIN patients p ON q.patient_id = p.id 
        WHERE q.status != 'completed' 
        ORDER BY q.queue_number ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - PQMS</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        .patient-card { 
            background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 15px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 6px solid #007bff;
        }
        .patient-card.in-consultation { border-left-color: #ffc107; background: #fffdf5; }
        
        .patient-info strong { font-size: 1.2rem; color: #333; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-waiting { background: #e9ecef; color: #495057; }
        .badge-active { background: #fff3cd; color: #856404; }
        
        .queue-number { font-weight: bold; color: #007bff; margin-right: 10px; font-size: 1.2rem; }
        
        .btn-group { display: flex; gap: 8px; }
        .btn { padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; color: white; }
        .btn-call { background: #007bff; }
        .btn-details { background: #17a2b8; }
        .btn-done { background: #28a745; }
        .btn:hover { filter: brightness(90%); }
        
        .empty-state { text-align: center; padding: 50px; background: white; border-radius: 10px; color: #666; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="header">
        <h1>Doctor's Consultation</h1>
        <span style="color: #666;">FCFS Mode Active</span>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): 
            $isActive = ($row['status'] == 'in consultation');
        ?>
            <div class="patient-card <?= $isActive ? 'in-consultation' : '' ?>">
                <div class="patient-info">
                    <span class="queue-number">#<?= $row['queue_number'] ?></span>
                    <strong><?= htmlspecialchars($row['name']) ?></strong>
                    <br>
                    <span class="badge <?= $isActive ? 'badge-active' : 'badge-waiting' ?>">
                        <?= $row['status'] ?>
                    </span>
                </div>

                <div class="btn-group">
                    <?php if (!$isActive): ?>
                        <a href="?action=call&id=<?= $row['queue_id'] ?>" class="btn btn-call">Call Patient</a>
                    <?php endif; ?>
                    
                    <a href="record_details.php?id=<?= $row['patient_id'] ?>&queue_id=<?= $row['queue_id'] ?>" class="btn btn-details">Patient Records</a>
                    
                    <?php if ($isActive): ?>
                        <a href="?action=done&id=<?= $row['queue_id'] ?>" class="btn btn-done">Mark Done</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <h3>No patients in queue</h3>
            <p>You're all caught up! Take a break. ☕</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>