<?php
require_once '../config.php';
require_once '../functions.php';
require_admin();

$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

$orders = get_orders($date_start, $date_end);


$total_revenue = 0;
$total_orders = count($orders);
foreach ($orders as $order) {
    $total_revenue += $order['total_amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .reports-container {
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
        .back-btn {
            padding: 10px 20px;
            background: #b30000;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .filter-section {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
            margin-bottom: 30px;
        }
        .filter-section h2 {
            color: #b30000;
            margin-top: 0;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: auto auto auto auto auto;
            gap: 15px;
            align-items: end;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            color: #1d1d1f;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .filter-group input {
            padding: 10px;
            border: 1px solid #b30000;
            border-radius: 4px;
        }
        .btn-filter {
            background: #b30000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-filter:hover {
            background: #8a0000;
        }
        .btn-pdf {
            background: #d9534f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-pdf:hover {
            background: #ac2925;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
            text-align: center;
        }
        .stat-card h3 {
            color: #b30000;
            margin: 0 0 10px 0;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #1d1d1f;
        }
        .reports-section {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
        }
        .reports-section h2 {
            color: #b30000;
            margin-top: 0;
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
        .order-number {
            color: #b30000;
            font-weight: bold;
        }
        .table-footer {
            background: #ffe6e6;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .empty-message {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 1.1rem;
        }
        @media print {
            .filter-section, .back-btn, .btn-pdf {
                display: none;
            }
            .reports-container {
                padding: 0;
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="reports-container">
        <div class="header">
            <h1>Sales Reports</h1>
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <div class="filter-section">
            <h2>Filter by Date</h2>
            <form method="GET" class="filter-grid">
                <div class="filter-group">
                    <label for="date_start">Start Date:</label>
                    <input type="date" id="date_start" name="date_start" value="<?php echo htmlspecialchars($date_start); ?>">
                </div>
                <div class="filter-group">
                    <label for="date_end">End Date:</label>
                    <input type="date" id="date_end" name="date_end" value="<?php echo htmlspecialchars($date_end); ?>">
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                <a href="reports.php" class="btn-filter" style="text-decoration: none; text-align: center;">Clear</a>
                <button type="button" class="btn-pdf" onclick="generatePDF()">Export PDF</button>
            </form>
        </div>

        <?php if ($date_start || $date_end): ?>
            <p style="text-align: center; color: #b30000; font-weight: bold;">
                Showing orders from <?php echo $date_start ?: 'All'; ?> to <?php echo $date_end ?: 'Today'; ?>
            </p>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="stat-value"><?php echo $total_orders; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="stat-value">₱<?php echo number_format($total_revenue, 2); ?></div>
            </div>
            <div class="stat-card">
                <h3>Average Order</h3>
                <div class="stat-value">₱<?php echo $total_orders > 0 ? number_format($total_revenue / $total_orders, 2) : '0.00'; ?></div>
            </div>
        </div>

        <div class="reports-section" id="reportTable">
            <h2>Order Transactions</h2>
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Cashier</th>
                            <th>Total Amount</th>
                            <th>Payment</th>
                            <th>Change</th>
                            <th>Date</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="order-number"><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>₱<?php echo number_format($order['payment_amount'], 2); ?></td>
                            <td>₱<?php echo number_format($order['change_amount'], 2); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['date_added'])); ?></td>
                            <td>
                                <button onclick="showDetails(<?php echo $order['id']; ?>)" style="background: #b30000; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">View</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-footer">
                            <td colspan="2">TOTAL REVENUE:</td>
                            <td colspan="5">₱<?php echo number_format($total_revenue, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <div class="empty-message">No orders found for the selected date range.</div>
            <?php endif; ?>
        </div>
    </div>


    <div id="detailsModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5);">
        <div style="background: white; margin: 5% auto; padding: 20px; border: 2px solid #b30000; border-radius: 10px; width: 90%; max-width: 500px;">
            <span style="color: #b30000; float: right; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeDetails()">&times;</span>
            <h2 style="color: #b30000; margin-top: 0;">Order Details</h2>
            <div id="detailsContent"></div>
        </div>
    </div>

    <script>
        function showDetails(orderId) {
            fetch('get-order-details.php?order_id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    let html = `<div style="margin-bottom: 10px;"><strong>Order #:</strong> ${data.order_number}</div>`;
                    html += `<div style="margin-bottom: 10px;"><strong>Date:</strong> ${data.date_added}</div>`;
                    html += `<div style="margin-bottom: 10px;"><strong>Cashier:</strong> ${data.username}</div>`;
                    html += `<hr><h3>Items:</h3>`;
                    
                    data.items.forEach(item => {
                        html += `<div style="margin-bottom: 8px;"><strong>${item.name}</strong><br>Qty: ${item.quantity} @ ₱${parseFloat(item.unit_price).toFixed(2)} = ₱${parseFloat(item.subtotal).toFixed(2)}</div>`;
                    });
                    
                    html += `<hr><div style="font-weight: bold; font-size: 1.1rem;"><div>Total: ₱${parseFloat(data.total_amount).toFixed(2)}</div><div>Payment: ₱${parseFloat(data.payment_amount).toFixed(2)}</div><div>Change: ₱${parseFloat(data.change_amount).toFixed(2)}</div></div>`;
                    
                    document.getElementById('detailsContent').innerHTML = html;
                    document.getElementById('detailsModal').style.display = 'block';
                });
        }

        function closeDetails() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        function generatePDF() {
            const dateStart = document.getElementById('date_start').value;
            const dateEnd = document.getElementById('date_end').value;
            
            let url = 'export-pdf.php';
            if (dateStart || dateEnd) {
                url += '?date_start=' + dateStart + '&date_end=' + dateEnd;
            }
            
            window.open(url, '_blank');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
