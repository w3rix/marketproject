<?php

// prevent unauthorized access
if (!isset($_SESSION["user"])) {
      header("Location: index.php") ;
      exit ;
}