<?php
session_start();
include 'db_connection.php';

$showDetails = false; // Variable to control visibility of additional details

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

    if (!empty($results)) {
        $_SESSION['userid'] = $results[0]['userid'];
        $_SESSION['username'] = $results[0]['firstname'] . " " . $results[0]['lastname'];
        $_SESSION['userDetails'] = $results;
        $_SESSION['allData'] = $results; // Store all user data for filtering
        $showDetails = true; // Show additional details since search was successful
    }
}

// Handle site and product filtering
if (isset($_POST['siteFilter']) || isset($_POST['productFilter'])) {
    $selectedSite = $_POST['siteFilter'];
    $selectedProduct = $_POST['productFilter'];
    $filteredData = [];

    if (isset($_SESSION['allData'])) {
        foreach ($_SESSION['allData'] as $detail) {
            $matchesSite = empty($selectedSite) || $detail['site'] == $selectedSite;
            $matchesProduct = empty($selectedProduct) || $detail['product'] == $selectedProduct;

            if ($matchesSite && $matchesProduct) {
                $filteredData[] = $detail;
            }
        }
    }
    $_SESSION['filteredData'] = $filteredData;
    $showDetails = true;
} else {
    $_SESSION['filteredData'] = $_SESSION['allData'] ?? [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="../css/labour.css">
</head>
<body>
    <div class="container">
        <form method="POST" action="">
            <h2>User Information</h2>
            
            <!-- Mobile Number Input and Search Button -->
            <input type="hidden" name="action" value="search">
            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" required placeholder="Enter your mobile number" value="<?php echo isset($mobile) ? $mobile : ''; ?>">
            <button type="submit">Search</button>
            
            <?php if ($showDetails && isset($_SESSION['userDetails']) && !empty($_SESSION['userDetails'])): ?>
            <!-- Additional Details and Filters, Shown After Search -->
            <div id="userDetails">
                <?php
                    $details = $_SESSION['userDetails'][0];
                    echo "<p>Name: " . $details['firstname'] . " " . $details['lastname'] . "</p>";
                    echo "<p>Email: " . $details['email'] . "</p>";
                    echo "<p>Role: " . $details['userrole'] . "</p>";
                ?>

                <!-- Site Filter Dropdown -->
                <label for="siteFilter">Filter by Site:</label>
                <select name="siteFilter" onchange="this.form.submit()">
                    <option value="">All Sites</option>
                    <?php
                        foreach ($sites as $site) {
                            echo "<option value='$site'" . (isset($selectedSite) && $selectedSite == $site ? " selected" : "") . ">$site</option>";
                        }
                    ?>
                </select>

                <!-- Product Filter Dropdown -->
                <label for="productFilter">Filter by Product:</label>
                <select name="productFilter" onchange="this.form.submit()">
                    <option value="">All Products</option>
                    <?php
                        foreach ($products as $product) {
                            echo "<option value='$product'" . (isset($selectedProduct) && $selectedProduct == $product ? " selected" : "") . ">$product</option>";
                        }
                    ?>
                </select>

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
                            $displayData = $_SESSION['filteredData'] ?? [];
                            foreach ($displayData as $detail) {
                                echo "<tr>
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
        </form>
        <a href="admin.php">Admin Login</a>
    </div>
</body>
</html>
