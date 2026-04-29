<?php
// 1. Session & Security
session_start();

// Redirect to login if the user isn't logged in at all
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// 2. Database Connection
include_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Patient Queue | PQMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="10"> 
    <style>
        body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 800px; margin: auto; text-align: center; }
        
        h1 { color: #2c3e50; margin-bottom: 5px; font-size: 2.5em; }
        .subtitle { color: #7f8c8d; margin-bottom: 30px; font-style: italic; }

        .queue-grid { display: grid; gap: 15px; }

        .queue-card { 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            transition: transform 0.2s;
        }

        /* Styling for the patient currently with the doctor */
        .current { 
            border-left: 15px solid #007bff; 
            background: #eef7ff; 
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0,123,255,0.15);
        }

        /* Styling for those waiting */
        .waiting { border-left: 15px solid #ffc107; }

        .q-number { font-size: 1.8em; font-weight: bold; color: #2c3e50; }
        .p-name { font-size: 1.2em; color: #555; }

        .status-badge { 
            padding: 8px 15px; 
            border-radius: 50px; 
            font-size: 0.85em; 
            font-weight: bold; 
            text-transform: uppercase;
            color: white;
        }
        .bg-blue { background-color: #007bff; animation: pulse 2s infinite; }
        .bg-orange { background-color: #ffc107; color: #856404; }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .empty-state { padding: 50px; color: #95a5a6; font-size: 1.2em; }
        .footer-nav { margin-top: 40px; }
        .footer-nav a { color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Outpatient Queue</h1>
    <p class="subtitle">Fairness & Transparency in Action</p>

    <div class="queue-grid">
        <?php
        // Fetch patients who are not 'completed' yet
        $sql = "SELECT q.queue_number, p.name, q.status 
                FROM queue q 
                JOIN patients p ON q.patient_id = p.id 
                WHERE q.status != 'completed' 
                ORDER BY q.queue_number ASC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $isConsulting = ($row['status'] == 'in consultation');
                $statusClass = $isConsulting ? 'current' : 'waiting';
                $badgeColor = $isConsulting ? 'bg-blue' : 'bg-orange';
                $displayStatus = $isConsulting ? 'Now Calling' : 'Waiting';
                
                echo "<div class='queue-card $statusClass'>";
                echo "    <div style='text-align: left;'>";
                echo "        <div class='q-number'>#" . $row['queue_number'] . "</div>";
                echo "        <div class='p-name'>" . htmlspecialchars($row['name']) . "</div>";
                echo "    </div>";
                echo "    <div>";
                echo "        <span class='status-badge $badgeColor'>" . $displayStatus . "</span>";
                echo "    </div>";
                echo "</div>";
            }
        } else {
            echo "<div class='empty-state'>🎉 All caught up! No patients in the queue.</div>";
        }
        ?>
    </div>

    <div class="footer-nav">
        <a href="index.php">← Back to Portal</a>
    </div>
</div>

</body>
</html>