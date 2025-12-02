<?php
require_once '../config.php';
require_once '../functions.php';
require_superadmin();

$users = get_all_users();
$total_users = count($users);


$user_result = $GLOBALS['conn']->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
$user_stats = $user_result->fetch_assoc();

$order_result = $GLOBALS['conn']->query("SELECT COUNT(*) as count, SUM(total_amount) as total FROM orders");
$order_stats = $order_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-container {
            background: linear-gradient(to bottom, #ffffff 0%, #ffcccc 50%, #b30000 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
        }
        .header h1 {
            color: #b30000;
            margin: 0;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        .nav-buttons a, .nav-buttons button {
            padding: 10px 20px;
            background: #b30000;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }
        .nav-buttons a:hover, .nav-buttons button:hover {
            background: #8a0000;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
        }
        .card h3 {
            color: #b30000;
            margin-top: 0;
        }
        .card-value {
            font-size: 2rem;
            font-weight: bold;
            color: #1d1d1f;
        }
        .users-section {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
        }
        .users-section h2 {
            color: #b30000;
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
        table tr:hover {
            background: #ffe6e6;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-suspended {
            color: red;
            font-weight: bold;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 0.9rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-suspend {
            background: #ff6b6b;
            color: white;
        }
        .btn-activate {
            background: #51cf66;
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1>Superadmin Dashboard</h1>
            <div class="nav-buttons">
                <a href="manage-users.php">Manage Users</a>
                <a href="manage-products.php">Manage Products</a>
                <a href="reports.php">Reports</a>
                <form method="POST" action="../logout.php" style="display: inline;">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Total Users</h3>
                <div class="card-value"><?php echo $user_stats['count']; ?></div>
            </div>
            <div class="card">
                <h3>Total Orders</h3>
                <div class="card-value"><?php echo $order_stats['count']; ?></div>
            </div>
            <div class="card">
                <h3>Total Revenue</h3>
                <div class="card-value">₱<?php echo number_format($order_stats['total'] ?? 0, 2); ?></div>
            </div>
        </div>

        <div class="users-section">
            <h2>Registered Admins</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="status-<?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['date_added'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($user['status'] === 'active'): ?>
                                    <form method="POST" action="suspend-user.php" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn-small btn-suspend">Suspend</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="activate-user.php" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn-small btn-activate">Activate</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
