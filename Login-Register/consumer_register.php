<?php

require "./Mail/email_function.php";

if (!empty($_POST)) {
    extract($_POST); 
    require "../db.php";

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $fail = "This email is already registered.";
    } else {
        $hashed = password_hash($pass, PASSWORD_BCRYPT);
        $result = sendVerificationEmail($email, $user, $hashed, $city, $district, "consumer");

        if ($result === true) {
            header("Location: ./Mail/verify.php");
            exit;
        } else {
            $fail = $result; 
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Consumer Register | EcoBasket</title>
    <link rel="stylesheet" href="../CSS/register.css">
</head>

<body>
    <header>
        Consumer Registration
    </header>
    <form action="?" method="post">
        <table>
            <tr>
                <td>Email :</td>
                <td><input type="text" name="email" required></td>
            </tr>
            <tr>
                <td>Fullname :</td>
                <td><input type="text" name="user" required></td>
            </tr>
            <tr>
                <td>Password :</td>
                <td><input type="password" name="pass" required></td>
            </tr>
            <tr>
                <td>City :</td>
                <td><input type="text" name="city" required></td>
            </tr>
            <tr>
                <td>District:</td>
                <td><input type="text" name="district" required></td>
            </tr>
            <tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="submit">Register</button>
                </td>
            </tr>
            </tr>
        </table>
    </form>

    <?php if (isset($fail)): ?>
        <p class="error"><?= htmlspecialchars($fail) ?></p>
    <?php endif; ?>
    <p><a href="./consumer_login.php">Back</a></p>
</body>

</html>