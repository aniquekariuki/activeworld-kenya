<?php
// Customer Event Status Check Page
$message = '';
$event = null;
$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

if ($conn->connect_error) {
    $message = "Connection failed. Please try again later.";
}

if (isset($_GET['ref']) || isset($_POST['reference'])) {
    $reference = isset($_POST['reference']) ? $_POST['reference'] : $_GET['ref'];
    
    // Extract ID from reference (e.g., AW00018 -> 18)
    $id = str_replace('AW', '', strtoupper($reference));
    $id = ltrim($id, '0');
    
    if (is_numeric($id)) {
        $stmt = $conn->prepare("SELECT * FROM event_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        
        if (!$event) {
            $message = "No event found with reference: " . htmlspecialchars($reference);
        }
    } else {
        $message = "Invalid reference format. Please enter a valid reference number (e.g., AW00018)";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Event Status - Active World Kenya</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #0A0A0A 0%, #1a1a2e 100%);
            color: white; 
            min-height: 100vh; 
        }
        
        .navbar { background: #111318; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .logo span { color: #F26419; }
        .nav-links a { color: white; text-decoration: none; margin-left: 25px; transition: color 0.3s; }
        .nav-links a:hover { color: #F26419; }
        
        .container { max-width: 600px; margin: 60px auto; padding: 20px; }
        
        .status-card { background: rgba(24, 28, 36, 0.95); backdrop-filter: blur(10px); border-radius: 20px; padding: 40px; text-align: center; border: 1px solid rgba(255,255,255,0.1); }
        .status-card h1 { color: #F26419; margin-bottom: 10px; }
        .status-card p { color: #aaa; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; }
        input { width: 100%; padding: 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; font-size: 16px; text-align: center; letter-spacing: 1px; }
        input:focus { outline: none; border-color: #F26419; }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #F26419, #e0550a); border: none; border-radius: 10px; color: white; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s; }
        button:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(242,100,25,0.3); }
        
        .result-box { margin-top: 30px; padding: 25px; border-radius: 15px; text-align: left; background: rgba(255,255,255,0.05); }
        .detail-row { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .detail-label { font-weight: bold; color: #F26419; min-width: 100px; display: inline-block; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; }
        .status-pending { background: #f39c12; }
        .status-approved { background: #27ae60; }
        .status-completed { background: #3498db; }
        .status-message { margin-top: 15px; padding: 10px; border-radius: 8px; text-align: center; }
        .error-message { background: rgba(229,57,53,0.2); border: 1px solid #E53935; padding: 15px; border-radius: 10px; text-align: center; margin-top: 20px; }
        
        .footer { background: #111318; text-align: center; padding: 30px; margin-top: 60px; }
        
        @media (max-width: 768px) {
            .container { margin: 30px auto; }
            .status-card { padding: 25px; }
            .nav-links a { margin-left: 15px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><span>🌍 ACTIVE</span> WORLD</div>
        <div class="nav-links">
            <a href="../index.html">Home</a>
            <a href="../about.html">About</a>
            <a href="../services.html">Services</a>
            <a href="../contact.html">Contact</a>
            <a href="../reviews.php">Reviews</a>
        </div>
    </nav>

    <div class="container">
        <div class="status-card">
            <h1>🔍 Check Your Event Status</h1>
            <p>Enter the reference number you received in your email</p>
            
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="reference" placeholder="Enter Reference Number (e.g., AW00018)" required autofocus>
                </div>
                <button type="submit">Check Status →</button>
            </form>
            
            <?php if ($message): ?>
                <div class="error-message">
                    ⚠️ <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($event): ?>
                <div class="result-box">
                    <h3>📋 Event Details</h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">Reference:</span>
                        <span>AW<?php echo str_pad($event['id'], 5, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Event Type:</span>
                        <span><?php echo htmlspecialchars($event['event_type']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Event Date:</span>
                        <span><?php echo $event['event_date'] ? date('F d, Y', strtotime($event['event_date'])) : 'To be confirmed'; ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Venue:</span>
                        <span><?php echo htmlspecialchars($event['venue'] ?: 'To be confirmed'); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Current Status:</span>
                        <span class="status-badge status-<?php echo $event['status']; ?>">
                            <?php echo strtoupper($event['status']); ?>
                        </span>
                    </div>
                    
                    <?php if ($event['status'] == 'pending'): ?>
                        <div class="status-message" style="background: rgba(243,156,18,0.2); border: 1px solid #f39c12;">
                            ⏳ Your request is pending review. Our team will update you within 24 hours.
                        </div>
                    <?php elseif ($event['status'] == 'approved'): ?>
                        <div class="status-message" style="background: rgba(39,174,96,0.2); border: 1px solid #27ae60;">
                            ✅ Congratulations! Your event has been APPROVED! Our team will contact you shortly to finalize the arrangements.
                        </div>
                    <?php elseif ($event['status'] == 'completed'): ?>
                        <div class="status-message" style="background: rgba(52,152,219,0.2); border: 1px solid #3498db;">
                            🎉 Your event has been COMPLETED! Thank you for choosing Active World Kenya. We hope you had a wonderful experience!
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 Active World Kenya. All rights reserved.</p>
        <p style="margin-top: 10px;">📞 +254 717 038 890 | ✉️ info@activeworld.co.ke</p>
    </footer>
</body>
</html>
<?php if(isset($conn)) $conn->close(); ?>