<?php
// PHPMailer setup file - Manual installation (no Composer)

// Include PHPMailer classes directly
require_once 'lib/PHPMailer/PHPMailer.php';
require_once 'lib/PHPMailer/SMTP.php';
require_once 'lib/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
    
function sendEmail($to, $subject, $body, $from_name= "Hostel Management System", $from_email = null) {
    // If no from_email is specified, use the SMTP username (Gmail address)
    if ($from_email === null) {
        $from_email = 'gadambharat3833@gmail.com';
    }
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;              // Enable verbose debug output (uncomment for debugging)
            $mail->isSMTP();                                      // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                 // SMTP server - change to your SMTP server
            $mail->SMTPAuth   = true;                             // Enable SMTP authentication
            $mail->Username   = 'gadambharat3833@gmail.com';           // SMTP username - change to your email
            $mail->Password   = 'ylnw tivl dovo kdra';              // SMTP password - change to your password/app-password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption
            $mail->Port       = 587;                              // TCP port to connect to (587 for TLS, 465 for SSL)
            
            // Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));
            
            $mail->send();
            return [
                'success' => true,
                'message' => 'Email has been sent successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"
            ];
        }
    }
?>