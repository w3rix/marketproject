<?php
session_start();
require "../db.php";

$stmt = $db->prepare("SELECT * FROM products JOIN cart_items ON products.id = cart_items.product_id WHERE cart_items.user_id = ?");
$stmt->execute([$_SESSION["user"]["id"]]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sumNormal = $sumDis = 0;
$body = "";

foreach ($cart as $p) {
    $sumNormal += $p["normal_price"] * $p["amount"];
    $sumDis += $p["discounted_price"] * $p["amount"];
    $body .= "<tr data-id='{$p["product_id"]}'>";
    $body .= "<td>{$p["title"]}</td>";
    $body .= "<td><img src='../part2/{$p["image_path"]}'></td>";
    $body .= "<td>{$p["stock"]}</td>";
    $body .= "<td><span class='priceNormal'>{$p["normal_price"]}₺</span></td>";
    $body .= "<td><span class='priceDis'>{$p["discounted_price"]}₺</span></td>";
    $body .= "<td class='amount'>{$p["amount"]}</td>";
    $body .= "<td><button class='decrease' data-id='{$p["product_id"]}'>-</button>";
    $body .= " <button class='increase' data-id='{$p["product_id"]}'>+</button></td>";
    $body .= "</tr>";
}

echo json_encode([
    "body" => $body,
    "sumNormal" => $sumNormal,
    "sumDis" => $sumDis
]);

