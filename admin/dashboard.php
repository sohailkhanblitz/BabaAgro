<?php
include 'db_connection.php';

$user_info = "";
$transaction_info = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['adduser'])) {
    $username = $_POST['adduser'];

    // Prepare and execute the query to fetch user and transaction information
    $stmt = $conn->prepare("
        SELECT ru.userid, ru.firstname, ru.lastname, ru.mobile, ru.email, ru.userrole, 
               al.product, al.site, al.amount, al.date
        FROM registereduser ru
        LEFT JOIN allowancemaster al ON ru.userid = al.userid
        WHERE ru.firstname = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch user information only once
        // $user_info = $result->fetch_assoc();

        // Loop through the rest of the rows to get transaction information
        while ($transaction = $result->fetch_assoc()) {
            $transaction_info[] = $transaction;
        }
    } else {
        $user_info = "No user found with name '$username'.";
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
    <title>Expense Management Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Expense Manager</h2>
            <nav>
                <ul>
                    <li><a href="#">Dashboard</a></li>
                    <li><a href="add_user.php">Add User</a></li>
                    <li><a href="add_allowonss.php">Add Allowonss</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <header>
                <h1>Dashboard</h1>
            </header>

            <!-- Search User Form -->
            <div class="form-container">
                <h2>Search User</h2>
                <form action="" method="post">
                    <label for="adduser">Search User:</label>
                    <input type="text" id="adduser" name="adduser" required><br><br>

                    <button type="submit">Search User</button>
                </form>
            </div>

            <!-- Display User Information -->
            <div class="user-info">
                <h2>User Information</h2>
                <?php
                // if (is_array($user_info)) {
                //     echo "<p><strong>User ID:</strong> " . $user_info['userid'] . "</p>";
                //     echo "<p><strong>First Name:</strong> " . $user_info['firstname'] . "</p>";
                //     echo "<p><strong>Last Name:</strong> " . $user_info['lastname'] . "</p>";
                //     echo "<p><strong>Mobile:</strong> " . $user_info['mobile'] . "</p>";
                //     echo "<p><strong>Email:</strong> " . $user_info['email'] . "</p>";
                //     echo "<p><strong>User Role:</strong> " . $user_info['userrole'] . "</p>";
                //     echo "<p><strong>User product:</strong> " . $user_info['product'] . "</p>";
                // } else {
                //     echo "<p>" . $user_info . "</p>";
                // }
                if ($result->num_rows > 0) {
                    echo "<p><strong>User ID:</strong> " . $transaction_info[0]['userid'] . "</p>";
                    echo "<p><strong>First Name:</strong> " . $transaction_info[0]['firstname'] . "</p>";
                    echo "<p><strong>Last Name:</strong> " . $transaction_info[0]['lastname'] . "</p>";
                    echo "<p><strong>Mobile:</strong> " . $transaction_info[0]['mobile'] . "</p>";
                    echo "<p><strong>User Role:</strong> " . $transaction_info[0]['userrole'] . "</p>";
                    // echo "<p><strong>First Name:</strong> " . $user_info['firstname'] . "</p>";
                    // echo "<p><strong>Last Name:</strong> " . $user_info['lastname'] . "</p>";
                    // echo "<p><strong>Mobile:</strong> " . $user_info['mobile'] . "</p>";
                    // echo "<p><strong>Email:</strong> " . $user_info['email'] . "</p>";
                    // echo "<p><strong>User Role:</strong> " . $user_info['userrole'] . "</p>";
                    // echo "<p><strong>User product:</strong> " . $user_info['product'] . "</p>";
                } else {
                    echo "<p>" . $user_info . "</p>";
                }
                ?>
            </div>

            <!-- Display Transaction Information -->
            <div class="transaction-info">
                <h2>Transaction History</h2>
                <?php
                if (!empty($transaction_info)) {
                    echo "<table>";
                    echo "<tr><th>Date</th><th>Product</th><th>Site</th><th>Amount</th></tr>";
                    foreach ($transaction_info as $transaction) {
                        echo "<tr>";
                        echo "<td>" . $transaction['date'] . "</td>";
                        echo "<td>" . $transaction['product'] . "</td>";
                        echo "<td>" . $transaction['site'] . "</td>";
                        echo "<td>" . $transaction['amount'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No transactions found for this user.</p>";
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
