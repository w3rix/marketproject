<?php
session_start();
require "../db.php";

$userId = $_SESSION["user"]["id"];

$stmt = $db->prepare("
    SELECT c.product_id, c.amount, p.stock
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->beginTransaction();

try {
    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $amount = $item['amount'];
        $stock = $item['stock'];

        if ($stock < $amount) {
            throw new Exception("Insufficient stock for product ID $productId");
        }

        $insert = $db->prepare("INSERT INTO bought_items (user_id, product_id, amount, bought_at) VALUES (?, ?, ?, NOW())");
        $insert->execute([$userId, $productId, $amount]);

        $update = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update->execute([$amount, $productId]);
    }

    $delete = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $delete->execute([$userId]);

    $db->commit();
    header("Location: see_cart.php?success=1");
} catch (Exception $e) {
    $db->rollBack();
    header("Location: see_cart.php?error=" . urlencode($e->getMessage()));
}
