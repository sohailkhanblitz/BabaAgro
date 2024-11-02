<?php
session_start();
include 'db_connection.php'; // Ensure db_connect.php connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = $_POST['mobile'];

    // Prepare and execute the query to check if the mobile number exists
    $stmt = $conn->prepare("SELECT mobile FROM registereduser WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Mobile number exists, redirect to expense.php
        $_SESSION['mobile'] = $mobile; // Store the mobile in session
        header("Location: expense.php");
        exit();
    } else {
        // Mobile number not found, show an error message
        $error = "Mobile number not found. Please try again.";
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
    <title>Login</title>
    <link rel="stylesheet" href="../css/expense.css">
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <h2>User Login</h2>
            <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" required placeholder="Enter your mobile number">
            <button type="submit">Login</button>
            <a href="admin.php">Admin Login</a>
        </form>
    </div>
</body>
</html>


<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="../css/labour.css">
</head>
<body>
    <div class="container">
        <form action="expense.php" method="post">
            <h2>User Login</h2>

            <label for="number">Enter Mobile:</label>
            <div class="input-group">
                <input type="number" id="number" name="number" required>
                <button class="btn1" type="submit">Search</button>
            </div> -->
<!-- 
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" readonly><br><br>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" readonly><br><br>

            <label for="user">User Type:</label>
            <input type="text" id="user" name="user" readonly><br><br>
             -->
            <!-- <button class="btn2" type="submit">Add Expense</button>
        </form>
    </div>
</body>
</html> -->
