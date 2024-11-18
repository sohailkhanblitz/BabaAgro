<?php
session_start();
include('db_connection.php'); // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    // Check if the user is an admin
    $query_admin = "SELECT * FROM admin_master WHERE mobile = ? AND password = ?";
    $stmt_admin = $conn->prepare($query_admin);
    $stmt_admin->bind_param("ss", $mobile, $password);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    // Check if the user is in the admin table
    if ($result_admin->num_rows > 0) {
        $admin = $result_admin->fetch_assoc();
        $_SESSION['user_type'] = 'admin';
        $_SESSION['logged_in_admin'] = $admin['first_name'] . ' ' . $admin['last_name'];
        $_SESSION['afirst_name']=$admin['first_name'];
        $_SESSION['admin_mobile']=$_POST['mobile'];
        // Store full name as user_name
        $_SESSION['admin_id'] = $admin['ad_id']; // Store admin ID in session
        header("Location: home.php"); // Redirect to admin home page
        exit;
    }

    // Check if the user is a regular user
    $query_user = "SELECT * FROM user_master WHERE mobile = ? AND password = ?";
    $stmt_user = $conn->prepare($query_user);
    $stmt_user->bind_param("ss", $mobile, $password);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    // Check if the user is in the user table
    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $_SESSION['user_type'] = 'user';
        $_SESSION['logged_in_user'] = $user['first_name'] . ' ' . $user['last_name']; 
        $_SESSION['user_mobile']=$_POST['mobile'];
        $_SESSION['ufirst_name']=$user['first_name'];
        // Store full name as user_name
        $_SESSION['user_id'] = $user['user_id']; // Store user ID in session
        header("Location: expence.php"); // Redirect to user expense page
        exit;
    }

    // If no matching user is found in both tables
    $error_message = "Invalid password or user not found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>

    <form method="POST" action="">
        <label for="mobile">Mobile Number:</label>
        <input type="text" id="mobile" name="mobile" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>
<?php

// session_destroy();
?>