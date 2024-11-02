<?php
// Include your database connection file
include 'db_connection.php';

// Initialize an empty variable to hold user options
$userOptions = "";

// Fetch registered users from the database
$sql = "SELECT userid, firstname, lastname FROM registereduser"; // Adjust according to your table structure
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each row and create an option for the dropdown
    while ($row = $result->fetch_assoc()) {
        // Option value is userid; display is firstname and lastname
        $userOptions .= "<option value='" . $row['userid'] . "'>" . $row['firstname'] . " " . $row['lastname'] . "</option>";
    }
} else {
    // If no users found, add a disabled option
    $userOptions = "<option disabled>No registered users available</option>";
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userid = $_POST['userid'];  // Ensure `userid` is the correct name if from a dropdown
    $product = $_POST['product'];
    $site = $_POST['site'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    // Prepare the SQL statement to insert data into allowancemaster
    $stmt = $conn->prepare("INSERT INTO allowancemaster (userid, product, site, amount, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userid, $product, $site, $amount, $date);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "Allowance added successfully!";
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
    <title>Add Allowance</title>
    <link rel="stylesheet" href="../css/add_user.css">
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <h2>Add Allowance</h2>

            <label for="userid">Select User:</label>
            <select name="userid" required>
                <option selected disabled>Select User</option>
                <?php echo $userOptions; ?> <!-- Insert user options here -->
            </select><br><br>

            <label for="product">Product:</label>
            <input type="text" id="product" name="product" required><br><br>

            <label for="site">Site:</label>
            <input type="text" id="site" name="site" required><br><br>

            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required><br><br>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br><br>

            <button class="addallow" type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
