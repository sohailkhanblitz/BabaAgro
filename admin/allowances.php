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

// Initialize an empty variable to hold user options
$userOptions = "";

// Fetch registered users from the database
$sql = "SELECT userid, firstname, lastname FROM registereduser"; // Adjust according to your table structure
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Option value is userid; display is firstname and lastname
        $userOptions .= "<option value='" . $row['userid'] . "'>" . $row['firstname'] . " " . $row['lastname'] . "</option>";
    }
} else {
    $userOptions = "<option disabled>No registered users available</option>";
}

// Check if the form is submitted
$message = ""; // Initialize the message variable
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userid = $_POST['userid'];
    $product = $_POST['product'];
    $site = $_POST['site'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status']; // Added status field

    // Prepare the SQL statement to insert data into allowancemaster
    $stmt = $conn->prepare("INSERT INTO allowancemaster (userid, product, site, amount, date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $userid, $product, $site, $amount, $date, $status); // Bind status

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $message = "<p class='success-message'>Allowance added successfully!</p>";
    } else {
        $message = "<p class='error-message'>Error: " . $stmt->error . "</p>";
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
    <title>Add Allowance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            padding: 10px;
            color: white;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
        }
        .navbar a:hover {
            background-color: #575757;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button.addallow {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button.addallow:hover {
            background-color: #45a049;
        }
        .success-message {
            color: green;
            text-align: center;
        }
        .error-message {
            color: red;
            text-align: center;
        }
    </style>
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
                <?php echo $userOptions; ?> <!-- Insert user options here -->
            </select>

            <label for="product">Product:</label>
            <input type="text" id="product" name="product" required>

            <label for="site">Site:</label>
            <input type="text" id="site" name="site" required>

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

        <!-- Display the message below the form -->
        <?php echo $message; ?>
    </div>
</body>
</html>
