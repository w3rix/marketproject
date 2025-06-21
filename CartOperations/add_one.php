<?php
session_start();
require "../db.php";

if (isset($_GET["pId"], $_GET["uId"])) {
    $stmt = $db->prepare("UPDATE cart_items SET amount = amount + 1 WHERE product_id = ? AND user_id = ?");
    $stmt->execute([$_GET["pId"], $_GET["uId"]]);

    echo json_encode(["success" => true]);
}
?>
