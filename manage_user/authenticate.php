<?php
session_start(); // Start the session at the beginning

$servername = "localhost";
$username = "raduser";
$password = "radpass";
$dbname = "radius";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve username and password from the form
$user = $_POST['username'];
$pass = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM radcheck WHERE username=? AND attribute='Cleartext-Password' AND value=?");
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Authentication successful
    echo "Login successful";
    // Start a session or set session variables if needed
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $user;
    // Show buttons for managing users and logging out
    echo "<form action='manage_users.php' method='get'>
            <input type='submit' value='Manage Users'>
          </form>";
    echo "<form action='logout.php' method='post'>
            <input type='submit' name='logout' value='Log Out'>
          </form>";
} else {
    echo "Invalid username or password";
}

$stmt->close();
$conn->close();
?>
