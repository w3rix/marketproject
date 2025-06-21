<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
require_once "../Login-Register/Mail/vendor/autoload.php"; 

function sendVerificationEmail($email, $name, $passwordHash, $city, $district, $type) {
    $code = mt_rand(100000, 999999);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';               
        $mail->SMTPAuth   = true;                             
        $mail->Username   = 'ctis256projectsamfur@gmail.com';         
        $mail->Password   = 'abls bjqy kqsu xian';                  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
        $mail->Port       = 587;                              

        $mail->setFrom('ctis256projectsamfur@gmail.com', 'EcoBasket');
        $mail->addAddress($email, $name);                 
        $mail->isHTML(true);
        $mail->Subject = 'EcoBasket Email Verification Code';
        $mail->Body    = "Your verification code is: <strong>$code</strong>";

        $mail->send();

        $_SESSION["pending_register"] = [
            "email" => $email,
            "name" => $name,
            "password" => $passwordHash,
            "city" => $city,
            "district" => $district,
            "type" => $type,
            "code" => $code
        ];

        return true;
    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}";
    }
}
