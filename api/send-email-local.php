<?php
function sendEmailLocal($to, $subject, $htmlContent) {
    // Save email to a file for testing
    $email_log = __DIR__ . '/../email_logs/' . date('Y-m-d_H-i-s') . '_' . md5($to) . '.html';
    
    if (!file_exists(__DIR__ . '/../email_logs')) {
        mkdir(__DIR__ . '/../email_logs', 0777, true);
    }
    
    $fullEmail = "
    <html>
    <head><title>$subject</title></head>
    <body>
        <h2>To: $to</h2>
        <h2>Subject: $subject</h2>
        <hr>
        $htmlContent
    </body>
    </html>
    ";
    
    file_put_contents($email_log, $fullEmail);
    
    // Also log to a text file
    $log = __DIR__ . '/../email_logs/sent_emails.txt';
    $logEntry = date('Y-m-d H:i:s') . " - Email to: $to - Subject: $subject - Saved: $email_log\n";
    file_put_contents($log, $logEntry, FILE_APPEND);
    
    return true;
}

// Use this function instead of mail()
// sendEmailLocal($to, $subject, $customerMessage);
?>