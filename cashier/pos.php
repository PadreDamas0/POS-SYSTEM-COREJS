<?php
require_once '../config.php';
require_once '../functions.php';
require_admin();

$products = get_all_products();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dambalasek POS</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .pos-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            background: linear-gradient(to bottom, #ffffff 0%, #ffcccc 50%, #b30000 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .header {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff5f5;
            padding: 15px 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #b30000;
            margin: 0;
            font-size: 1.8rem;
        }
        .user-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .logout-btn {
            padding: 10px 20px;
            background: #b30000;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }
        .logout-btn:hover {
            background: #8a0000;
        }
        .menu-section {
            grid-column: 1;
        }
        .menu-header {
            background: #fff5f5;
            padding: 15px 20px;
            border: 2px solid #b30000;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }
        .menu-header h2 {
            color: #b30000;
            margin: 0;
        }
        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #fff5f5;
            border: 2px solid #b30000;
            border-top: 1px solid #b30000;
            border-radius: 0 0 10px 10px;
        }
        .item-card {
            border: 2px solid #b30000;
            border-radius: 10px;
            padding: 12px;
            background: white;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .item-card:hover {
            transform: scale(1.05);
        }
        .item-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #f0f0f0;
            display: block;
        }
        .item-card h4 {
            color: #b30000;
            margin: 8px 0 5px;
            font-size: 0.95rem;
        }
        .item-card p {
            color: #1d1d1f;
            margin: 5px 0;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .item-card button {
            background: #b30000;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            margin-top: 8px;
        }
        .item-card button:hover {
            background: #8a0000;
        }
        .cart-section {
            grid-column: 2;
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        .cart-box {
            background: #fff5f5;
            padding: 15px;
            border: 2px solid #b30000;
            border-radius: 10px;
        }
        .cart-box h3 {
            color: #b30000;
            margin-top: 0;
            font-size: 1.2rem;
        }
        #cartItems {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 300px;
            overflow-y: scroll;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: #b30000 #f0f0f0;
        }
        #cartItems::-webkit-scrollbar {
            width: 8px;
        }
        #cartItems::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }
        #cartItems::-webkit-scrollbar-thumb {
            background: #b30000;
            border-radius: 10px;
        }
        #cartItems::-webkit-scrollbar-thumb:hover {
            background: #8a0000;
        }
        #cartItems li {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }
        #cartItems li:last-child {
            border-bottom: none;
        }
        .cart-item-remove {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.8rem;
            flex-shrink: 0;
            margin-left: 8px;
        }
        .cart-total {
            background: #b30000;
            color: white;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 10px;
        }
        .payment-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .payment-section input {
            padding: 10px;
            border: 1px solid #b30000;
            border-radius: 6px;
            font-size: 1rem;
        }
        .payment-section button {
            background: #b30000;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
        }
        .payment-section button:hover {
            background: #8a0000;
        }
        .clear-cart-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        .receipt-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .receipt-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border: 2px solid #b30000;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }
        .receipt-content h2 {
            color: #b30000;
        }
        .receipt-items {
            text-align: left;
            margin: 20px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .receipt-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .receipt-summary {
            margin: 15px 0;
        }
        .receipt-summary div {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-weight: bold;
        }
        .close-receipt {
            background: #b30000;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 15px;
        }
        @media (max-width: 768px) {
            .pos-container {
                grid-template-columns: 1fr;
            }
            .cart-section {
                grid-column: 1;
                position: static;
            }
            .menu-container {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="pos-container">
        <div class="header">
            <h1>DAMBALASEK™ POS</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <form method="POST" action="../logout.php" style="display: inline;">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        <div class="menu-section">
            <div class="menu-header">
                <h2>Menu Items</h2>
            </div>
            <div class="menu-container">
                <?php foreach ($products as $product): ?>
                <div class="item-card" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>">
                    <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.style.display='none'">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p>₱<?php echo number_format($product['price'], 2); ?></p>
                    <button type="button" class="addBtn">Add to Cart</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="cart-section">
            <div class="cart-box">
                <h3>Your Cart</h3>
                <ul id="cartItems"></ul>
                <div class="cart-total">₱<span id="cartTotal">0.00</span></div>
                <button class="clear-cart-btn" onclick="clearCart()">Clear Cart</button>
            </div>

            <div class="cart-box payment-section">
                <h3 style="margin: 0 0 10px 0; color: #b30000;">Payment</h3>
                <input type="number" id="paymentInput" placeholder="Enter amount" step="0.01" min="0">
                <button id="payBtn">Complete Order & Pay</button>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="receipt-modal">
        <div class="receipt-content">
            <h2>Order Receipt</h2>
            <div id="receiptContent"></div>
            <button class="close-receipt" onclick="closeReceipt()">Print & Close</button>
        </div>
    </div>

    <script>
        let cart = [];

        $(document).ready(function() {
            $('.addBtn').click(function() {
                const card = $(this).closest('.item-card');
                const id = card.data('id');
                const name = card.data('name');
                const price = parseFloat(card.data('price'));

                const existing = cart.find(item => item.id === id);
                if (existing) {
                    existing.qty += 1;
                } else {
                    cart.push({ id, name, price, qty: 1 });
                }

                renderCart();
            });

            $('#payBtn').click(function() {
                if (cart.length === 0) {
                    alert('Cart is empty');
                    return;
                }

                const total = parseFloat($('#cartTotal').text());
                const payment = parseFloat($('#paymentInput').val());

                if (isNaN(payment) || payment < total) {
                    alert("Not enough payment!");
                    return;
                }

                processOrder(total, payment);
            });
        });

        function renderCart() {
            const $cartItems = $('#cartItems');
            const $cartTotal = $('#cartTotal');
            $cartItems.empty();
            let total = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.qty * item.price;
                total += itemTotal;
                $cartItems.append(`
                    <li>
                        <div>
                            <strong>${item.name}</strong><br>
                            x${item.qty} @ ₱${item.price.toFixed(2)} = ₱${itemTotal.toFixed(2)}
                        </div>
                        <button class="cart-item-remove" onclick="removeFromCart(${index})">Remove</button>
                    </li>
                `);
            });

            $cartTotal.text(total.toFixed(2));
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
            $('#paymentInput').val('');
        }

        function processOrder(total, payment) {
            const change = payment - total;

            const formData = new FormData();
            formData.append('total_amount', total);
            formData.append('payment_amount', payment);
            formData.append('items', JSON.stringify(cart));

            $.ajax({
                type: 'POST',
                url: 'process-order.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const order = JSON.parse(response);
                    
                    let receiptHTML = '<div class="receipt-items">';
                    cart.forEach(item => {
                        receiptHTML += `<div class="receipt-item"><span>${item.name} x${item.qty}</span><span>₱${(item.qty * item.price).toFixed(2)}</span></div>`;
                    });
                    receiptHTML += '</div>';
                    receiptHTML += `<div class="receipt-summary">
                        <div><span>Order #:</span><span>${order.order_number}</span></div>
                        <div><span>Subtotal:</span><span>₱${total.toFixed(2)}</span></div>
                        <div><span>Payment:</span><span>₱${payment.toFixed(2)}</span></div>
                        <div style="font-size: 1.1rem; border-top: 1px solid #ddd; padding-top: 10px;"><span>Change:</span><span>₱${change.toFixed(2)}</span></div>
                    </div>`;

                    $('#receiptContent').html(receiptHTML);
                    $('#receiptModal').fadeIn();
                    
                    clearCart();
                },
                error: function() {
                    alert('Error processing order');
                }
            });
        }

        function closeReceipt() {
            $('#receiptModal').fadeOut();
        }
    </script>
</body>
</html>
