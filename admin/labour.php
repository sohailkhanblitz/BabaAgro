<?php
session_start();
include 'db_connection.php';

$showDetails = false; // Variable to control visibility of additional details

// Handle site and product filtering
$sites = [];
$products = [];
$results = [];

if (isset($_GET['mobile'])) {
    $mobile = $_GET['mobile'];

    // Query to get user and allowance details, including the user ID
    $stmt = $conn->prepare("SELECT r.userid, r.firstname, r.lastname, r.email, r.userrole, a.site, a.product, a.status
                            FROM registereduser r
                            LEFT JOIN allowancemaster a ON r.userid = a.userid
                            WHERE r.mobile = ? ORDER BY a.status = 'Active' DESC");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userid, $firstname, $lastname, $email, $userrole, $site, $product, $status);

    while ($stmt->fetch()) {
        $results[] = ['userid' => $userid, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'userrole' => $userrole, 'site' => $site, 'product' => $product, 'status' => $status];
        if (!in_array($site, $sites) && $site) $sites[] = $site;
        if (!in_array($product, $products) && $product) $products[] = $product;
    }

    if (!empty($results)) {
        $_SESSION['userid'] = $results[0]['userid'];
        $_SESSION['username'] = $results[0]['firstname'] . " " . $results[0]['lastname'];
        $_SESSION['userDetails'] = $results;
        $_SESSION['allData'] = $results; // Store all user data for filtering
        $showDetails = true; // Show additional details since search was successful
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="../css/labour.css">
    <script>
        function searchUser() {
            const mobile = document.getElementById('mobile').value;
            window.location.href = "?mobile=" + mobile;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>User Login</h2>

        <!-- Mobile Number Input and Search Button -->
        <label for="mobile">Mobile Number:</label>
        <input type="text" id="mobile" required placeholder="Enter your mobile number">
        <button type="submit" onclick="searchUser()">Search</button>

        <?php if ($showDetails && isset($_SESSION['userDetails']) && !empty($_SESSION['userDetails'])): ?>
        <!-- Additional Details -->
        <div id="userDetails">
            <?php
            $details = $_SESSION['userDetails'][0];
            ?>

            <!-- Site Filter Dropdown -->
            <label for="siteFilter">Filter by Site:</label>
            <select id="siteFilter" onchange="filterData()">
                <option value="">All Sites</option>
                <?php
                    foreach ($sites as $site) {
                        echo "<option value='$site'>$site</option>";
                    }
                ?>
            </select>

            <!-- Product Filter Dropdown -->
            <label for="productFilter">Filter by Product:</label>
            <select id="productFilter" onchange="filterData()">
                <option value="">All Products</option>
                <?php
                    foreach ($products as $product) {
                        echo "<option value='$product'>$product</option>";
                    }
                ?>
            </select>

            <table border="1" cellpadding="10">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
                <tr>
                    <td><?php echo htmlspecialchars($details['firstname'] . " " . $details['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($details['email']); ?></td>
                    <td><?php echo htmlspecialchars($details['userrole']); ?></td>
                </tr>
            </table>

            <!-- Allowance Table -->
            <table id="allowanceTable" border="1" style="width:100%; margin-top:20px;">
                <thead>
                    <tr>
                        <th>Site</th>
                        <th>Product</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($results as $detail) {
                            echo "<tr class='dataRow' data-site='" . $detail['site'] . "' data-product='" . $detail['product'] . "'>
                                    <td><a href='expense.php?site=" . urlencode($detail['site']) . "&product=" . urlencode($detail['product']) . "'>" . $detail['site'] . "</a></td>
                                    <td>" . $detail['product'] . "</td>
                                    <td>" . $detail['status'] . "</td>
                                  </tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function filterData() {
            const siteFilter = document.getElementById('siteFilter').value;
            const productFilter = document.getElementById('productFilter').value;
            const rows = document.querySelectorAll('#allowanceTable .dataRow');

            rows.forEach(row => {
                const site = row.getAttribute('data-site');
                const product = row.getAttribute('data-product');
                let isVisible = true;

                if (siteFilter && site !== siteFilter) {
                    isVisible = false;
                }

                if (productFilter && product !== productFilter) {
                    isVisible = false;
                }

                row.style.display = isVisible ? '' : 'none';
            });
        }
    </script>
</body>
</html>
