<?php
// Include the database connection file
include 'db_connection.php';

// Fetch query string parameters
$site_id = isset($_GET['site_id']) ? intval($_GET['site_id']) : 0;
$sp_id = isset($_GET['sp_id']) ? intval($_GET['sp_id']) : 0;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Fetch site name
$site_name = "Unknown Site"; // Default value
$product_name = "Unknown Product"; // Default value

// Query to fetch site name
$site_query = "SELECT site_name FROM sites WHERE site_id = $site_id";
$site_result = $conn->query($site_query);
if ($site_result && $site_result->num_rows > 0) {
    $site_row = $site_result->fetch_assoc();
    $site_name = $site_row['site_name'];
}

// Query to fetch product name
$product_query = "SELECT product_name FROM site_product WHERE sp_id = $sp_id";
$product_result = $conn->query($product_query);
if ($product_result && $product_result->num_rows > 0) {
    $product_row = $product_result->fetch_assoc();
    $product_name = $product_row['product_name'];
}

// Set default view to "allowance"
$view = isset($_GET['view']) ? $_GET['view'] : 'allowance';

// Fetch allowance or expense records based on view
if ($view === 'allowance') {
    $query = "SELECT al_id, al_amount, status, date, created_date FROM allowance_master 
              WHERE sp_id = $sp_id AND user_id = $user_id";
} else {
    $query = "SELECT ex_id, ex_header, ex_amount, date, file_path, created_date FROM expense_master 
              WHERE sp_id = $sp_id AND user_id = $user_id";
}

$result = $conn->query($query);
$records = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .toggle-container {
            margin-bottom: 20px;
        }
        .toggle-container a {
            padding: 10px 20px;
            text-decoration: none;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            color: #000;
            background-color: #f1f1f1;
        }
        .toggle-container a.active {
            background-color: #007bff;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1><?= htmlspecialchars($site_name) ?></h1>
        <h2>Product: <?= htmlspecialchars($product_name) ?></h2>
    </div>

    <!-- Toggle Buttons -->
    <div class="toggle-container">
        <a href="history.php?site_id=<?= $site_id ?>&sp_id=<?= $sp_id ?>&user_id=<?= $user_id ?>&view=allowance"
           class="<?= $view === 'allowance' ? 'active' : '' ?>">Allowance History</a>
        <a href="history.php?site_id=<?= $site_id ?>&sp_id=<?= $sp_id ?>&user_id=<?= $user_id ?>&view=expense"
           class="<?= $view === 'expense' ? 'active' : '' ?>">Expense History</a>
    </div>

    <h2><?= ucfirst($view) ?> Records</h2>

    <!-- Records Table -->
    <?php if (!empty($records)) : ?>
        <table>
            <thead>
                <tr>
                    <?php if ($view === 'allowance') : ?>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Created Date</th>
                    <?php else : ?>
                        <th>Expense ID</th>
                        <th>Header</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>File Path</th>
                        <th>Created Date</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record) : ?>
                    <tr>
                        <?php if ($view === 'allowance') : ?>
                            <td><?= $record['al_amount'] ?></td>
                            <td><?= $record['date'] ?></td>
                            <td><?= $record['created_date'] ?></td>
                        <?php else : ?>
                            <td><?= $record['ex_id'] ?></td>
                            <td><?= $record['ex_header'] ?></td>
                            <td><?= $record['ex_amount'] ?></td>
                            <td><?= $record['date'] ?></td>
                            <td><?= $record['file_path'] ?></td>
                            <td><?= $record['created_date'] ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No records found.</p>
    <?php endif; ?>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
