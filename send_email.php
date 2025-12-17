<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendOTP($user_email, $otp_code) {
    $mail = new PHPMailer(true);

    try {
        // 1. Server Settings (The Postman)
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'kimad7141@gmail.com';          // <--- EDIT THIS
        $mail->Password   = 'jnpo nbfk aror azcz';          // <--- PASTE CODE HERE
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $mail->Port       = 465;                                    

        // 2. Sender & Recipient
        $mail->setFrom('kimad7141@gmail.com', 'Smart Wallet'); // <--- EDIT THIS
        $mail->addAddress($user_email);                           

        // 3. Content
        $mail->isHTML(true);                                  
        $mail->Subject = 'Your Verification Code';
        $mail->Body    = '
            <div style="font-family: Arial, sans-serif; padding: 20px;">
                <h2>Smart Wallet Login</h2>
                <p>Your One-Time Password (OTP) is:</p>
                <h1 style="color: green; letter-spacing: 5px;">' . $otp_code . '</h1>
                <p>Do not share this code with anyone.</p>
            </div>
        ';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>