<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$conn->query("
    CREATE TABLE IF NOT EXISTS `admin_users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `email` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Check if admin exists
$check = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'");
if ($check->num_rows == 0) {
    $hashed = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    $conn->query("INSERT INTO admin_users (username, password, email) VALUES ('admin', '$hashed', 'admin@activeworld.co.ke')");
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = $conn->query("SELECT * FROM admin_users WHERE username = '$username'");
    $admin = $result->fetch_assoc();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Active World</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #0A0A0A, #1a1a2e);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background: #181C24;
            padding: 50px;
            border-radius: 20px;
            width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .login-box h1 {
            color: #F26419;
            text-align: center;
            margin-bottom: 10px;
            font-size: 36px;
        }
        .login-box p {
            color: #888;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        label {
            display: block;
            color: #aaa;
            margin-bottom: 8px;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 14px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            transition: all 0.3s;
            padding-right: 45px;
        }
        input:focus {
            outline: none;
            border-color: #F26419;
            background: rgba(242,100,25,0.05);
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #F26419, #e0550a);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(242,100,25,0.3);
        }
        .error {
            background: rgba(229,57,53,0.2);
            border: 1px solid #E53935;
            color: #E53935;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        /* Password toggle button styles */
        .password-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            margin: 0;
            width: auto;
            transition: color 0.3s;
        }
        .toggle-password:hover {
            color: #F26419;
            transform: translateY(-50%);
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>ACTIVE.WORLD</h1>
        <p>Admin Login</p>
        
        <?php if ($error): ?>
            <div class="error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Enter password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        👁️
                    </button>
                </div>
            </div>
            
            <button type="submit">Login →</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }
    </script>
</body>
</html>