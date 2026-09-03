<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$log_file = __DIR__ . '/../email_log.txt';
$log_content = file_exists($log_file) ? file_get_contents($log_file) : 'No emails sent yet.';

// Get email backups
$backup_dir = __DIR__ . '/../email_backups/';
$backups = file_exists($backup_dir) ? array_reverse(glob($backup_dir . '*.html')) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Log - Active World</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0A0A0A; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #F26419; margin-bottom: 20px; }
        .log-box { background: #111318; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        pre { background: #1a1a1a; padding: 15px; border-radius: 5px; overflow-x: auto; font-family: monospace; color: #0f0; }
        .backup-list { background: #111318; padding: 20px; border-radius: 10px; }
        .backup-item { padding: 10px; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; }
        .backup-item a { color: #F26419; text-decoration: none; }
        .backup-item a:hover { text-decoration: underline; }
        .back-btn { background: #F26419; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <h1>📧 Email Notification Log</h1>
        
        <div class="log-box">
            <h2>Email History</h2>
            <pre><?php echo htmlspecialchars($log_content); ?></pre>
        </div>
        
        <div class="backup-list">
            <h2>Email Backups (Sent Emails)</h2>
            <?php if (count($backups) > 0): ?>
                <?php foreach ($backups as $backup): ?>
                <div class="backup-item">
                    <span><?php echo basename($backup); ?></span>
                    <a href="<?php echo $backup; ?>" target="_blank">📧 View Email</a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No email backups yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>