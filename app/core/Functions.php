<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($emailTo, $subject, $content){

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                     
    $mail->isSMTP();                                         
    $mail->Host       = 'smtp.gmail.com';                   
    $mail->SMTPAuth   = true;                                
    $mail->Username   = 'dat82770@gmail.com';                
    $mail->Password   = 'vutboygsxpcimrmw';                 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         
    $mail->Port       = 465;                                

    $mail->setFrom('dat82770@gmail.com', 'datweb');
    $mail->addAddress($emailTo);     

    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);                                  
    $mail->Subject = $subject;
    $mail->Body    = $content;

    return $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}

?>
