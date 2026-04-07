<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($emailTo, $subject, $content){

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                         //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                    //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                //Enable SMTP authentication
    $mail->Username   = 'dat82770@gmail.com';                //SMTP username
    $mail->Password   = 'vutboygsxpcimrmw';                  //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         //Enable implicit TLS encryption
    $mail->Port       = 465;                                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('dat82770@gmail.com', 'datweb');
    $mail->addAddress($emailTo);     //Add a recipient

    //Content
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $content;

    return $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}

?>
