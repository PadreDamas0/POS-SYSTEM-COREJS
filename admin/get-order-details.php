<?php
require_once '../config.php';
require_once '../functions.php';
require_admin();

header('Content-Type: application/json');

$order_id = $_GET['order_id'] ?? 0;
$order_id = intval($order_id);

$order_result = $GLOBALS['conn']->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.created_by = u.id WHERE o.id = $order_id");
$order = $order_result->fetch_assoc();

$items = get_order_items($order_id);

echo json_encode([
    'order_number' => $order['order_number'],
    'date_added' => date('M d, Y H:i', strtotime($order['date_added'])),
    'username' => $order['username'],
    'total_amount' => $order['total_amount'],
    'payment_amount' => $order['payment_amount'],
    'change_amount' => $order['change_amount'],
    'items' => $items
]);
?>
