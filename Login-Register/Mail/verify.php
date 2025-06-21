<?php
session_start();
require_once '../../db.php';


if (!isset($_SESSION["pending_register"])) {
   if ($_SESSION["pending_register"]["type"] === "market") {
            header("Location: ../market_register.php");
        } else {
            header("Location: ../consumer_register.php");
        }
    exit;
}

$pending = $_SESSION["pending_register"];
$fail = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $enteredCode = $_POST["code"] ?? '';

    if ($enteredCode == $pending["code"]) {
        $stmt = $db->prepare("INSERT INTO users (type, email, name, password, city, district) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $pending["type"],
            $pending["email"],
            $pending["name"],
            $pending["password"],
            $pending["city"],
            $pending["district"]
        ]);

        if ($_SESSION["pending_register"]["type"] === "market") {
            header("Location: ../market_login.php");
        } else {
            header("Location: ../consumer_login.php");
        }
        unset($_SESSION["pending_register"]);

        exit;
    } else {
        $fail = "Incorrect verification code. Please check your email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 40px;
        }

        form {
            display: inline-block;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            padding: 10px;
            font-size: 1.2em;
            width: 200px;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            background-color: rgb(112, 12, 174);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1em;
        }

        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <h2>Verify Your Email</h2>
    <p>We have sent a 6-digit confirmation code to <strong><?= htmlspecialchars($pending["email"]) ?></strong></p>

    <form method="POST">
        <input type="text" name="code" placeholder="Enter code" required maxlength="6">
        <br>
        <button type="submit">Verify</button>
    </form>

    <?php if (!empty($fail)): ?>
        <p class="error"><?= htmlspecialchars($fail) ?></p>
    <?php endif; ?>

</body>

</html>