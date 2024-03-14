<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userInputCode = $_POST['twofa_code'];

    if (isset($_SESSION['2fa_code']) && $userInputCode == $_SESSION['2fa_code']) {
        // Redirect to succeed.php on successful 2FA verification
        header('Location: succeed.php');
        exit();
    } else {
        echo "Incorrect 2FA code. Try again.";
        echo "<br><a href='enter-2fa.php'>Back to 2FA verification</a>";
    }
} else {
    header('Location: enter-2fa.php');
    exit();
}
?>
