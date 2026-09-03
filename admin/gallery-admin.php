<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $target_file = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $conn->query("INSERT INTO event_images (title, category, image_path) VALUES ('$title', '$category', '$target_file')");
        $uploaded = true;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $img = $conn->query("SELECT image_path FROM event_images WHERE id = $id")->fetch_assoc();
    if ($img && file_exists($img['image_path'])) unlink($img['image_path']);
    $conn->query("DELETE FROM event_images WHERE id = $id");
}

$images = $conn->query("SELECT * FROM event_images ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Gallery - Admin</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .form-box, .gallery-grid { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: #F26419; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .gallery-item img { width: 100%; height: 150px; object-fit: cover; }
        .delete-btn { background: #e74c3c; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Event Gallery</h1>
        
        <div class="form-box">
            <h2>Add New Event Image</h2>
            <?php if(isset($uploaded)): ?>
            <p style="color:green;">✅ Image uploaded successfully!</p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Event Title" required>
                <select name="category">
                    <option>Concert</option>
                    <option>Corporate</option>
                    <option>Wedding</option>
                    <option>Private Party</option>
                </select>
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Upload Image</button>
            </form>
        </div>
        
        <h2>Current Gallery</h2>
        <div class="gallery-grid">
            <?php while($img = $images->fetch_assoc()): ?>
            <div class="gallery-item">
                <img src="<?php echo $img['image_path']; ?>" alt="<?php echo $img['title']; ?>">
                <p><strong><?php echo $img['title']; ?></strong></p>
                <p><?php echo $img['category']; ?></p>
                <a href="?delete=<?php echo $img['id']; ?>" class="delete-btn" onclick="return confirm('Delete?')">Delete</a>
            </div>
            <?php endwhile; ?>
        </div>
        
        <p><a href="dashboard.php">← Back to Dashboard</a></p>
    </div>
</body>
</html>