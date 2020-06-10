<?php
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

$mail = new PHPMailer\PHPMailer\PHPMailer;
$mail->SMTPDebug = 0;           // Enable verbose debug output. Change to 0 to disable debugging output.

$mail->isSMTP();                // Set mailer to use SMTP.
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
$mail->SMTPAuth = true;         // Enable SMTP authentication


$mail->Username = EMAIL_USER;   // SMTP username
$mail->Password = EMAIL_PASS;   // SMTP password
$mail->SMTPSecure = 'tls';      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
$mail->Port = 587;              // TCP port to connect to
$options = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->smtpConnect($options);
$mail->From = $formData['email'];  // sender's email address (shows in "From" field)
$mail->FromName = $formData['name'];   // sender's name (shows in "From" field)
$mail->addAddress('johntheholman@gmail.com', 'John Holman');  // Add a recipient (name is optional)
//$mail->addAddress('ellen@example.com');                        // Add a second recipient
$mail->addReplyTo($formData['email']);                          // Add a reply-to address
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $formData['name']. ' has sent you a new message | johntheholman.com';
$mail->Body    = 'Name: '.$formData['name'].'<br>'.
                    'Email: '.$formData['email'].'<br>'.
                    'Phone: '.$formData['phone'].'<br>'.
                    'Message: '.$formData['message'].'<br>'.
                    '<br>'.
                    'This message whas sent from the contact form on johntheholman.com';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>
