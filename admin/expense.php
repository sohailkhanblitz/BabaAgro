<?php
session_start();
include 'db_connection.php';  // Includes the database connection
$logged_mobile = $_SESSION['mobile'];
$message = '';
$sites = [];
$products = [];

// Fetching the user ID based on mobile number
$user_stmt = $conn->prepare("SELECT userid FROM registereduser WHERE mobile = ?");
$user_stmt->bind_param("s", $logged_mobile);
$user_stmt->execute();
$user_stmt->bind_result($userid);
$user_stmt->fetch();
$user_stmt->close();

// Fetch sites and products for the logged in user
if ($userid) {
    // Fetching sites and products from allowance table
    $stmt = $conn->prepare("SELECT DISTINCT site, product FROM allowancemaster WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($site, $product);

    while ($stmt->fetch()) {
        // Store sites and products in arrays
        if (!in_array($site, $sites)) {
            $sites[] = $site;
        }
        if (!in_array($product, $products)) {
            $products[] = $product;
        }
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assign form values to variables
    $site = $_POST['site'];
    $product = $_POST['product'];
    // $total_allowances = $_POST['text'];
    $expense_amount = $_POST['expense_amount'];
    $expense_header = $_POST['expense_header'];
    
    // Prepare the insert statement
    $stmt = $conn->prepare("INSERT INTO expense (Exid, expense_header, site, product, expense_amount, date, createdby, createddate, updatedby, updateddate) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Prepare values
    $exid = NULL; // If Exid is an auto-increment field, you can use NULL instead
    $date = date('Y-m-d'); // Use PHP to get the current date

    // Bind parameters, using appropriate types
    $createdby = $userid;  // Use the fetched user ID
    $updatedby = $userid;  // Use the fetched user ID
    $createddate = $date;  // Set created date
    $updateddate = $date;  // Set updated date

    $stmt->bind_param("issdssssss", $exid, $expense_header, $site, $product, $expense_amount, $date, $createdby, $createddate, $updatedby, $updateddate);

    if ($stmt->execute()) {
        $message = "Expense successfully added!";
    } else {
        $message = "Error: " . $stmt->error;
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
    <title>Add Expenses</title>
    <link rel="stylesheet" href="../css/expense.css">
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <h2>Add Expense</h2>

            <label for="site">Select Site:</label>
            <select name="site" required id="select">
                <option selected disabled>Select Sites</option>
                <?php foreach ($sites as $site) : ?>
                    <option value="<?php echo htmlspecialchars($site); ?>"><?php echo htmlspecialchars($site); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="product">Select Product:</label>
            <select name="product" required id="select">
                <option selected disabled>Select Product</option>
                <?php foreach ($products as $product) : ?>
                    <option value="<?php echo htmlspecialchars($product); ?>"><?php echo htmlspecialchars($product); ?></option>
                <?php endforeach; ?>
            </select><br><br>
<!-- 
            <label for="text">Total Allowances:</label>
            <input type="text" id="text" name="text" > -->

            <label for="expense_amount">Expense Amount:</label>
            <input type="number" id="expense_amount" name="expense_amount" step="0.01" required><br><br>

            <label for="expense_header">Expense Header:</label>
            <textarea id="expense_header" name="expense_header" rows="3" placeholder="Header (optional)"></textarea><br><br>

            <button type="submit">Submit</button>
            <?php echo $message; ?>
        </form>
    </div>
</body>
</html>
