<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'vnb_wismilak';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->query('DELETE FROM vnb_plan_items');
$conn->query('DELETE FROM vnb_plans');
echo "✓ Cleared vnb_plans and vnb_plan_items\n";
$conn->close();
