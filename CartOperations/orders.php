<?php
session_start();
require "../db.php";

$userId = $_SESSION["user"]["id"];
$stmt = $db->prepare("SELECT b.*, p.title, p.image_path FROM bought_items b JOIN products p ON b.product_id = p.id WHERE b.user_id = ? ORDER BY b.bought_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="../CSS/cartstyle.css">
</head>

<body>
    <header class="header">
        <div class="header-title">EcoBasket</div>
        <div class="header-buttons">
            <a href="main.php"><i class="fas fa-home"></i> Home</a>
            <a href="see_cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
        </div>
    </header>

    <div class="container">
        <h2>Order History</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Amount</th>
                    <th>Ordered At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order["title"]) ?></td>
                        <td><img src="../part2/<?= $order["image_path"] ?>" width="100"></td>
                        <td><?= $order["amount"] ?></td>
                        <td><?= $order["bought_at"] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>