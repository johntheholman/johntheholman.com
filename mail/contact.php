<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('contact_config.php');
require_once('./src/Exception.php');
require_once('./src/PHPMailer.php');
require_once('./src/SMTP.php');

$formName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$formEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$formPhone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
$formMessage = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

$formData = [
    'name' => $formName,
    'email' => $formEmail,
    'phone' => $formPhone,
    'message' => $formMessage,
];

$mail = new PHPMailer(true);

try {
    //Server Settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;           // Enable verbose debug output. Change to 0 to disable debugging output.
    $mail->isSMTP();                // Set mailer to use SMTP.
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
    $mail->SMTPAuth = true;         // Enable SMTP authentication
    $mail->Username = EMAIL_USER;   // SMTP username
    $mail->Password = EMAIL_PASS;   // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
    $mail->Port = 587;              // TCP port to connect to

    //Recipients
    $mail->setFrom($formData['email'], $formData['name']);         // sender's email address and name (shows in "From" field)
    $mail->addAddress('johntheholman@gmail.com', 'John Holman');   // Add a recipient (name is optional)
    //$mail->addAddress('ellen@example.com');                      // Add a second recipient
    $mail->addReplyTo($formData['email'], $formData['name']);                         // Add a reply-to address
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $formData['name']. " has sent you a new message | johntheholman.com";
    $mail->Body    = 'Name: '.$formData['name'].'<br>'.
                     'Email: '.$formData['email'].'<br>'.
                     'Phone: '.$formData['phone'].'<br>'.
                     'Message: '.$formData['message'].'<br>'.
                     '<br>'.
                     'This message whas sent from the contact form on johntheholman.com';
    $mail->AltBody = 'Name: '.$formData['name']. ' | '
                     'Email: '.$formData['email'].' | '.
                     'Phone: '.$formData['phone'].' | '.
                     'Message: '.$formData['message'].' | '.
                     'This message whas sent from the contact form on johntheholman.com';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
