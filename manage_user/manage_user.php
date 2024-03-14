
<?php
// Start the session
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit;
}

$servername = "localhost";
$username = "raduser"; // Database username
$password = "radpass"; // Database password
$dbname = "radius"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a delete request was made
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $deleteId = $conn->real_escape_string($_GET['delete_id']);
   
    // statement to delete user
    $stmt = $conn->prepare("DELETE FROM radcheck WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
   
    if ($stmt->execute()) {
        $_SESSION['message'] = "<p>User deleted successfully.</p>";
    } else {
        $_SESSION['message'] = "<p>Error deleting user: " . $stmt->error . "</p>";
    }
    $stmt->close();
   
    // avoid re-submission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Attempt to add user if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $newUsername = $conn->real_escape_string($_POST['username']);
    $newPassword = $conn->real_escape_string($_POST['password']);
   
    // Prepare statement to insert new user
    $stmt = $conn->prepare("INSERT INTO radcheck (username, attribute, op, value) VALUES (?, 'Cleartext-Password', ':=', ?)");
    $stmt->bind_param("ss", $newUsername, $newPassword);
   
    if ($stmt->execute()) {
        $_SESSION['message'] = "<p>User added successfully.</p>";
    } else {
        $_SESSION['message'] = "<p>Error adding user: " . $stmt->error . "</p>";
    }
    $stmt->close();
   
    // Redirect to avoid re-submission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}

echo "<h2>Users</h2>";

// Display users
$sql = "SELECT id, username FROM radcheck";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        // Removed the part where the ID was echoed.
        echo "<li>Username: " . $row["username"] . " <a href='?delete_id=" . $row["id"] . "'>Delete</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p>0 results</p>";
}


?>

<h3>Add User</h3>
<form method="post" action="">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Add User">
</form>

<form action="logout.php" method="post">
    <input type="submit" name="logout" value="Log Out">
</form>

<?php
$conn->close();
?>