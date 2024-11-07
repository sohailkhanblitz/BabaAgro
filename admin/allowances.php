<?php
// Include your database connection file
session_start();

$admin = $_SESSION['admin_username'];
include 'db_connection.php';

// Fetch admin ID
$user_stmt = $conn->prepare("SELECT Adminid FROM admin WHERE username = ?");
$user_stmt->bind_param("s", $admin);
$user_stmt->execute();
$user_stmt->bind_result($adminid);
$user_stmt->fetch();
$user_stmt->close();

// Initialize variables to hold user and site options
$userOptions = "";
$siteOptions = "";

// Fetch registered users from the database
$sql = "SELECT userid, firstname, lastname FROM registereduser";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userOptions .= "<option value='" . $row['userid'] . "'>" . $row['firstname'] . " " . $row['lastname'] . "</option>";
    }
} else {
    $userOptions = "<option disabled>No registered users available</option>";
}

// Fetch existing sites from the database
$site_sql = "SELECT DISTINCT site FROM allowancemaster";
$site_result = $conn->query($site_sql);
if ($site_result->num_rows > 0) {
    while ($site_row = $site_result->fetch_assoc()) {
        $siteOptions .= "<option value='" . $site_row['site'] . "'>";
    }
}

// Check if the form is submitted
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userid = $_POST['userid'];
    $product = $_POST['product'];
    $site = $_POST['site'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Insert data into allowancemaster
    $stmt = $conn->prepare("INSERT INTO allowancemaster (userid, product, site, amount, date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $userid, $product, $site, $amount, $date, $status);

    if ($stmt->execute()) {
        $message = "<p class='success-message'>Allowance added successfully!</p>";
    } else {
        $message = "<p class='error-message'>Error: " . $stmt->error . "</p>";
    }

    // Close statement
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Allowance</title>
    <link rel="stylesheet" href="../css/allowance.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div>
            <a href="nav.php">Home</a>
            <a href="add_user.php">Add User</a>
            <a href="allowances.php">Add Allowance</a>
        </div>
        <div>
            <span>Welcome, <?php echo $admin; ?> | </span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <form action="" method="post">
            <h2>Add Allowance</h2>

            <label for="userid">Select User:</label>
            <select name="userid" required>
                <option selected disabled>Select User</option>
                <?php echo $userOptions; ?>
            </select>

            <label for="product">Product:</label>
            <input type="text" id="product" name="product" required>

            <label for="site">Site:</label>
            <input type="text" id="site" name="site" list="siteList" required>
            <datalist id="siteList">
                <?php echo $siteOptions; ?>
            </datalist>

            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="status">Status:</label>
            <select name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="done">Done</option>
            </select>

            <button class="addallow" type="submit">Submit</button>
        </form>

        <?php echo $message; ?>
    </div>
</body>
</html>
