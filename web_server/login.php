<?php
// Define FreeRADIUS server settings
$radiusServer = "192.168.2.180";
$radiusSecret = "testing123"; // The shared secret for your FreeRADIUS server
$radiusPort = 1812; // Default port for RADIUS authentication

// Retrieve username and password from form
$user = escapeshellarg($_POST['username']);
$pass = escapeshellarg($_POST['password']);

// Prepare the echo request string for radclient
$echoRequest = "User-Name={$user},User-Password={$pass}";

// Execute radclient
$command = "echo {$echoRequest} | radclient -x {$radiusServer}:{$radiusPort} auth {$radiusSecret}";
$output = shell_exec($command);

// Check radclient output for Access-Accept or Access-Reject
if (strpos($output, "Access-Accept") !== false) {
    echo "Login successful!";
    // Perform actions after successful login (e.g., redirect to another page)
} elseif (strpos($output, "Access-Reject") !== false) {
    echo "Invalid username or password.";
} else {
    echo "Authentication error.";
}
?>
    