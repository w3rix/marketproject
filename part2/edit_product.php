<?php
session_start();
require_once '../db.php';

$market_id = $_SESSION["user"]["id"];
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0; 

$stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND market_id = ?");
$stmt->execute([$product_id, $market_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found or access denied.";
    echo "<br><br><a href='./market.php'>Return</a>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $stock = intval($_POST['stock']);
    $normal_price = floatval($_POST['normal_price']);
    $discounted_price = floatval($_POST['discounted_price']);
    $expiration_date = $_POST['expiration_date'];

    $new_image_path = $product['image_path'];

    
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "uploads/";
        $new_image_path = $upload_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $new_image_path);
    }

    $update_stmt = $db->prepare("UPDATE products SET title=?, stock=?, normal_price=?, discounted_price=?, expiration_date=?, image_path=? WHERE id=? AND market_id=?");
    $update_stmt->execute([$title, $stock, $normal_price, $discounted_price, $expiration_date, $new_image_path, $product_id, $market_id]);

    header("Location: market.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../CSS/editproduct.css">
</head>
<body>
<div class="container">
    <h2>Edit Product: <?= htmlspecialchars($product['title']) ?></h2>
    <div class="product-preview">
        <p><strong>Current Image:</strong></p>
        <?php if ($product['image_path']): ?>
            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image">
        <?php else: ?>
            <p>No image uploaded.</p>
        <?php endif; ?>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
        <input class="form-control" type="number" name="stock" value="<?= $product['stock'] ?>" required>
        <input class="form-control" type="number" step="0.01" name="normal_price" value="<?= $product['normal_price'] ?>" required>
        <input class="form-control" type="number" step="0.01" name="discounted_price" value="<?= $product['discounted_price'] ?>" required>
        <input class="form-control" type="date" name="expiration_date" value="<?= $product['expiration_date'] ?>" required>
        <label>Upload New Image (optional):</label>
        <input class="form-control" type="file" name="image" accept="image/*">

        <button class="btn" type="submit">Update Product</button>
        <br>
        <hr>
        <a href="market.php">BACK</a>
    </form>
</div>
</body>
</html>
