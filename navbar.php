<nav style="background: #333; padding: 10px; color: white; display: flex; justify-content: space-between; align-items: center;">
    <div><strong>Hospital PQMS</strong></div>
    <div>
        <?php if(isset($_SESSION['role'])): ?>
            <span style="margin-right: 15px;">Logged in as: <?php echo ucfirst($_SESSION['role']); ?></span>
            
            <?php if($_SESSION['role'] == 'nurse'): ?>
                <a href="index.php" style="color: white; margin-right: 10px;">Register</a>
            <?php endif; ?>
            
            <?php if($_SESSION['role'] == 'doctor'): ?>
                <a href="doctor.php" style="color: white; margin-right: 10px;">Dashboard</a>
            <?php endif; ?>
            
            <a href="queue.php" style="color: white; margin-right: 10px;">View Queue</a>
            <a href="logout.php" style="background: #dc3545; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;">Logout</a>
        <?php else: ?>
            <a href="login.php" style="color: white;">Login</a>
        <?php endif; ?>
    </div>
</nav>