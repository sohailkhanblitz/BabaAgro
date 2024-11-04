<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'search') {
    $mobile = $_POST['mobile'];

    // Query to get user and allowance details, including the user ID
    $stmt = $conn->prepare("SELECT r.userid, r.firstname, r.lastname, r.email, r.userrole, a.site, a.product, a.status
                            FROM registereduser r
                            LEFT JOIN allowancemaster a ON r.userid = a.userid
                            WHERE r.mobile = ? ORDER BY a.status = 'active' DESC");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userid, $firstname, $lastname, $email, $userrole, $site, $product, $status);

    $results = [];
    $sites = [];
    $products = [];
    while ($stmt->fetch()) {
        $results[] = ['userid' => $userid, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'userrole' => $userrole, 'site' => $site, 'product' => $product, 'status' => $status];
        if (!in_array($site, $sites) && $site) $sites[] = $site;
        if (!in_array($product, $products) && $product) $products[] = $product;
    }
    
    // Store the userid in the session
    if (!empty($results)) {
        $_SESSION['userid'] = $results[0]['userid']; // Store the first user's ID
    }

    echo json_encode(['status' => 'found', 'details' => $results, 'sites' => $sites, 'products' => $products]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="../css/expense.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <form id="loginForm">
            <h2>User Information</h2>
            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" required placeholder="Enter your mobile number">
            <button type="button" id="searchButton">Search</button>

            <div id="userDetails" style="display: none;">
                <p id="nameDisplay"></p>
                <p id="emailDisplay"></p>
                <p id="roleDisplay"></p>

                <label for="siteFilter">Filter by Site:</label>
                <select id="siteFilter">
                    <option value="">All Sites</option>
                </select>

                <label for="productFilter">Filter by Product:</label>
                <select id="productFilter">
                    <option value="">All Products</option>
                </select>

                <!-- Table for User Details -->
                <table id="allowanceTable" border="1" style="width:100%; margin-top:20px;">
                    <thead>
                        <tr>
                            <th>Site</th>
                            <th>Product</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </form>
        <a href="admin.php"><button>Admin Login</button></a>
    </div>

    <script>
        // Handle search button click
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

                            // Populate dropdown filters
                            $('#siteFilter').empty().append('<option value="">All Sites</option>');
                            data.sites.forEach(site => {
                                $('#siteFilter').append(`<option value="${site}">${site}</option>`);
                            });
                            $('#productFilter').empty().append('<option value="">All Products</option>');
                            data.products.forEach(product => {
                                $('#productFilter').append(`<option value="${product}">${product}</option>`);
                            });

                            // Populate table with all records
                            updateTable(data.details);

                            $('#userDetails').show();
                        } else {
                            $('#nameDisplay').text('User not found.');
                            $('#emailDisplay').text('');
                            $('#roleDisplay').text('');
                            $('#userDetails').hide();
                        }
                    }
                });
            }
        });

        // Filter table based on dropdown selections
        $('#siteFilter, #productFilter').on('change', function () {
            const siteFilter = $('#siteFilter').val();
            const productFilter = $('#productFilter').val();

            $.ajax({
                url: '', // Re-query server for updated results
                type: 'POST',
                data: { mobile: $('#mobile').val(), action: 'search' },
                success: function (response) {
                    const data = JSON.parse(response);
                    const filteredDetails = data.details.filter(detail => {
                        const matchesSite = siteFilter ? detail.site === siteFilter : true;
                        const matchesProduct = productFilter ? detail.product === productFilter : true;
                        return matchesSite && matchesProduct;
                    });

                    updateTable(filteredDetails);
                }
            });
        });

        // Function to update table
        function updateTable(details) {
            const tableBody = $('#allowanceTable tbody');
            tableBody.empty();

            details.forEach(function (detail) {
                const row = `<tr>
                    <td><a href="expense.php?site=${encodeURIComponent(detail.site)}&product=${encodeURIComponent(detail.product)}" style="color: blue; text-decoration: underline;">${detail.site}</a></td>
                    <td>${detail.product}</td>
                    <td>${detail.status}</td>
                </tr>`;
                tableBody.append(row);
            });
        }
    </script>
</body>
</html>
