<?php
session_start();
require_once '../db.php';

$error = [];

$max_postsize = ini_get("post_max_size");


if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($_POST)) {
    $error = "Request exceeds $max_postsize";
}

if (!empty($_POST)) {
    $photo = upload("image");


    if (isset($photo["error"])) {
        echo "<p style='color:red'>Image upload failed: " . htmlspecialchars($photo["error"]) . "</p>";
    } else {
        $title = $_POST['title'];
        $market_id = $_SESSION["user"]["id"];
        $stock = intval($_POST['stock']);
        $normal_price = floatval($_POST['normal_price']);
        $discounted_price = floatval($_POST['discounted_price']);
        $expiration_date = $_POST['expiration_date'];
        $image_path = "uploads/" . $photo["filename"]; 

        $stmt = $db->prepare("INSERT INTO products (market_id, title, stock, normal_price, discounted_price, expiration_date, image_path)
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$market_id, $title, $stock, $normal_price, $discounted_price, $expiration_date, $image_path]);
    }
}

function upload($filebox) {
    global $max_filesize;

    if (isset($_FILES[$filebox])) {
        $file = $_FILES[$filebox];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if ($file["error"] == UPLOAD_ERR_INI_SIZE) {
            return ["error" => "{$file["name"]} : exceeds max size"];
        } elseif ($file["error"] == UPLOAD_ERR_NO_FILE) {
            return ["error" => "No file chosen"];
        } elseif (!in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
            return ["error" => "{$file["name"]} : not a valid image"];
        } else {
            $filename = bin2hex(random_bytes(8)) . ".$ext";
            $destination = "./uploads/" . $filename;

            if (move_uploaded_file($file["tmp_name"], $destination)) {
                return ["filename" => $filename];
            } else {
                return ["error" => "Upload failed (check permissions)"];
            }
        }
    }
    return ["error" => "File not uploaded"];
}

$products = $db->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$today = date('Y-m-d');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Market Dashboard</title>
    <link rel="stylesheet" href="../CSS/marketcss.css">
</head>

<body>
        <div class="dashboard-header">
            <div class="welcome-text">Welcome, <?= $_SESSION["user"]["name"]?></div>
            <div class="header-actions">
                <span class="date">Today: <?= $today ?></span>
                <a href="edit_market_user.php" class="btn-header">Edit Profile</a>
                <button onclick="toggleAddProduct()" class="btn-header">Add Product</button>
                <button class="btn-header"><a href="../logout.php">Logout</a></button>
            </div>

        </div>
        <div class="container">

        <div>
            <h3>Products on Sale</h3>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Stock</th>
                        <th>Normal Price</th>
                        <th>Discounted Price</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr class="<?= $p['expiration_date'] < $today ? 'table-danger' : '' ?>">
                            <td><a href="edit_product.php?id=<?= $p['id'] ?>"><img src="<?=$p['image_path'] ?>"
                                        width="60"></a></td>
                            <td><?= $p['title'] ?></td>
                            <td><?= $p['stock'] ?></td>
                            <td><?= $p['normal_price'] ?> TL</td>
                            <td><?= $p['discounted_price'] ?> TL</td>
                            <td><?= $p['expiration_date'] ?></td>
                            <td><?= $p['expiration_date'] < $today ? 'Expired' : 'Active' ?></td>
                            <td><a href="delete.php?id=<?= $p['id'] ?>"><img src="./uploads/del.png"></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


        <div id="add-product-section" style= "display: none;">
            <h3>Add New Product</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div>
                    <div class="col">
                        <input type="text" name="title" class="form-control" placeholder="Product Title" required>
                    </div>
                    <div class="col">
                        <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                    </div>
                </div>
                <div>
                    <div class="col">
                        <input type="number" step="0.01" name="normal_price" class="form-control"
                            placeholder="Normal Price" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" name="discounted_price" class="form-control"
                            placeholder="Discounted Price" required>
                    </div>
                </div>
                <div>
                    <input type="date" name="expiration_date" class="form-control" required>
                </div>
                <div>
                    <input type="file" name="image" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Add Product</button>
            </form>
        </div>
    </div>
</body>
<script>
    function toggleAddProduct() {
        const section = document.getElementById("add-product-section");
        section.style.display = section.style.display === "none" ? "block" : "none";
    }
</script>

</html>