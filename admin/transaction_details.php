<?php
session_start();
include 'db_connection.php';

// Check if the necessary data is passed via POST
if (isset($_POST['site']) && isset($_POST['product']) && isset($_POST['userid'])) {
    $site = $_POST['site'];
    $product = $_POST['product'];
    $userid = $_POST['userid'];

    // Fetch user information (to display on the page)
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
    // If no site, product or userid is passed, redirect to dashboard
    header("Location: dashboard.php");
    exit;
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
        <header><h1>Transaction Details for Site: <?php echo htmlspecialchars($site); ?> | Product: <?php echo htmlspecialchars($product); ?></h1></header>

        <!-- User Info -->
        <div class="user-info">
            <h2>User Information</h2>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($userid); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></p>
            <p><strong>Mobile:</strong> <?php echo htmlspecialchars($mobile); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>User Role:</strong> <?php echo htmlspecialchars($userrole); ?></p>
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <h2>Transaction History</h2>
            <?php if (empty($transactions)) { ?>
                <p>No transactions found for this site and product.</p>
            <?php } else { ?>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Expense ID</th>
                            <th>Expense Amount</th>
                            <th>Expense Date</th>
                            <th>Description</th>
                            <th>Uploaded File</th> <!-- New Column for Uploaded File -->
                        </tr>
                        <?php foreach ($transactions as $transaction) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['exid']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['expense_amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['createddate']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['expense_header']); ?></td>
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

        <!-- Back Button -->
        <a href="nav.php" class="back-btn">Back to Dashboard</a>
    </div>

</body>
</html>
