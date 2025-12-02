<?php
require_once '../config.php';
require_once '../functions.php';
require_superadmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    activate_user($_POST['user_id']);
}

header("Location: dashboard.php");
exit;
?>
