<?php
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
$duplicate_mobile = "";
$duplicate_email = "";
$duplicate_name = "";

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

    // Check for duplicate mobile number
    $stmt_check_mobile = $conn->prepare("SELECT 1 FROM registereduser WHERE mobile = ?");
    $stmt_check_mobile->bind_param("s", $mobile);
    $stmt_check_mobile->execute();
    $stmt_check_mobile->store_result();

    // Check for duplicate email
    $stmt_check_email = $conn->prepare("SELECT 1 FROM registereduser WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    // Check for duplicate first name and last name combination
    $stmt_check_name = $conn->prepare("SELECT 1 FROM registereduser WHERE firstname = ? AND lastname = ?");
    $stmt_check_name->bind_param("ss", $fname, $lname);
    $stmt_check_name->execute();
    $stmt_check_name->store_result();

    if ($stmt_check_mobile->num_rows > 0) {
        $duplicate_mobile = " - Duplicate mobile number!";
    }

    if ($stmt_check_email->num_rows > 0) {
        $duplicate_email = " - Duplicate email!";
    }

    if ($stmt_check_name->num_rows > 0) {
        $duplicate_name = " - Duplicate name!";
    }

    // Only proceed with insertion if no duplicates were found
    if ($stmt_check_mobile->num_rows == 0 && $stmt_check_email->num_rows == 0 && $stmt_check_name->num_rows == 0) {
        // Prepare and execute the insert query
        $stmt = $conn->prepare("INSERT INTO registereduser (firstname, lastname, mobile, email, userrole, createdby, updatedby) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fname, $lname, $mobile, $email, $user_role, $createdby, $updatedby);

        if ($stmt->execute()) {
            $message = "<p class='success-message'>User added successfully!</p>"; // Set success message
        } else {
            $message = "<p class='error-message'>Error: " . $stmt->error . "</p>"; // Set error message
        }

        // Close the statement
        $stmt->close();
    } else {
        $message = "<p class='error-message'>Please fix the duplicate entries before submitting.</p>";
    }

    // Close the statement for duplicate checks
    $stmt_check_mobile->close();
    $stmt_check_email->close();
    $stmt_check_name->close();
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

    <!-- Navigation Bar -->
    <div class="navbar">
        <div>
            <a href="nav.php">Home</a>
        </div>

        <div>
            <a href="add_user.php">Add User</a>
        </div>
        
        <div>
            <a href="allowances.php">Add Allowance</a>
        </div>
    </div>

    <div class="container">
        <form action="" method="post">
            <h2>Add New User</h2>

            <label for="fname">First Name: <span class="required">*</span></label>
            <input type="text" id="fname" name="fname" required>
            <span class="error-message"><?php echo $duplicate_name; ?></span>

            <label for="lname">Last Name: <span class="required">*</span></label>
            <input type="text" id="lname" name="lname" required>
            <span class="error-message"><?php echo $duplicate_name; ?></span>

            <label for="number">Enter Mobile: <span class="required">*</span></label>
            <input type="text" id="number" name="number" maxlength="10" pattern="\d{10}" required>
            <span class="error-message"><?php echo $duplicate_mobile; ?></span>

            <label for="email">Enter Email:</label>
            <input type="email" id="email" name="email" >
            <span class="error-message"><?php echo $duplicate_email; ?></span>

            <label for="user">User Role: <span class="required">*</span></label>
            <select name="user" required>
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
