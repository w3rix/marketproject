<?php
session_start();
require "../db.php";
$stmt = $db->prepare("SELECT COUNT(*) count FROM cart_items WHERE user_id = ?");
$stmt->execute([$_SESSION["user"]["id"]]);
$count = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($count);
?>
