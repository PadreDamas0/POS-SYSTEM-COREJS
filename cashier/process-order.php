<?php
require_once '../config.php';
require_once '../functions.php';
require_admin();

header('Content-Type: application/json');

$total_amount = $_POST['total_amount'] ?? 0;
$payment_amount = $_POST['payment_amount'] ?? 0;
$items = json_decode($_POST['items'] ?? '[]', true);


$order_items = [];
foreach ($items as $item) {
    $order_items[] = [
        'product_id' => $item['id'],
        'quantity' => $item['qty'],
        'price' => $item['price']
    ];
}

$order_id = create_order($order_items, $payment_amount);

if ($order_id) {
    $order_result = $GLOBALS['conn']->query("SELECT order_number FROM orders WHERE id = $order_id");
    $order = $order_result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'order_number' => $order['order_number']
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to process order'
    ]);
}
?>
