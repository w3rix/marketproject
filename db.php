<?php

const DSN = "mysql:host=localhost;dbname=test5;charset=utf8mb4" ;
const USER = "std" ;
const PASSWORD = "" ;

try {
   $db = new PDO(DSN, USER, PASSWORD) ; 
} catch(PDOException $e) {
     echo "Set username and password in 'db.php' appropriately" ;
     exit ;
}
 
 function checkUser($email, $pass, &$user,$expectedType = null) {
     global $db ;

     $stmt = $db->prepare("select * from users where email=?") ;
     $stmt->execute([$email]) ;
     $user = $stmt->fetch() ;

     if ($expectedType && $user["type"] !== $expectedType) {
      return false;
  }
  
     return $user ? password_verify($pass, $user["password"]) : false ;
 }

 // Remember me
 function getUserByToken($token) {
    global $db ;
    $stmt = $db->prepare("select * from users where remember = ?") ;
    $stmt->execute([$token]) ;
    return $stmt->fetch() ;
 }

 function setTokenByEmail($email, $token) {
    global $db ;
    $stmt = $db->prepare("update users set remember = ? where email = ?") ;
    $stmt->execute([$token, $email]) ;
 }

 
 
 

