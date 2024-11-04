<?php
session_start();
include 'db_connection.php'; // Ensure db_connect.php connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'login') {
        // Login logic
        $mobile = $_POST['mobile'];

        // Check if the mobile number exists
        $stmt = $conn->prepare("SELECT mobile FROM registereduser WHERE mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['mobile'] = $mobile; // Store the mobile in session
            header("Location: expense.php");
            exit();
        } else {
            $error = "Mobile number not found. Please try again.";
        }

        $stmt->close();
    } elseif ($_POST['action'] == 'search') {
        // Search logic for AJAX request
        $mobile = $_POST['mobile'];

        // Prepare the SQL statement to retrieve user details and allowance data
        $stmt = $conn->prepare("SELECT r.firstname, r.lastname, r.email, r.userrole, a.site, a.product
                                FROM registereduser r
                                LEFT JOIN allowancemaster a ON r.userid = a.userid
                                WHERE r.mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($firstname, $lastname, $email, $userrole, $site, $product);

        $results = [];
        while ($stmt->fetch()) {
            // Populate results array with user and allowance details
            $results[] = ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'userrole' => $userrole, 'site' => $site, 'product' => $product];
        }

        if (count($results) > 0) {
            // Send user details and all associated site-product pairs as JSON
            echo json_encode(['status' => 'found', 'details' => $results]);
        } else {
            echo json_encode(['status' => 'not_found']);
        }

        $stmt->close();
        exit(); // Exit to avoid further output
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/expense.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <form action="" method="post" id="loginForm">
            <h2>User Login</h2>
            <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" required placeholder="Enter your mobile number">
            <button type="button" id="searchButton">Search</button>
            <button type="submit" name="action" value="login">Login</button>
            <a href="admin.php">Admin Login</a>

            <div id="userDetails" style="display: none;">
                <p id="nameDisplay"></p>
                <p id="emailDisplay"></p>
                <p id="roleDisplay"></p>
                <!-- <div>
                    <label for="siteDropdown">Select Site:</label>
                    <select id="siteDropdown" name="site">
                        <option selected disabled>Select Site</option>
                    </select>
                </div>
                <div>
                    <label for="productDropdown">Select Product:</label>
                    <select id="productDropdown" name="product">
                        <option selected disabled>Select Product</option>
                    </select>
                </div> -->
            </div>
        </form>
    </div>

    <script>
        $('#searchButton').on('click', function () {
            const mobile = $('#mobile').val();
            if (mobile) {
                $.ajax({
                    url: '', // Current PHP file
                    type: 'POST',
                    data: { mobile: mobile, action: 'search' },
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.status === 'found') {
                            const details = data.details[0];
                            $('#nameDisplay').text('Name: ' + details.firstname + ' ' + details.lastname);
                            $('#emailDisplay').text('Email: ' + details.email);
                            $('#roleDisplay').text('Role: ' + details.userrole);

                            // Clear and populate dropdowns
                            $('#siteDropdown').empty().append('<option selected disabled>Select Site</option>');
                            $('#productDropdown').empty().append('<option selected disabled>Select Product</option>');

                            data.details.forEach(function(item) {
                                if (item.site) {
                                    $('#siteDropdown').append(`<option value="${item.site}">${item.site}</option>`);
                                }
                                if (item.product) {
                                    $('#productDropdown').append(`<option value="${item.product}">${item.product}</option>`);
                                }
                            });

                            $('#userDetails').show();
                        } else {
                            $('#nameDisplay').text('User not found.');
                            $('#emailDisplay').text('');
                            $('#roleDisplay').text('');
                            $('#siteDropdown').empty().append('<option selected disabled>Select Site</option>');
                            $('#productDropdown').empty().append('<option selected disabled>Select Product</option>');
                            $('#userDetails').show();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
