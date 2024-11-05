<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Home</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            padding: 20px;
        }
        .table-container {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .info-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="nav.php">Home</a>
        <a href="add_user.php">Add User</a>
        <a href="allowances.php">Add Allowance</a>
    </div>

    <div class="container">
        <!-- Embedded Dashboard Code -->
        <?php
        session_start();
        include 'db_connection.php'; // Ensure this connects to your database

        $user_info = "";
        $transaction_info = [];

        // Check if the status update form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
            $site = $_POST['site'];
            $product = $_POST['product'];
            $userid = $_POST['userid'];
            $status = $_POST['status'];

            // Update the status in the allowancemaster table
            $stmt = $conn->prepare("UPDATE allowancemaster SET status = ? WHERE site = ? AND product = ? AND userid = ?");
            $stmt->bind_param("sssi", $status, $site, $product, $userid);
            $stmt->execute();
            $stmt->close();
        }

        // Fetch user transactions if searching by username
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['adduser'])) {
            $username = $_POST['adduser'];

            // Prepare and execute the query to fetch user and transaction information with total expense per site and product
            $stmt = $conn->prepare("
                SELECT ru.userid, ru.firstname, ru.lastname, ru.mobile, ru.email, ru.userrole, 
                       al.product, al.site, al.amount, al.date, al.status,
                       (SELECT SUM(e.expense_amount) FROM expense e WHERE e.product = al.product AND e.site = al.site) AS total_expense
                FROM registereduser ru
                LEFT JOIN allowancemaster al ON ru.userid = al.userid
                WHERE ru.firstname = ?
            ");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if user exists and store their transactions
            if ($result->num_rows > 0) {
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

        <div class="dashboard">
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
                if (!empty($transaction_info)) {
                    echo "<p><strong>User ID:</strong> " . $transaction_info[0]['userid'] . "</p>";
                    echo "<p><strong>First Name:</strong> " . $transaction_info[0]['firstname'] . "</p>";
                    echo "<p><strong>Last Name:</strong> " . $transaction_info[0]['lastname'] . "</p>";
                    echo "<p><strong>Mobile:</strong> " . $transaction_info[0]['mobile'] . "</p>";
                    echo "<p><strong>Email:</strong> " . $transaction_info[0]['email'] . "</p>";
                    echo "<p><strong>User Role:</strong> " . $transaction_info[0]['userrole'] . "</p>";
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
                    echo "<div class='table-container'>";
                    echo "<table>";
                    echo "<tr><th>Date</th><th>Product</th><th>Site</th><th>Allowance</th><th>Total Expense</th><th>Status</th><th>Info</th></tr>";
                    foreach ($transaction_info as $transaction) {
                        echo "<tr>";
                        echo "<td>" . $transaction['date'] . "</td>";
                        echo "<td>" . $transaction['product'] . "</td>";
                        echo "<td>" . $transaction['site'] . "</td>";
                        echo "<td>" . $transaction['amount'] . "</td>";
                        echo "<td>" . $transaction['total_expense'] . "</td>";
                        echo "<td>
                                <!-- Form for each dropdown to submit via AJAX -->
                                <form class='status-form' data-userid='" . $transaction['userid'] . "' data-site='" . $transaction['site'] . "' data-product='" . $transaction['product'] . "' style='display:inline;'>
                                    <select name='status' onchange='updateStatus(this)'>
                                        <option value='active' " . ($transaction['status'] == 'active' ? 'selected' : '') . ">Active</option>
                                        <option value='inactive' " . ($transaction['status'] == 'inactive' ? 'selected' : '') . ">Inactive</option>
                                        <option value='done' " . ($transaction['status'] == 'done' ? 'selected' : '') . ">Done</option>
                                    </select>
                                </form>
                              </td>";
                        echo "<td>
                                <form action='transaction_details.php' method='post' style='display:inline;'>
                                    <input type='hidden' name='site' value='" . $transaction['site'] . "'>
                                    <input type='hidden' name='product' value='" . $transaction['product'] . "'>
                                    <input type='hidden' name='userid' value='" . $transaction['userid'] . "'>
                                    <button type='submit' class='info-btn'>Info</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No transactions found for this user.</p>";
                }
                ?>
            </div>
        </div>

        <script>
            function updateStatus(selectElement) {
                const form = selectElement.closest('.status-form');
                const userid = form.dataset.userid;
                const site = form.dataset.site;
                const product = form.dataset.product;
                const status = selectElement.value;

                // Create an AJAX request to update the status
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "", true); // Use the current URL for the request
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log("Status updated successfully!");
                    } else {
                        console.error("Error updating status: " + xhr.status);
                    }
                };

                // Send the request with the necessary parameters
                xhr.send(`update_status=1&userid=${userid}&site=${site}&product=${product}&status=${status}`);
            }
        </script>
    </div>
</body>
</html>
