<?php
session_start();
require "../db.php";

$stmt = $db->prepare("SELECT * FROM products JOIN cart_items ON products.id = cart_items.product_id WHERE cart_items.user_id = ?");
$stmt->execute([$_SESSION["user"]["id"]]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sumNormal = 0;
$sumDis = 0;
foreach ($cart as $item) {
    $sumNormal += $item["normal_price"] * $item["amount"];
    $sumDis += $item["discounted_price"] * $item["amount"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../CSS/cartstyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .empty-cart-msg {
            font-size: 1.5em;
            color: #777;
            text-align: center;
            margin-top: 60px;
        }

        .increase, .decrease {


            margin: 0 auto;
            margin: 0.1em;

        }


        .increase, .decrease > span {
            text-align: center;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-title">EcoBasket</div>
        <div class="header-buttons">
            <a href="main.php"><i class="fas fa-home"></i> Home</a>
            <a href="orders.php"><i class="fas fa-box"></i> My Orders</a>
        </div>
    </header>

    <?php if (count($cart) === 0): ?>
        <p class="empty-cart-msg">Your cart is currently empty. Time to go shopping!</p>
    <?php else: ?>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Stock</th>
                        <th>Normal Price</th>
                        <th>Discounted Price</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    <?php foreach ($cart as $item): ?>
                        <tr data-id="<?= $item["product_id"] ?>">
                            <td><?= htmlspecialchars($item["title"]) ?></td>
                            <td><img src="../part2/<?= $item["image_path"] ?>" width="100"></td>
                            <td><?= $item["stock"] ?></td>
                            <td><span class="priceNormal"><?= $item["normal_price"]?>₺</span></td>
                            <td><span class="priceDis"><?= $item["discounted_price"]?>₺</span></td>
                            <td class="amount"><?= $item["amount"] ?></td>
                            <td>
                                <button class="decrease" data-id="<?= $item["product_id"] ?>">-</button>
                                <button class="increase" data-id="<?= $item["product_id"] ?>">+</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px; font-size: 1.2em; text-align: right;">
                <strong>Total Normal Price: </strong><span id="sum-normal"><?= $sumNormal ?> ₺</span><br>
                <strong>Total Discounted Price: </strong><span id="sum-discount"><?= $sumDis ?> ₺</span>
            </div>

            <form action="buy_cart.php" method="post" style="margin-top: 30px; text-align: right;">
                <button type="submit" class="btn">Complete Order</button>
            </form>
        </div>
    <?php endif; ?>

    <script>
        function refreshCart() {
            $.get("see_cart_content.php", function(data) {
                $("#cart-body").html(data.body);
                $("#sum-normal").text(data.sumNormal + " ₺");
                $("#sum-discount").text(data.sumDis + " ₺");

                if (data.body.trim() === "") {
                    $(".container").hide();
                    if ($(".empty-cart-msg").length === 0) {
                        $("body").append('<p class="empty-cart-msg">🛒 Your cart is currently empty. Time to go shopping!</p>');
                    }
                } else {
                    $(".container").show();
                    $(".empty-cart-msg").remove();
                }
            }, "json");
        }

        $(document).on("click", ".increase", function() {
            const id = $(this).data("id");
            $.get("add_one.php", {
                pId: id,
                uId: <?= $_SESSION["user"]["id"] ?>
            }, function() {
                refreshCart();
            });
        });

        $(document).on("click", ".decrease", function() {
            const id = $(this).data("id");
            $.get("remove_from_cart.php", {
                pId: id,
                uId: <?= $_SESSION["user"]["id"] ?>
            }, function() {
                refreshCart();
            });
        });
    </script>
</body>

</html>