<?php
// Start a session if necessary (only if using session for user authentication)
session_start();
include 'db_connection.php'; // Ensure this file contains your database connection code

$admin = $_SESSION['admin_username'];

$user_stmt = $conn->prepare("SELECT Adminid FROM admin WHERE username = ?");
$user_stmt->bind_param("s", $admin);
$user_stmt->execute();
$user_stmt->bind_result($adminid);
$user_stmt->fetch();
$user_stmt->close();


$message = ""; // Initialize the message variable

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize it to avoid SQL injection
    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $mobile = htmlspecialchars($_POST['number']);
    $email = htmlspecialchars($_POST['email']);
    $user_role = htmlspecialchars($_POST['user']);
    $createdby = $adminid;
    $updatedby = $adminid;

    // Prepare and execute the insert query
    $stmt = $conn->prepare("INSERT INTO registereduser (firstname, lastname, mobile, email, userrole, createdby, updatedby) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fname, $lname, $mobile, $email, $user_role, $createdby, $updatedby);

    if ($stmt->execute()) {
        $message = "<p class='success-message'>User added successfully!</p>"; // Set success message
    } else {
        $message = "<p class='error-message'>Error: " . $stmt->error . "</p>"; // Set error message
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        input[type="number"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button.adduser {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button.adduser:hover {
            background-color: #45a049;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-top: 20px;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="nav.php">Home</a>
        <a href="add_user.php">Add User</a>
        <a href="allowances.php">Add Allowance</a>
    </div>

    <div class="container">
        <form action="" method="post">
            <h2>Add New User</h2>

            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" required>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" required>

            <label for="number">Enter Mobile:</label>
            <input type="number" id="number" name="number" required>

            <label for="email">Enter Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="user">User Role:</label>
            <input type="text" id="user" name="user" required>

            <button class="adduser" type="submit">Add User</button>
        </form>
    </div>

        <!-- Display the message below the form -->
        <?php echo $message; ?>

</body>
</html>
