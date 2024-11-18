<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


session_start();
if (!isset($_SESSION['admin_mobile'])) {
    header("Location: login.php");
    exit();
}
// echo "Welcome to Home Page!". $_SESSION['first_name'];


include 'db_connection.php';

// Fetch distinct usernames for the dropdown
$usernames = [];
$query = "SELECT DISTINCT CONCAT(first_name, ' ', last_name) AS fullname FROM user_master";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row['fullname'];
    }
}

// Handle AJAX requests
if (isset($_POST['action']) && $_POST['action'] == 'search_user') {
    $username = $_POST['adduser'] ?? '';
    $mobile = $_POST['mobile'] ?? '';

    // Build dynamic query based on input
    $query = "SELECT user_id, first_name, last_name, mobile, email, user_role FROM user_master";
    $conditions = [];
    $params = [];
    $types = "";

    if (!empty($username)) {
        $conditions[] = "CONCAT(first_name, ' ', last_name) = ?";
        $params[] = $username;
        $types .= "s";
    }
    if (!empty($mobile)) {
        $conditions[] = "mobile = ?";
        $params[] = $mobile;
        $types .= "s";
    }
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $transaction_info = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transaction_info[] = $row;
        }
    } else {
        $transaction_info = "No user found with the provided details.";
    }
    $stmt->close();

    echo json_encode($transaction_info);
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
    <link rel="stylesheet" href="../Csss/Home.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-items">
      <a href="./Home.php">Home</a>
      <a href="./Sites.php">Sites</a>
      <a href="./Add_user.php">Add User</a>
      <a href="./Allowance.php">Add Allowance</a>
    </div>
</nav>
<?php
echo "Welcome to Home Page!". $_SESSION['logged_in_admin'];
?>

<div class="container">
    <div class="form-container">
        <h2>Search User</h2>
        <label for="adduser">User Name:</label>
        <select id="adduser" name="adduser">
            <option value="">All Users</option>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Retrieve the last search from sessionStorage
    const lastAdduser = sessionStorage.getItem('lastAdduser') || '';
    const lastMobile = sessionStorage.getItem('lastMobile') || '';

    // Restore values in the form
    document.getElementById('adduser').value = lastAdduser;
    document.getElementById('mobile').value = lastMobile;

    // Execute the last search
    executeSearch(lastAdduser, lastMobile);
});

function searchUser() {
    const adduser = document.getElementById('adduser').value;
    const mobile = document.getElementById('mobile').value;

    // Save the search values to sessionStorage
    sessionStorage.setItem('lastAdduser', adduser);
    sessionStorage.setItem('lastMobile', mobile);

    executeSearch(adduser, mobile);

    // Clear the search fields after the search
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
        } else {
            let userInfo = `
                <h2>User Information</h2>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>User Role</th>
                    </tr>`;

            data.forEach(user => {
                userInfo += `
                    <tr style="cursor: pointer;" onclick='redirectToTransaction("${user.first_name}", "${user.last_name}", "${user.mobile}", "${user.user_id}")'>
                        <td>${user.first_name}</td>
                        <td>${user.last_name}</td>
                        <td>${user.mobile}</td>
                        <td>${user.email}</td>
                        <td>${user.user_role}</td>
                    </tr>`;
            });

            userInfo += '</table>';
            document.getElementById('user-info').innerHTML = userInfo;
        }
    })
    .catch(error => console.error('Error:', error));
}

function redirectToTransaction(firstName, lastName, mobile, userId) {
    const queryParams = new URLSearchParams({
        first_name: firstName,
        last_name: lastName,
        mobile: mobile,
        user_id: userId
    });

    window.location.href = `transaction.php?${queryParams.toString()}`;
}
</script>


</body>
</html>



<!-- perfectly working -->
<?php

// session_destroy();
?>