<?php
// PHP Logic for handling AJAX search and fetching usernames
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
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

if (isset($_POST['action']) && $_POST['action'] == 'search_user') {
    $username = $_POST['adduser'] ?? '';
    $mobile = $_POST['mobile'] ?? '';

    // Query to search based on both username and mobile
    $stmt = $conn->prepare("
        SELECT um.user_id, um.first_name, um.last_name, um.mobile, um.email, um.user_role
        FROM user_master um
        WHERE (CONCAT(um.first_name, ' ', um.last_name) = ? OR um.mobile = ?)
    ");
    $stmt->bind_param("ss", $username, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    $transaction_info = [];
    if ($result->num_rows > 0) {
        while ($transaction = $result->fetch_assoc()) {
            $transaction_info[] = $transaction;
        }

        // Store user data in session
        $_SESSION['user_data'] = $transaction_info;
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
    <link rel="stylesheet" href="../Csss/Home.css"></head>
<body>

<!-- New Navbar -->
<nav class="navbar">
    <div class="nav-items">
      <a href="./Home.php">Home</a>
      <a href="./Sites.php">Sites</a>
      <a href="./Add_user.php">Add User</a>
      <a href="./Allowance.php">Add Allowance</a>
    </div>
</nav>

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
        } else {
            let userInfo = `
                <h2>User Information</h2>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>User Role</th>
                    </tr>`;

            data.forEach(user => {
                userInfo += `
                    <tr onclick="redirectToTransaction(${user.user_id}, '${user.first_name}', '${user.last_name}', '${user.mobile}', '${user.email}', '${user.user_role}')">
                        <td>${user.user_id}</td>
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

function redirectToTransaction(user_id, first_name, last_name, mobile, email, user_role) {
    // Store user info in session
    sessionStorage.setItem('user_id', user_id);
    sessionStorage.setItem('first_name', first_name);
    sessionStorage.setItem('last_name', last_name);
    sessionStorage.setItem('mobile', mobile);
    sessionStorage.setItem('email', email);
    sessionStorage.setItem('user_role', user_role);

    // Redirect to transaction history page
    window.location.href = 'transaction_history.php?user_id=' + user_id;
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
