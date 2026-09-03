<?php
$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$review_submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $event_type = $conn->real_escape_string($_POST['event_type']);
    $rating = (int)$_POST['rating'];
    $review_text = $conn->real_escape_string($_POST['review_text']);
    
    $conn->query("INSERT INTO reviews (customer_name, customer_email, event_type, rating, review_text, is_approved) 
                  VALUES ('$name', '$email', '$event_type', $rating, '$review_text', 0)");
    $review_submitted = true;
}

$approved_reviews = $conn->query("SELECT * FROM reviews WHERE is_approved = 1 ORDER BY created_at DESC");
$avg_result = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE is_approved = 1");
$avg_data = $avg_result->fetch_assoc();
$average_rating = round($avg_data['avg'], 1);
$total_reviews = $avg_data['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Reviews - Active World</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #0A0A0A; color: white; }
        .navbar { background: #111318; padding: 15px 30px; display: flex; justify-content: space-between; }
        .logo span { color: #F26419; }
        .nav-links a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        .review-form { background: #111318; padding: 40px; border-radius: 15px; margin-bottom: 40px; }
        .review-card { background: #111318; padding: 25px; border-radius: 10px; margin-bottom: 20px; }
        .stars { color: #F5A623; font-size: 20px; }
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; background: #1a1a1a; border: 1px solid #333; border-radius: 5px; color: white; }
        button { background: #F26419; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; }
        .success { background: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .footer { background: #111318; text-align: center; padding: 40px; margin-top: 60px; }
        h1, h2 { margin-bottom: 20px; }
        .rating-summary { text-align: center; margin-bottom: 40px; }
        .stars-big { font-size: 48px; color: #F5A623; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><span>🌍 ACTIVE</span> WORLD</div>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="services.html">Services</a>
            <a href="contact.html">Contact</a>
            <a href="reviews.php">Reviews</a>
        </div>
    </nav>

    <div class="container">
        <div class="rating-summary">
            <div class="stars-big">
                <?php 
                if ($total_reviews > 0) {
                    echo str_repeat('★', floor($average_rating));
                    echo str_repeat('☆', 5 - floor($average_rating));
                } else {
                    echo '☆☆☆☆☆';
                }
                ?>
            </div>
            <p style="margin-top: 10px;"><?php echo $total_reviews; ?> reviews | Average: <?php echo $average_rating; ?>/5</p>
        </div>

        <div class="review-form">
            <h2>Share Your Experience</h2>
            <?php if ($review_submitted): ?>
            <div class="success">✅ Thank you! Your review will appear after approval.</div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <select name="event_type">
                    <option>Concert</option>
                    <option>Corporate</option>
                    <option>Wedding</option>
                    <option>Private Party</option>
                </select>
                <select name="rating" required>
                    <option value="">Rating</option>
                    <option value="5">★★★★★ (5/5)</option>
                    <option value="4">★★★★☆ (4/5)</option>
                    <option value="3">★★★☆☆ (3/5)</option>
                    <option value="2">★★☆☆☆ (2/5)</option>
                    <option value="1">★☆☆☆☆ (1/5)</option>
                </select>
                <textarea name="review_text" rows="4" placeholder="Your review..." required></textarea>
                <button type="submit" name="submit_review">Submit Review</button>
            </form>
        </div>

        <h2>Customer Reviews</h2>
        <?php if ($approved_reviews && $approved_reviews->num_rows > 0): ?>
            <?php while ($review = $approved_reviews->fetch_assoc()): ?>
            <div class="review-card">
                <div class="stars"><?php echo str_repeat('★', $review['rating']); ?><?php echo str_repeat('☆', 5 - $review['rating']); ?></div>
                <p style="margin: 10px 0;">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                <p><strong>- <?php echo htmlspecialchars($review['customer_name']); ?></strong> (<?php echo $review['event_type']; ?>)</p>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to share your experience!</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Active World Kenya. All rights reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>