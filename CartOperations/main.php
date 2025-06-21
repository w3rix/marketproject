<?php
session_start();
require "../db.php";


$city = $_SESSION['user']['city'] ?? '';
$district = $_SESSION['user']['district'] ?? '';


$today = date("Y-m-d");
$stmt = $db->prepare("SELECT 
    products.id AS product_id,
    products.title,
    products.stock,
    products.normal_price,
    products.discounted_price,
    products.expiration_date,
    products.image_path,
    users.id AS market_id,
    users.name AS market_name,
    users.city,
    users.district
    FROM products
    JOIN users ON products.market_id = users.id
    WHERE users.city = ? AND users.district = ?
");
$stmt->execute([$city, $district]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);




$stmt = $db->prepare("SELECT SUM(amount) AS total FROM cart_items WHERE user_id = ?");
$stmt->execute([$_SESSION["user"]["id"]]);
$count = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBasket</title>
    <link rel="stylesheet" href="../CSS/cartstyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <header class="header">
        <div class="header-title">EcoBasket</div>
        <div class="header-buttons">
            <a href="../part2/edit_market_user.php">Edit Profile</a>
            <a href="orders.php"><i class="fas fa-box"></i> My Orders</a>
            <a href="see_cart.php"><i class="fas fa-shopping-cart"></i> Go to Cart
                <span id="cart-count">
                    <?= isset($count["total"]) && $count["total"] != 0 ? $count["total"] : "0" ?>
                </span>
            </a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="container">
        <input type="text" id="search" placeholder="Search products..."
            style="width: 20%; padding: 10px; margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Stock</th>
                    <th>Normal Price</th>
                    <th>Discounted Price</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="product-list">
                <?php foreach ($items as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <td><img src="../part2/<?= htmlspecialchars($p['image_path']) ?>"></td>
                        <td><?= $p['stock'] ?></td>
                        <td><?= $p['normal_price'] ?> TL</td>
                        <td><?= $p['discounted_price'] ?> TL</td>
                        <td><?= $p['expiration_date'] ?></td>
                        <td><?= $p['expiration_date'] < $today ? 'Expired' : 'Active' ?></td>
                        <td>
                            <button class="add-to-cart" data-id="<?= $p['product_id'] ?>"
                                data-user="<?= $_SESSION["user"]["id"] ?>">
                                Add to cart
                            </button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>

        </table>
    </div>



    <script>
        function updateCartCount() {
            $.get("cart_count.php", function (data) {
                if (data.count !== undefined) {
                    $("#cart-count").text(data.count);
                }
            }, "json");
        }

        $(document).ready(function () {
            updateCartCount();
            $(".add-to-cart").on("click", function () {
                const pId = $(this).data("id");
                console.log(pId);

                $.get("add_to_cart.php", {
                    pId
                }, function (response) {
                    if (response.success) {
                        updateCartCount();
                    } else {
                        console.error(response.error || "Error adding to cart.");
                    }
                }, "json");
            });
        });

        $("#search").on("input", function () {
            const query = $(this).val();
            const city = "<?= $_SESSION["user"]["city"] ?>";
            const district = "<?= $_SESSION["user"]["district"] ?>";

            console.log(city)
            console.log(district)

            $.get("search_products.php", {
                q: query,
                c: city,
                d: district
            }, function (data) {
                let rows = "";

                data.forEach(p => {
                    const today = new Date().toISOString().split("T")[0];
                    const expired = p.expiration_date < today ? "Expired" : "Active";

                    rows += `
                <tr>
                    <td>${p.title}</td>
                    <td><img src="../part2/${p.image_path}" width="120"></td>
                    <td>${p.stock}</td>
                    <td>${p.normal_price} TL</td>
                    <td>${p.discounted_price} TL</td>
                    <td>${p.expiration_date}</td>
                    <td>${expired}</td>
                    <td>
                        <button class="add-to-cart" data-id="${p.id}" data-user="${<?= $_SESSION["user"]["id"] ?>}">Add to cart</button>
                    </td>
                </tr>
            `;
                });

                $("#product-list").html(rows);

                $(".add-to-cart").on("click", function () {
                    const pId = $(this).data("id");

                    $.get("add_to_cart.php", {
                        pId
                    }, function (response) {
                        if (response.success) {
                            updateCartCount();
                        } else {
                            console.error(response.error);
                        }
                    }, "json");
                });
            }, "json");
        });
    </script>

</body>

</html>