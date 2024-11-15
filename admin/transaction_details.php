<?php
session_start();
include 'db_connection.php';

// Initialize variables
$site = $product = $userid = null;

// Check if site, product, and userid are passed via GET, if not, fetch from the database
if (isset($_GET['site']) && isset($_GET['product']) && isset($_GET['userid'])) {
    // Fetch data from GET parameters
    $site = $_GET['site'];
    $product = $_GET['product'];
    $userid = $_GET['userid'];
} else {
    // If parameters are missing, fetch the first available data from the database
    $stmt = $conn->prepare("
        SELECT e.site, e.product, e.createdby 
        FROM expense e 
        LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($site, $product, $userid);
    $stmt->fetch();
    $stmt->close();
}

if ($site && $product && $userid) {
    // Fetch user information for the given userid
    $stmt = $conn->prepare("SELECT userid, firstname, lastname, mobile, email, userrole FROM registereduser WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($userid, $firstname, $lastname, $mobile, $email, $userrole);
    $stmt->fetch();
    $stmt->close();

    // Fetch all transactions for the given site, product, and createdby (userid)
    $stmt = $conn->prepare("
        SELECT e.exid, e.expense_amount, e.createddate, e.expense_header, e.file_path
        FROM expense e
        WHERE e.site = ? AND e.product = ? AND e.createdby = ?
    ");
    $stmt->bind_param("ssi", $site, $product, $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    $stmt->close();
} else {
    $transactions = [];
    $errorMessage = "Missing or invalid parameters.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details</title>
    <link rel="stylesheet" href="../css/transaction_details.css">
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="nav.php">Home</a>
        <a href="add_user.php">Add User</a>
        <a href="allowances.php">Add Allowance</a>
    </div>

    <div class="container">

       <!-- <div class="btn">
           <a href="nav.php"><button class="bbtn">Back</button></a>       
       </div> -->

        <?php if (isset($site) && isset($product) && isset($userid)) { ?>
        <p class="site-product-info">
            <span class="site">Site: <?php echo htmlspecialchars($site); ?></span>
            <span class="product">Product: <?php echo htmlspecialchars($product); ?></span>
        </p>

        <!-- User Info -->
        
        <div class="user-info">
            <h2>User Information</h2>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>User Role</th>
                </tr>
                <tr>
                    <td><?php echo htmlspecialchars($userid); ?></td>
                    <td><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></td>
                    <td><?php echo htmlspecialchars($mobile); ?></td>
                    <td><?php echo htmlspecialchars($email); ?></td>
                    <td><?php echo htmlspecialchars($userrole); ?></td>
                </tr>
            </table>
        </div>
        <!-- Transaction Info -->
        <div class="transaction-info">
            <h2>Expense History</h2>
            <?php if (empty($transactions)) { ?>
                <p>No transactions found for this site and product.</p>
            <?php } else { ?>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Expense Header</th>
                            <th>Expense Amount</th>
                            <th>Expense Date</th>
                            <th>Uploaded File</th> <!-- New Column for Uploaded File -->
                        </tr>
                        <?php foreach ($transactions as $transaction) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['expense_header']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['expense_amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['createddate']); ?></td>
                                <td>
                                    <?php 
                                    if ($transaction['file_path']) {
                                        echo '<a href="' . htmlspecialchars($transaction['file_path']) . '" target="_blank">View File</a>';
                                    } else {
                                        echo 'NA'; // Display 'NA' if no file is available
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            <?php } ?>
        </div>
        <?php } else { ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($errorMessage ?? 'Invalid parameters provided.'); ?></p>
            </div>
        <?php } ?>
    </div>

</body>
</html>
