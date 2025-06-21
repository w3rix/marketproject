<?php
require "../db.php";
$today = date("Y-m-d");



$search = $_GET['q'] ?? '';
$city = $_GET['c'] ?? '';
$district = $_GET['d'] ?? '';

$stmt = $db->prepare("SELECT * FROM products JOIN users ON users.id=products.market_id WHERE title LIKE ? AND users.city=? AND users.district=?");
$stmt->execute(['%' . $search . '%',$city,$district]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($products);
