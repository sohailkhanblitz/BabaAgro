<?php
session_start();
include 'db_connection.php'; // Ensure this file has your database connection details

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if username exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the password
        if ($password === $hashed_password) { // Replace with password_verify($password, $hashed_password) if passwords are hashed
            $_SESSION['admin_username'] = $username;
            header("Location: nav.php");
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "Username not found. Please try again.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <h2>Admin Login</h2>
            <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password"  placeholder="Enter Password" required><br><br>

            <button type="submit" name="login">Submit</button>

            <a href="labour.php">User Login</a>
        </form>
    </div>
</body>
</html>
