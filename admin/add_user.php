<?php
// Start a session if necessary (only if using session for user authentication)
// session_start();
include 'db_connection.php'; // Ensure this file contains your database connection code

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize it to avoid SQL injection
    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $mobile = htmlspecialchars($_POST['number']);
    $email = htmlspecialchars($_POST['email']);
    $user_role = htmlspecialchars($_POST['user']);

    // Prepare and execute the insert query
    $stmt = $conn->prepare("INSERT INTO registereduser (firstname, lastname, mobile, email, userrole) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fname, $lname, $mobile, $email, $user_role);

    if ($stmt->execute()) {
        echo "User added successfully!";
    } else {
        echo "Error: " . $stmt->error;
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
    <link rel="stylesheet" href="../css/add_user.css">
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <h2>Add New User</h2>

            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" required><br><br>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" required><br><br>

            <label for="number">Enter Mobile:</label>
            <input type="number" id="number" name="number" required><br><br>

            <label for="email">Enter Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="user">User Role:</label>
            <input type="text" id="user" name="user" required><br><br>

            <button class="adduser" type="submit">Add User</button>
        </form>
    </div>
</body>
</html>
