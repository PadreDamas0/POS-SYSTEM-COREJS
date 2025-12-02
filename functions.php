<?php
require_once 'config.php';


function login($username, $password) {
    global $conn;
    $username = $conn->real_escape_string($username);
    $password_hash = hash('sha256', $password);
    
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password_hash' AND status = 'active'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header("Location: index.php");
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_superadmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin';
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: index.php");
        exit;
    }
}

function require_superadmin() {
    require_login();
    if (!is_superadmin()) {
        header("Location: dashboard.php");
        exit;
    }
}

function require_admin() {
    require_login();
    if (!is_admin() && !is_superadmin()) {
        header("Location: dashboard.php");
        exit;
    }
}


function create_user($username, $email, $password, $role) {
    global $conn;
    if (!is_superadmin()) return false;
    
    $username = $conn->real_escape_string($username);
    $email = $conn->real_escape_string($email);
    $password_hash = hash('sha256', $password);
    $created_by = $_SESSION['user_id'];
    
    $sql = "INSERT INTO users (username, email, password, role, status, created_by) 
            VALUES ('$username', '$email', '$password_hash', '$role', 'active', $created_by)";
    
    return $conn->query($sql);
}

function get_all_users() {
    global $conn;
    if (!is_superadmin()) return [];
    
    $sql = "SELECT id, username, email, role, status, date_added FROM users WHERE role = 'admin'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function suspend_user($user_id) {
    global $conn;
    if (!is_superadmin()) return false;
    
    $user_id = intval($user_id);
    $sql = "UPDATE users SET status = 'suspended' WHERE id = $user_id AND role = 'admin'";
    return $conn->query($sql);
}

function activate_user($user_id) {
    global $conn;
    if (!is_superadmin()) return false;
    
    $user_id = intval($user_id);
    $sql = "UPDATE users SET status = 'active' WHERE id = $user_id AND role = 'admin'";
    return $conn->query($sql);
}

function add_product($name, $price, $description, $image_path) {
    global $conn;
    require_admin();
    
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $image_path = $conn->real_escape_string($image_path);
    $price = floatval($price);
    $added_by = $_SESSION['user_id'];
    
    $sql = "INSERT INTO products (name, price, description, image_path, added_by) 
            VALUES ('$name', $price, '$description', '$image_path', $added_by)";
    
    return $conn->query($sql);
}

function get_all_products() {
    global $conn;
    $sql = "SELECT * FROM products WHERE status = 'active' ORDER BY date_added DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_product($id) {
    global $conn;
    $id = intval($id);
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function update_product($id, $name, $price, $description) {
    global $conn;
    require_admin();
    
    $id = intval($id);
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $price = floatval($price);
    
    $sql = "UPDATE products SET name = '$name', price = $price, description = '$description' WHERE id = $id";
    return $conn->query($sql);
}


function create_order($items, $payment_amount) {
    global $conn;
    require_admin();
    
    $payment_amount = floatval($payment_amount);
    $total_amount = 0;
    $user_id = $_SESSION['user_id'];
    

    foreach ($items as $item) {
        $total_amount += $item['quantity'] * $item['price'];
    }
    
    if ($payment_amount < $total_amount) {
        return false;
    }
    
    $change_amount = $payment_amount - $total_amount;
    $order_number = 'ORD-' . date('YmdHis');
    

    $sql = "INSERT INTO orders (order_number, total_amount, payment_amount, change_amount, created_by) 
            VALUES ('$order_number', $total_amount, $payment_amount, $change_amount, $user_id)";
    
    if ($conn->query($sql)) {
        $order_id = $conn->insert_id;
        

        foreach ($items as $item) {
            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            $price = floatval($item['price']);
            $subtotal = $quantity * $price;
            
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) 
                    VALUES ($order_id, $product_id, $quantity, $price, $subtotal)";
            $conn->query($sql);
        }
        
        return $order_id;
    }
    
    return false;
}

function get_orders($date_start = null, $date_end = null) {
    global $conn;
    
    $sql = "SELECT o.*, u.username FROM orders o JOIN users u ON o.created_by = u.id WHERE 1=1";
    
    if ($date_start) {
        $date_start = $conn->real_escape_string($date_start);
        $sql .= " AND DATE(o.date_added) >= '$date_start'";
    }
    
    if ($date_end) {
        $date_end = $conn->real_escape_string($date_end);
        $sql .= " AND DATE(o.date_added) <= '$date_end'";
    }
    
    $sql .= " ORDER BY o.date_added DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_order_items($order_id) {
    global $conn;
    $order_id = intval($order_id);
    
    $sql = "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
