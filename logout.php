<?php
   session_start() ;
   require "./protect.php" ;

   require "db.php" ;


   // delete remember me part
   setTokenByEmail($_SESSION["user"]["email"], 0) ;
   setcookie("access_token", "", 1) ; 

   // delete session file
   session_destroy() ;
   // delete session cookie
   setcookie("PHPSESSID", "", 1 , "/") ; // delete memory cookie 

   // redirect to login page.
   header("Location: index.php") ;