<?php
session_start();
include 'db_connection.php';  // Includes the database connection

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assign form values to variables
    $site = $_POST['site'];
    $product = $_POST['product'];
    $total_allowances = $_POST['text'];
    $expense_amount = $_POST['expense_amount'];
    $expense_header = $_POST['expense_header'];
    
    // Prepare the insert statement
    $stmt = $conn->prepare("INSERT INTO expense (Exid, expense_header, site, product, expense_amount, date, createdby, createddate, updatedby, updateddate) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Prepare values
    $exid = NULL; // If Exid is an auto-increment field, you can use NULL instead
    $date = date('Y-m-d'); // Use PHP to get the current date

    // Bind parameters, using appropriate types
    $createdby = 1;  // Adjust this as needed
    $updatedby = 1;  // Adjust this as needed


    $stmt->bind_param("issdssssss", $exid, $expense_header, $site, $product, $expense_amount, $date, $createdby, $createddate, $updatedby, $updateddate);

    if ($stmt->execute()) {
        $message = "Expense successfully added!";
        // echo  $message;
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
    <title>Add Expences</title>
    <link rel="stylesheet" href="../css/expense.css">
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <h2>Add Expense</h2>

       
            <label for="site">Select Site:</label>
            <select name="site" required id="select">
                <option selected disabled>Select Sites</option>
                <option value="1">Site1</option>
                <option value="2">Site2</option>
                <option value="3">Site3</option>
                <option value="4">Site4</option>
            </select><br><br>

            <label for="product">Select Product:</label>
            <select name="product" required id="select">
                <option selected disabled>Select Product</option>
                <option value="1">Product1</option>
                <option value="2">Product2</option>
                <option value="3">Product3</option>
                <option value="4">Product4</option>
            </select><br><br>

           
            <label for="text">Total Allowenss:</label>
            <input type="text" id="text" name="text" >

            <!-- New Expense Fields -->
            <label for="expense_amount">Expense Amount:</label>
            <input type="number" id="expense_amount" name="expense_amount" step="0.01" required><br><br>

            <label for="expense_header">Expense Header:</label>
            <textarea id="expense_header" name="expense_header" rows="3" placeholder="Header (optional)"></textarea><br><br>
            

            <button type="submit">Submit</button>
            <?php
            
            echo $message;
            
            ?>
        </form>
    </div>
</body>
</html>
