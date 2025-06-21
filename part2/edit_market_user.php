<?php
session_start();
require_once '../db.php';

$market_id = $_SESSION["user"]["id"] ? $_SESSION["user"]["id"] : null;


$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$market_id]);
$market = $stmt->fetch();

if (!$market) {
    echo "Market not found.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = "market";
    $email = $_POST['email'];
    $name = $_POST['name'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $new_password = $_POST['password'];

    
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $db->prepare("UPDATE users SET  email=?, name=?, city=?, district=?, password=? WHERE id=?");
        $update->execute([ $email, $name, $city, $district, $hashed_password, $market_id]);
    } else {
        $update = $db->prepare("UPDATE users SET  email=?, name=?, city=?, district=? WHERE id=?");
        $update->execute([ $email, $name, $city, $district, $market_id]);
    }


    $location = $_SESSION["user"]["type"]=="market" ? "market" : "../CartOperations/main";
    header("Location: ". $location. ".php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Market Register | EcoBasket</title>
    <link rel="stylesheet" href="../CSS/editmarket.css">
</head>

<body>
    <h1>Edit Profile</h1>
    </div>

    <form action="?" method="post">
        <table>
            <tr>
                <td>Email:</td>
                <td><input type="text" name="email" value="<?= htmlspecialchars($market['email']) ?>" required></td>
            </tr>
            <tr>
                <td>Market Name:</td>
                <td><input type="text" name="name" value="<?= htmlspecialchars($market['name']) ?>" required></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" placeholder="************" required></td>
            </tr>
            <tr>
                <td>City:</td>
                <td><input type="text" name="city" value="<?= htmlspecialchars($market['city']) ?>" required></td>
            </tr>
            <tr>
                <td>District:</td>
                <td><input type="text" name="district" value="<?= htmlspecialchars($market['district']) ?>" required></td>
            </tr>
            <tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="submit">Save</button>
                </td>
            </tr>
            </tr>
        </table>
    </form>

    <?php if (isset($fail)): ?>
        <p class="error"><?= htmlspecialchars($fail) ?></p>
    <?php endif; ?>
    <p><a href="<?= $_SESSION["user"]["type"]=="market" ? "market" : "../CartOperations/main" ?>.php">Back</a></p>
</body>
</html>