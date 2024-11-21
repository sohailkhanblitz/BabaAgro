<?php
// Include the database connection file
include 'db_connection.php';
session_start();
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $sp_id = intval($_POST['sp_id']);
    $user_id = intval($_POST['user_id']);
    $ex_header = $conn->real_escape_string($_POST['ex_header']);
    $ex_amount = floatval($_POST['ex_amount']);
    $date = $conn->real_escape_string($_POST['date']);
    $file_path = null;

    // Handle file upload if provided
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file_path']['name']);
        $target_dir = "uploads/";
        $file_path = $target_dir . $file_name;
        if (!move_uploaded_file($_FILES['file_path']['tmp_name'], $file_path)) {
            echo "Error uploading file.";
            exit;
        }
    }

    // Insert data into expense_master table
    $created_date = date('Y-m-d H:i:s');
    $query = "INSERT INTO expense_master (sp_id, user_id, ex_header, ex_amount, date, file_path, created_date) 
              VALUES ($sp_id, $user_id, '$ex_header', $ex_amount, '$date', '$file_path', '$created_date')";

    if ($conn->query($query)) {
        // Redirect to the same page to avoid resubmission issues
        header('Location: ' . $_SERVER['PHP_SELF'] . '?site_id=' . $_GET['site_id'] . '&sp_id=' . $sp_id . '&user_id=' . $user_id . '&view=expense');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

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
    <link rel="stylesheet" href="../Csss/history.css">
</head>
<body>
    <div class="container">
    <div class="header">
        <h1>Site: <?= htmlspecialchars($site_name) ?></h1>
        <h2>Product: <?= htmlspecialchars($product_name) ?></h2>
    </div>

   

    <h2><?= ucfirst($view) ?> Records</h2>
    <div class="toggle">
    <div class="toggle-container " >
        <a href="history.php?site_id=<?= $site_id ?>&sp_id=<?= $sp_id ?>&user_id=<?= $user_id ?>&view=allowance"
           class="<?= $view === 'allowance' ? 'active' : '' ?>">Allowance</a>
        <a href="history.php?site_id=<?= $site_id ?>&sp_id=<?= $sp_id ?>&user_id=<?= $user_id ?>&view=expense"
           class="<?= $view === 'expense' ? 'active' : '' ?>">Expense</a>
 
</div>

    <?php if ($view === 'expense' && $_SESSION['user_type'] !== 'admin') : ?>
        <div class="expense">
            <button onclick="openModal()">+</button>
        </div>
<?php endif; ?>
</div>


    <?php if (!empty($records)) : ?>
        <table>
            <thead>
                <tr>
                    <?php if ($view === 'allowance') : ?>
                        <th>Amount</th>
                        <th>Created Date</th>
                    <?php else : ?>
                        <!-- <th>Expense ID</th> -->
                        <th>Header</th>
                        <th>Amount</th>
                        <!-- <th>Date</th> -->
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
                            <td><?= $record['created_date'] ?></td>
                        <?php else : ?>
                            <!-- <td><?= $record['ex_id'] ?></td> -->
                            <td><?= $record['ex_header'] ?></td>
                            <td><?= $record['ex_amount'] ?></td>
                            <!-- <td><?= $record['date'] ?></td>  -->
                            <td><a href="<?= $record['file_path'] ?>" target="_blank">View File</a></td>
                            <td><?= $record['created_date'] ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No records found.</p>
    <?php endif; ?>
    <div id="addExpenseModal">
    <div class="modal-content" >
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-header">Add Expense</div>
            <input type="hidden" name="sp_id" value="<?= $sp_id ?>">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <label for="ex_header">Header</label>
            <input type="text" id="ex_header" name="ex_header" required><br><br>
            <label for="ex_amount">Amount</label>
            <input type="number" id="ex_amount" name="ex_amount" required><br><br>
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required><br><br>
            <label for="file_path">Upload File</label>
            <input type="file" id="file_path" name="file_path"><br><br>
            <div class="modal-footer">
                 <button type="submit" class="save-btn">Save</button>
                <button type="button" class="close-btn" onclick="closeModal()">Close</button>
              
            </div>
        </form>
    </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('addExpenseModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('addExpenseModal').classList.remove('active');
        }
    </script>
    </div>
</body>
</html>

<?php
$conn->close();
?>


<!-- working  -->