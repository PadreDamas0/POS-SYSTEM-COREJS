<?php
require_once 'config.php';
require_once 'functions.php';

if (is_logged_in()) {
    if (is_superadmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: cashier/pos.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        if (is_superadmin()) {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: cashier/pos.php");
        }
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dambalasek Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, #ffffff 0%, #ffcccc 50%, #b30000 100%);
        }
        .login-box {
            background: #fff5f5;
            padding: 40px;
            border-radius: 10px;
            border: 2px solid #b30000;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-box h1 {
            color: #b30000;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1d1d1f;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #b30000;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #b30000;
            box-shadow: 0 0 5px rgba(179, 0, 0, 0.3);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #b30000;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #8a0000;
        }
        .error-message {
            color: #b30000;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .info-text {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>DAMBALASEK</h1>
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            <div class="info-text">
                <p>Default: superadmin / superadmin123</p>
            </div>
        </div>
    </div>
</body>
</html>
