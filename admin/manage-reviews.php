<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

// Approve review
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE reviews SET is_approved = 1 WHERE id = $id");
    header('Location: manage-reviews.php');
    exit;
}

// Delete review
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM reviews WHERE id = $id");
    header('Location: manage-reviews.php');
    exit;
}

$pending_reviews = $conn->query("SELECT * FROM reviews WHERE is_approved = 0 ORDER BY created_at DESC");
$approved_reviews = $conn->query("SELECT * FROM reviews WHERE is_approved = 1 ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Reviews - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1, h2 { margin: 20px 0; color: #333; }
        .review-card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stars { color: #F5A623; margin: 10px 0; font-size: 18px; }
        .approve-btn { background: #27ae60; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px; }
        .delete-btn { background: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .back-btn { background: #F26419; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
        .review-text { margin: 15px 0; line-height: 1.6; color: #555; }
        .review-meta { color: #888; font-size: 14px; margin-top: 10px; }
        .review-meta strong { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <h1>⭐ Manage Customer Reviews</h1>
        
        <!-- Pending Reviews Section -->
        <h2>Pending Reviews (<?php echo $pending_reviews->num_rows; ?>)</h2>
        <?php if ($pending_reviews->num_rows > 0): ?>
            <?php while ($review = $pending_reviews->fetch_assoc()): ?>
            <div class="review-card">
                <p><strong><?php echo htmlspecialchars($review['customer_name']); ?></strong></p>
                <div class="stars">
                    <?php echo str_repeat('★', $review['rating']); ?>
                    <?php echo str_repeat('☆', 5 - $review['rating']); ?>
                </div>
                <p><strong>Event:</strong> <?php echo htmlspecialchars($review['event_type']); ?></p>
                <p class="review-text">"<?php echo nl2br(htmlspecialchars($review['review_text'])); ?>"</p>
                <p class="review-meta">Submitted: <?php echo date('M d, Y', strtotime($review['created_at'])); ?></p>
                <a href="?approve=<?php echo $review['id']; ?>" class="approve-btn">✅ Approve</a>
                <a href="?delete=<?php echo $review['id']; ?>" class="delete-btn" onclick="return confirm('Delete this review?')">🗑️ Delete</a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #888; margin-bottom: 30px;">No pending reviews.</p>
        <?php endif; ?>
        
        <!-- Approved Reviews Section -->
        <h2>Approved Reviews (<?php echo $approved_reviews->num_rows; ?>)</h2>
        <?php if ($approved_reviews->num_rows > 0): ?>
            <?php while ($review = $approved_reviews->fetch_assoc()): ?>
            <div class="review-card">
                <p><strong><?php echo htmlspecialchars($review['customer_name']); ?></strong></p>
                <div class="stars">
                    <?php echo str_repeat('★', $review['rating']); ?>
                    <?php echo str_repeat('☆', 5 - $review['rating']); ?>
                </div>
                <p><strong>Event:</strong> <?php echo htmlspecialchars($review['event_type']); ?></p>
                <p class="review-text">"<?php echo nl2br(htmlspecialchars($review['review_text'])); ?>"</p>
                <a href="?delete=<?php echo $review['id']; ?>" class="delete-btn" onclick="return confirm('Delete this review?')">🗑️ Delete</a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #888;">No approved reviews yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>