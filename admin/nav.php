<?php
// PHP Logic for handling AJAX search and fetching usernames
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include 'db_connection.php';

// Fetch usernames for the dropdown
$usernames = [];
$query = "SELECT CONCAT(firstname, ' ', lastname) AS fullname FROM registereduser";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row['fullname'];
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'search_user') {
    $username = $_POST['adduser'] ?? '';
    $mobile = $_POST['mobile'] ?? '';

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

    $transaction_info = [];
    if ($result->num_rows > 0) {
        while ($transaction = $result->fetch_assoc()) {
            $transaction_info[] = $transaction;
        }

        $_SESSION['user_data'] = $transaction_info[0];
        $_SESSION['transaction_data'] = $transaction_info;
    } else {
        $transaction_info = "No user found with the provided details.";
    }
    $stmt->close();

    echo json_encode($transaction_info);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $status = $_POST['status'] ?? '';
    $product = $_POST['product'] ?? '';
    $site = $_POST['site'] ?? '';
    $userid = $_POST['userid'] ?? '';

    if ($status && $product && $site && $userid) {
        // Update the status in the allowancemaster table
        $update_stmt = $conn->prepare("UPDATE allowancemaster SET status = ? WHERE product = ? AND site = ? AND userid = ?");
        $update_stmt->bind_param("ssss", $status, $product, $site, $userid);
        $update_stmt->execute();
        $update_stmt->close();
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/nav.css">
</head>
<body>

<div class="navbar">
    <div><a href="nav.php">Home</a></div>
    <div><a href="add_user.php">Add User</a></div>
    <div><a href="allowances.php">Add Allowance</a></div>
</div>

<div class="container">
    <div class="form-container">
        <h2>Search User</h2>
        <label for="adduser">User Name:</label>
        <select id="adduser" name="adduser">
            <option value="">Select User</option>
            <?php foreach ($usernames as $name): ?>
                <option value="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($name); ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" maxlength="10" pattern="\d{10}" placeholder="Enter Mobile Number">
        <br><br>
        <button class="info-btn" onclick="searchUser()">Search User</button>
    </div>

    <div class="user-info" id="user-info"></div>
    <div class="transaction-info" id="transaction-info"></div>
</div>

<script>
function searchUser() {
    const adduser = document.getElementById('adduser').value;
    const mobile = document.getElementById('mobile').value;

    // Execute the search with current values
    executeSearch(adduser, mobile);

    // Store the search input in session storage
    sessionStorage.setItem('adduser', adduser);
    sessionStorage.setItem('mobile', mobile);

    // Clear the input fields after storing in session
    document.getElementById('adduser').value = '';
    document.getElementById('mobile').value = '';
}

function executeSearch(adduser, mobile) {
    fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'search_user',
            adduser: adduser,
            mobile: mobile
        })
    })
    .then(response => response.json())
    .then(data => {
        if (typeof data === 'string') {
            document.getElementById('user-info').innerHTML = `<p>${data}</p>`;
            document.getElementById('transaction-info').innerHTML = '';
        } else {
            let userInfo = `
                <h2 >User Information</h2>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>User Role</th>
                    </tr>
                    <tr>
                        <td>${data[0].userid}</td>
                        <td>${data[0].firstname}</td>
                        <td>${data[0].lastname}</td>
                        <td>${data[0].mobile}</td>
                        <td>${data[0].email}</td>
                        <td>${data[0].userrole}</td>
                    </tr>
                </table>`;
            document.getElementById('user-info').innerHTML = userInfo;

            let transactionInfo = `
                <h2>Transaction History</h2>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Site</th>
                        <th>Allowance</th>
                        <th>Total Expense</th>
                        <th>Status</th>
                        <th>Change Status</th>
                        <th>Info</th>
                    </tr>`;
            data.forEach(transaction => {
                transactionInfo += `
                    <tr>
                        <td>${transaction.date}</td>
                        <td>${transaction.product}</td>
                        <td>${transaction.site}</td>
                        <td>${transaction.amount}</td>
                        <td>${transaction.total_expense}</td>
                        <td>${transaction.status}</td>
                        <td>
                            <select class="status-select" data-product="${transaction.product}" data-site="${transaction.site}" data-userid="${transaction.userid}">
                                <option value="Active" ${transaction.status === 'Active' ? 'selected' : ''}>Active</option>
                                <option value="Settled" ${transaction.status === 'Settled' ? 'selected' : ''}>Settled</option>
                            </select>
                        </td>
                        <td><a href="transaction_details.php?product=${encodeURIComponent(transaction.product)}&site=${encodeURIComponent(transaction.site)}&userid=${encodeURIComponent(transaction.userid)}">Details</a></td>
                    </tr>`;
            });
            transactionInfo += '</table>';
            document.getElementById('transaction-info').innerHTML = transactionInfo;

            // Add event listeners for status change
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    updateStatus(select);
                });
            });
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateStatus(select) {
    const status = select.value;
    const product = select.getAttribute('data-product');
    const site = select.getAttribute('data-site');
    const userid = select.getAttribute('data-userid');

    fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'update_status',
            status: status,
            product: product,
            site: site,
            userid: userid
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully!');
        } else {
            alert('Failed to update status!');
        }
    })
    .catch(error => console.error('Error:', error));
}

window.onload = function() {
    const storedUsername = sessionStorage.getItem('adduser');
    const storedMobile = sessionStorage.getItem('mobile');

    if (storedUsername || storedMobile) {
        executeSearch(storedUsername, storedMobile);
    }
};
</script>

</body>
</html>
