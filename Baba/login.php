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

    if ($result_admin->num_rows > 0) {
        $admin = $result_admin->fetch_assoc();
        $_SESSION['user_type'] = 'admin';
        $_SESSION['logged_in_admin'] = $admin['first_name'] . ' ' . $admin['last_name'];
        $_SESSION['afirst_name'] = $admin['first_name'];
        $_SESSION['admin_mobile'] = $_POST['mobile'];
        $_SESSION['admin_id'] = $admin['ad_id'];
        header("Location: Home.php");
        exit;
    }

    // Check if the user is a regular user
    $query_user = "SELECT * FROM user_master WHERE mobile = ? AND password = ?";
    $stmt_user = $conn->prepare($query_user);
    $stmt_user->bind_param("ss", $mobile, $password);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $_SESSION['user_type'] = 'user';
        $_SESSION['logged_in_user'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_mobile'] = $_POST['mobile'];
        $_SESSION['ufirst_name'] = $user['first_name'];
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: Allotted_sites.php");
        exit;
    }

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
    <link rel="stylesheet" href="../Csss/login.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                var errorElement = document.getElementById("error-message");
                if (errorElement) {
                    errorElement.style.display = "none";
                }
            }, 3000);
        });
    </script>
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        <label for="mobile">Mobile Number:</label>
        <input type="text" id="mobile" name="mobile" required placeholder="Enter Number"><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter Password"><br><br>
        
        <?php if (isset($error_message)): ?>
            <p id="error-message" style="color:red;"><?= $error_message; ?></p>
        <?php endif; ?>

        <input type="submit" value="Login">
    </form>
</body>
</html>
