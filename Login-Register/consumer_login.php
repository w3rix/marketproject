<?php
session_start();

if (isset($_SESSION["user"]) && $_SESSION["user"]["type"] === "consumer") {
  header("Location: ../CartOperations/main.php");
  exit;
}

require "../db.php";

if (isset($_COOKIE["consumer_access_token"])) {
  $user = getUserByToken($_COOKIE["consumer_access_token"]);
  if ($user && $user["type"] === 'consumer') {
    $_SESSION["user"] = $user;
    header("Location: ../CartOperations/main.php");
    exit;
  }
}

if (!empty($_POST)) {
  extract($_POST);
  if (checkUser($email, $pass, $user, 'consumer')) {

    $token = bin2hex(random_bytes(32));
    setcookie("consumer_access_token", $token, time() + 60 * 60);
    setTokenByEmail($email, $token);

    $_SESSION["user"] = $user;
    header("Location: ../CartOperations/main.php");
    exit;
  } else {
    $fail = true;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Consumer Register | EcoBasket</title>
  <link rel="stylesheet" href="../CSS/style.css">
</head>

<body>
  <header>
    Login as a Consumer
  </header>

  <form action="?" method="post">
    <table>
      <tr>
        <td>Email :</td>
        <td><input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></td>
      </tr>
      <tr>
        <td>Password : </td>
        <td><input type="password" name="pass"></td>
      </tr>

      <tr>
        <td colspan="2" style="text-align: center;"><button><i class="fa fa-right-to-bracket"></i> Login</button></td>
      </tr>
    </table>
  </form>


  <p><a href="./consumer_register.php">Don't you have an Account? Register!</a></p>
  <p><a href="../index.php">Back</a></p>

  <?php
  if (isset($fail)) {
    echo "<p class='error'>Wrong email or password</p>";
  }
  if (isset($_GET["error"])) {
    echo "<p class='error'>You tried to access main.php directly</p>";
  }
  ?>



</body>

</html>