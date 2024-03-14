<!DOCTYPE html>
<html>
<head>
    <title>Enter 2FA Code</title>
</head>
<body>
    <h2>Enter 2FA Code</h2>
    <form action="verify-2fa.php" method="post">
        2FA Code:<br>
        <input type="text" name="twofa_code" required>
        <br><br>
        <input type="submit" value="Verify Code">
    </form>
</body>
</html>
