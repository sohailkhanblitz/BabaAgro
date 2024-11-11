<?php
// Disable caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Home</title>
    <link rel="stylesheet" href="../css/nav.css">
   
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
        <?php
        session_start();
        include 'db_connection.php';

        $user_info = "";
        $transaction_info = [];

        // Fetch all users for search dropdown
        $userOptions = "";
        $usernames = [];
        $sql = "SELECT firstname, lastname, mobile FROM registereduser";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $usernames[] = $row['firstname'] . " " . $row['lastname'];
        }

        // Handle status update (existing functionality)
// Handle status update (existing functionality)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $site = $_POST['site'];
    $product = $_POST['product'];
    $userid = $_POST['userid'];
    $status = $_POST['status'];

    $response = [];  // Initialize response array

    if ($status === 'active') {
        // Check if there is already an active site for this user
        $activeCheck = $conn->prepare("SELECT site FROM allowancemaster WHERE status = 'active' AND userid = ?");
        $activeCheck->bind_param("i", $userid);
        $activeCheck->execute();
        $activeCheck->bind_result($activeSite);
        $activeCheck->fetch();
        $activeCheck->close();

        if ($activeSite) {
            $response['success'] = false;
            $response['message'] = 'Please deactivate the site "' . $activeSite . '" first.';
        } else {
            // Update status if no active site found
            $stmt = $conn->prepare("UPDATE allowancemaster SET status = ? WHERE site = ? AND product = ? AND userid = ?");
            $stmt->bind_param("sssi", $status, $site, $product, $userid);
            $stmt->execute();
            $stmt->close();
            $response['success'] = true;
            $response['message'] = 'Status updated to active successfully.';
        }
    } else {
        // Update status to inactive or another value
        $stmt = $conn->prepare("UPDATE allowancemaster SET status = ? WHERE site = ? AND product = ? AND userid = ?");
        $stmt->bind_param("sssi", $status, $site, $product, $userid);
        $stmt->execute();
        $stmt->close();
        $response['success'] = true;
        $response['message'] = 'Status updated successfully.';
    }

    // Always return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}



        // Fetch transactions based on search input
if ($_SERVER["REQUEST_METHOD"] == "POST" && (!empty($_POST['adduser']) || !empty($_POST['mobile']))) {
    $username = $_POST['adduser'];
    $mobile = $_POST['mobile'];

    $stmt = $conn->prepare("
        SELECT ru.userid, ru.firstname, ru.lastname, ru.mobile, ru.email, ru.userrole, 
               al.product, al.site, al.amount, al.date, al.status,
               (SELECT COALESCE(SUM(e.expense_amount), 0) 
                FROM expense e 
                WHERE e.product = al.product AND e.site = al.site ) AS total_expense
        FROM registereduser ru
        LEFT JOIN allowancemaster al ON ru.userid = al.userid
        WHERE (CONCAT(ru.firstname, ' ', ru.lastname) = ? OR ru.mobile = ?)
    ");
    $stmt->bind_param("ss", $username, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($transaction = $result->fetch_assoc()) {
            $transaction_info[] = $transaction;
        }

        // Sort transactions: "active" status first
        usort($transaction_info, function ($a, $b) {
            return ($a['status'] === 'active' ? -1 : 1);
        });
    } else {
        $user_info = "No user found with the provided details.";
    }
    $stmt->close();
}

        $conn->close();
        ?>

        <div class="dashboard">
            <header><h1>Dashboard</h1></header>

            <!-- Search User Form with Dropdown Suggestions and Mobile Number Search -->
            <div class="form-container">
                <h2>Search User</h2>
                <form action="" method="post">
                    <label for="adduser">User Name:</label>
                    <input type="text" list="usernames" id="adduser" name="adduser" placeholder="Enter User Name">
                    <datalist id="usernames">
                        <?php foreach ($usernames as $name) echo "<option value='$name'>"; ?>
                    </datalist>
                    <br><br>
                    <label for="mobile">Mobile:</label>
                    <input type="text" id="mobile" name="mobile" pattern="\d{10}" placeholder="Enter mobile number">
                    <br><br>
                    <button type="submit">Search User</button>
                </form>
            </div>

            <!-- Display User Information -->
            <div class="table-container">
                <h2>User Information</h2>
                <?php
if (!empty($transaction_info)) {
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr>";
    echo "<th>User ID</th>";
    echo "<th>First Name</th>";
    echo "<th>Last Name</th>";
    echo "<th>Mobile</th>";
    echo "<th>Email</th>";
    echo "<th>User Role</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($transaction_info[0]['userid']) . "</td>";
    echo "<td>" . htmlspecialchars($transaction_info[0]['firstname']) . "</td>";
    echo "<td>" . htmlspecialchars($transaction_info[0]['lastname']) . "</td>";
    echo "<td>" . htmlspecialchars($transaction_info[0]['mobile']) . "</td>";
    echo "<td>" . htmlspecialchars($transaction_info[0]['email']) . "</td>";
    echo "<td>" . htmlspecialchars($transaction_info[0]['userrole']) . "</td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "<p>" . htmlspecialchars($user_info) . "</p>";
}
?>

            </div>

            <!-- Display Transaction Information -->
            <div class="transaction-info">
                <h2>Transaction History</h2>
                <div id="status-message"></div> <!-- Message display area -->
                <?php
                if (!empty($transaction_info[0]['product'])) {
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
                                <form class='status-form' data-userid='" . $transaction['userid'] . "' data-site='" . $transaction['site'] . "' data-product='" . $transaction['product'] . "' style='display:inline;'>
                                    <select name='status' onchange='updateStatus(this)'>
                                        <option value='active' " . ($transaction['status'] == 'active' ? 'selected' : '') . ">Active</option>
                                        <option value='inactive' " . ($transaction['status'] == 'inactive' ? 'selected' : '') . ">Inactive</option>
                                        <option value='Pushed For Approval' " . ($transaction['status'] == 'Pushed For Approval' ? 'selected' : '') . ">Pushed For Approval</option>
                                    </select>
                                </form>
                              </td>";
                        echo "<td>
                                <form action='transaction_details.php' method='post' style='display:inline;'>
                                    <input type='hidden' name='site' value='" . $transaction['site'] . "'>
                                    <input type='hidden' name='product' value='" . $transaction['product'] . "'>
                                    <input type='hidden' name='userid' value='" . $transaction['userid'] . "'>
                                    <button type='submit' class='info-btn'>Details</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                }
                else{
                    echo "No transaction found";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
function updateStatus(selectElement) {
    const form = selectElement.closest('.status-form');
    const status = selectElement.value;
    const site = form.dataset.site;
    const product = form.dataset.product;
    const userid = form.dataset.userid;

    const data = new FormData();
    data.append('update_status', true);
    data.append('site', site);
    data.append('product', product);
    data.append('userid', userid);
    data.append('status', status);

    fetch('', {
        method: 'POST',
        body: data
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(result => {
        const statusMessage = document.getElementById('status-message');
        if (result.success) {
            statusMessage.innerText = result.message;
            statusMessage.style.color = 'green';  // Success message color
        } else {
            statusMessage.innerText = result.message;
            statusMessage.style.color = 'red';  // Error message color
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const statusMessage = document.getElementById('status-message');
        statusMessage.innerText = 'An error occurred while updating the status.';
        statusMessage.style.color = 'red';
    });
}


    </script>

</body>
</html>
