<?php

require_once '../db.php';
$id = $_GET["id"];
$stmt = $db->prepare("delete from products where id = ?") ;
$stmt->execute([$id]);

header("Location: market.php");

?>