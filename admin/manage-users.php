<?php
require_once '../config.php';
require_once '../functions.php';
require_superadmin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'admin';
    
    if (create_user($username, $email, $password, $role)) {
        $success = 'User created successfully';
    } else {
        $error = 'Error creating user. Username or email may already exist.';
    }
}

$users = get_all_users();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .container {
            background: linear-gradient(to bottom, #ffffff 0%, #ffcccc 50%, #b30000 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #b30000;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .form-section {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
            margin-bottom: 30px;
            max-width: 500px;
        }
        .form-section h2 {
            color: #b30000;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #1d1d1f;
            font-weight: 600;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #b30000;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(179, 0, 0, 0.3);
        }
        .btn-submit {
            background: #b30000;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        .btn-submit:hover {
            background: #8a0000;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .users-table {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #b30000;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Create New Admin User</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="admin">Admin (Cashier)</option>
                    </select>
                </div>
                <button type="submit" name="create_user" class="btn-submit">Create User</button>
            </form>
        </div>

        <div class="users-table">
            <h2>All Admin Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst($user['status']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['date_added'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
