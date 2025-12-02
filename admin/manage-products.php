<?php
require_once '../config.php';
require_once '../functions.php';
require_admin();

$success = '';
$error = '';
$products = get_all_products();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['product_price'] ?? '';
    $description = $_POST['product_description'] ?? '';
    
    $image_path = 'uploads/placeholder.jpg';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            $image_path = 'uploads/' . $filename;
        }
    }
    
    if (add_product($name, $price, $description, $image_path)) {
        $success = 'Product added successfully';
        $products = get_all_products();
    } else {
        $error = 'Error adding product';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['product_id'] ?? '';
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['product_price'] ?? '';
    $description = $_POST['product_description'] ?? '';
    
    if (update_product($id, $name, $price, $description)) {
        $success = 'Product updated successfully';
        $products = get_all_products();
    } else {
        $error = 'Error updating product';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .container {
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
        .form-section {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
            margin-bottom: 30px;
            max-width: 600px;
        }
        .form-section h2 {
            color: #b30000;
            margin-top: 0;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group.full {
            grid-column: 1 / -1;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #1d1d1f;
            font-weight: 600;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #b30000;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: 'Oswald', sans-serif;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-group input:focus, .form-group textarea:focus {
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
            grid-column: 1 / -1;
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
        .products-section {
            background: #fff5f5;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #b30000;
        }
        .products-section h2 {
            color: #b30000;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            border: 2px solid #b30000;
            border-radius: 10px;
            padding: 15px;
            background: white;
            text-align: center;
        }
        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #f0f0f0;
            display: block;
        }
        .product-card h4 {
            color: #b30000;
            margin: 10px 0 5px;
        }
        .product-card p {
            color: #666;
            font-size: 0.9rem;
            margin: 5px 0;
        }
        .product-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: #b30000;
            margin: 10px 0;
        }
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .btn-edit {
            flex: 1;
            padding: 8px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-edit:hover {
            background: #45a049;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff5f5;
            margin: 5% auto;
            padding: 20px;
            border: 2px solid #b30000;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
        }
        .close {
            color: #b30000;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #8a0000;
        }
        .modal h2 {
            color: #b30000;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Products</h1>
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Add New Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input type="text" id="product_name" name="product_name" required>
                    </div>
                    <div class="form-group">
                        <label for="product_price">Price (₱)</label>
                        <input type="number" id="product_price" name="product_price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group full">
                        <label for="product_description">Description</label>
                        <textarea id="product_description" name="product_description"></textarea>
                    </div>
                    <div class="form-group full">
                        <label for="product_image">Product Image</label>
                        <input type="file" id="product_image" name="product_image" accept="image/*">
                    </div>
                    <button type="submit" name="add_product" class="btn-submit">Add Product</button>
                </div>
            </form>
        </div>

        <div class="products-section">
            <h2>All Products</h2>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.style.display='none'">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?></p>
                    <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                    <p style="font-size: 0.8rem; color: #999;">Added by: <?php 
                        $user_result = $GLOBALS['conn']->query("SELECT username FROM users WHERE id = " . $product['added_by']);
                        $user = $user_result->fetch_assoc();
                        echo htmlspecialchars($user['username']);
                    ?></p>
                    <p style="font-size: 0.8rem; color: #999;"><?php echo date('M d, Y', strtotime($product['date_added'])); ?></p>
                    <div class="product-actions">
                        <button class="btn-edit" onclick="openEditModal(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo htmlspecialchars($product['description']); ?>')">Edit</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Product</h2>
            <form method="POST">
                <input type="hidden" id="edit_product_id" name="product_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_product_name">Product Name</label>
                        <input type="text" id="edit_product_name" name="product_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_price">Price (₱)</label>
                        <input type="number" id="edit_product_price" name="product_price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group full">
                        <label for="edit_product_description">Description</label>
                        <textarea id="edit_product_description" name="product_description"></textarea>
                    </div>
                    <button type="submit" name="update_product" class="btn-submit">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, price, description) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_product_name').value = name;
            document.getElementById('edit_product_price').value = price;
            document.getElementById('edit_product_description').value = description;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
