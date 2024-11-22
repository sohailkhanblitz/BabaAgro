<?php
session_start();
if (!isset($_SESSION['admin_mobile'])) {
    header("Location: login.php");
    exit();
}
include 'db_connection.php'; // Ensure this file contains your database connection code

$admin = $_SESSION['afirst_name'];

$user_stmt = $conn->prepare("SELECT ad_id FROM admin_master WHERE first_name = ?");
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
    $password = htmlspecialchars($_POST['password']); // Get the password field

    $createdby = $adminid;
    $updatedby = $adminid;

    // Check if the mobile number already exists
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM user_master WHERE mobile = ?");
    $check_stmt->bind_param("s", $mobile);
    $check_stmt->execute();
    $check_stmt->bind_result($mobile_exists);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($mobile_exists > 0) {
        $message = "<p class='error-message'>Error: Mobile number already exists!</p>"; // Set error message
    } else {
        // Prepare and execute the insert query for user_master table
        $stmt = $conn->prepare("INSERT INTO user_master (first_name, last_name, mobile, email, user_role, password, created_by, updated_by) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $fname, $lname, $mobile, $email, $user_role, $password, $createdby, $updatedby);

        if ($stmt->execute()) {
            $message = "<p class='success-message'>User added successfully!</p>"; // Set success message
        } else {
            $message = "<p class='error-message'>Error: " . $stmt->error . "</p>"; // Set error message
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link rel="stylesheet" href="../Csss/Add_user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-items">
    <a href="./logout.php"><i class="fas fa-sign-out-alt"></i></a>
      <a href="./Home.php">Home</a>
      <a href="./Sites.php">Sites</a>
      <a href="./Add_user.php">Add User</a>
      <a href="./Allowance.php">Add Allowance</a>
    </div>
</nav>

    <div class="container">
        <!-- Disable autocomplete for the entire form -->
        <form action="" method="post" autocomplete="off">
            <h2>Add New User</h2>

            <label for="fname">First Name: <span class="required">*</span></label>
            <input type="text" id="fname" name="fname" required autocomplete="off">

            <label for="lname">Last Name: <span class="required">*</span></label>
            <input type="text" id="lname" name="lname" required autocomplete="off">

            <label for="number">Enter Mobile: <span class="required">*</span></label>
            <input type="text" id="number" name="number" maxlength="10" pattern="\d{10}" required autocomplete="off">

            <label for="email">Enter Email:</label>
            <input type="email" id="email" name="email" autocomplete="off"> <!-- Disable autocomplete -->

            <label for="password">Password: <span class="required">*</span></label>
            <input type="password" id="password" name="password" required autocomplete="new-password"> <!-- Disable password autofill -->

            <label for="user">User Role: <span class="required">*</span></label>
            <select name="user" required autocomplete="off">
                <option value="" selected disabled>Select Role</option>
                <option value="Employee">Employee</option>
                <option value="Contractor">Contractor</option>
            </select>
            
            <button class="adduser" type="submit">Add User</button>
        </form>
        <?php echo $message; ?>
    </div>

</body>
</html>
