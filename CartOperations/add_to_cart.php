<?php
session_start();
require "../db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user"]["id"])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$userId = $_SESSION["user"]["id"];
$pId = $_GET["pId"] ?? null;


if (!$pId) {
    echo json_encode(["success" => false, "error" => "Missing product ID"]);
    exit;
}

$stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->execute([$pId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(["success" => false, "error" => "Product not found"]);
    exit;
}

$stmt = $db->prepare("SELECT amount FROM cart_items WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $pId]);
$cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

$currentAmount = $cartItem ? $cartItem["amount"] : 0;

if ($currentAmount >= $product["stock"]) {
    echo json_encode(["success" => false, "error" => "Not enough stock"]);
    exit;
}

if ($cartItem) {
    $stmt = $db->prepare("UPDATE cart_items SET amount = amount + 1 WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $pId]);
} else {
    $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, amount) VALUES (?, ?, 1)");
    $stmt->execute([$userId, $pId]);
}

echo json_encode(["success" => true]);
