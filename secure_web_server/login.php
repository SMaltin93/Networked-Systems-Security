<?php
// Start the session to store the 2FA code
session_start();

// Include PHPMailer classes for email sending
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Ensure this path points to Composer's autoload.php

// Define FreeRADIUS server settings
$radiusServer = "192.168.2.180";
$radiusSecret = "testing123";
$radiusPort = 1812;

// Retrieve username, password, and email from form
$user = escapeshellarg($_POST['username']);
$pass = escapeshellarg($_POST['password']);
$userEmail = $_POST['email']; // Email where the 2FA code will be sent

// Prepare the echo request string for radclient
$echoRequest = "User-Name={$user},User-Password={$pass}";

// Execute radclient to authenticate against the FreeRADIUS server
$command = "echo {$echoRequest} | radclient -x {$radiusServer}:{$radiusPort} auth {$radiusSecret}";
$output = shell_exec($command);

// Check radclient output for Access-Accept or Access-Reject
if (strpos($output, "Access-Accept") !== false) {
    // Authentication successful, generate and send 2FA code
    $randomCode = rand(100000, 999999);
    $_SESSION['2fa_code'] = $randomCode;
    $_SESSION['username'] = $_POST['username']; // Store the username for 2FA verification

    $mail = new PHPMailer(true);

    try {
        // Server settings for PHPMailer
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'xxxxxxxxxxxx@gmail.com'; // SMTP username
        $mail->Password = 'xxxx xxxx xxxx xxxx'; // SMTP password, use your generated app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('president.of.g4@gmail.com', 'Mailer');
        $mail->addAddress($userEmail); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your 2FA Code';
        $mail->Body    = "Your login verification code is: $randomCode";

        $mail->send();
        header('Location: enter-2fa.php'); // Redirect to enter the 2FA code
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        // Handle email sending failure
    }
} elseif (strpos($output, "Access-Reject") !== false) {
    echo "Invalid username or password."; // Handle invalid credentials
} else {
    echo "Authentication error."; // Handle other errors
}
?>
