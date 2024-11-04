<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connection.php'; // Include your database connection

// Handle the form submission for adding an expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_expense') {
    $expense_header = $_POST['expense_header'];
    $expense_amount = $_POST['expense_amount'];
    $expense_date = $_POST['expense_date'];
    $site = $_POST['site'];
    $product = $_POST['product'];
    $user_id = $_SESSION['userid'];
    $created_date = date('Y-m-d H:i:s');

    // Handle file upload if present
    $file_path = null;
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // Make sure this directory exists and is writable
        $file_name = basename($_FILES['file_upload']['name']);
        $file_tmp = $_FILES['file_upload']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        // Validate file type (JPG or PDF)
        if (in_array($file_ext, ['jpg', 'jpeg', 'pdf'])) {
            $file_path = $upload_dir . uniqid('', true) . '.' . $file_ext; // Unique file name
            if (!move_uploaded_file($file_tmp, $file_path)) {
                echo "Failed to move uploaded file.";
                exit;
            }
        } else {
            echo "Invalid file type. Only JPG and PDF files are allowed.";
            exit;
        }
    }

    // Prepare the insert statement
    $stmt = $conn->prepare("
        INSERT INTO expense (expense_header, site, product, expense_amount, createdby, createddate, date, file_path) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssss", $expense_header, $site, $product, $expense_amount, $user_id, $created_date, $expense_date, $file_path);

    if ($stmt->execute()) {
        // Redirect to the same page to avoid resubmission on refresh
        header("Location: expense.php?site=$site&product=$product");
        exit; // Ensure no further code is executed after the redirect
    } else {
        echo "Failed to add expense: " . $stmt->error; // Output detailed error
        exit;
    }

    $stmt->close();
}

// Get the selected site and product from the URL
$site = isset($_GET['site']) ? $_GET['site'] : '';
$product = isset($_GET['product']) ? $_GET['product'] : '';

// Fetch all expenses for the selected site and product
$expenses = [];
if ($site && $product) {
    $stmt = $conn->prepare("
        SELECT date, expense_header, expense_amount, file_path 
        FROM expense 
        WHERE site = ? AND product = ?
        ORDER BY date DESC
    ");
    $stmt->bind_param("ss", $site, $product);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Details</title>
    <link rel="stylesheet" href="../css/expense.css">
    <style>
        /* Basic styles for layout */
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        header { display: flex; justify-content: space-between; align-items: center; }
        .add-expense-button { padding: 10px 20px; background-color: #4CAF50; color: white; border-radius: 5px; cursor: pointer; }
        .add-expense-button:hover { background-color: #45a049; }
        .expense-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .expense-table th, .expense-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .expense-table th { background-color: #f2f2f2; }
        #expenseModal { display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close:hover, .close:focus { color: black; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Site: <?php echo htmlspecialchars($site); ?> Product: <?php echo htmlspecialchars($product); ?></h1>
            <button id="addExpenseButton" class="add-expense-button">Add New Expense</button>
        </header>

        <?php if (!empty($expenses)) : ?>
            <table class="expense-table">
                <thead>
                    <tr><th>Date</th><th>Description</th><th>Amount</th><th>File</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['date']); ?></td>
                            <td><?php echo htmlspecialchars($expense['expense_header']); ?></td>
                            <td><?php echo htmlspecialchars($expense['expense_amount']); ?></td>
                            <td>
                                <?php if ($expense['file_path']) : ?>
                                    <a href="<?php echo htmlspecialchars($expense['file_path']); ?>" target="_blank">View File</a>
                                <?php else : ?>
                                    No File Uploaded
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No expenses found for this product.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for Adding Expense -->
    <div id="expenseModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add Expense</h3>
            <form id="expenseForm" method="post" enctype="multipart/form-data">
                <label for="expense_date">Date:</label>
                <input type="date" id="expense_date" name="expense_date" required>
                <label for="expense_header">Expense Header:</label>
                <input type="text" id="expense_header" name="expense_header" required>
                <label for="expense_amount">Expense Amount:</label>
                <input type="number" id="expense_amount" name="expense_amount" required>
                <label for="file_upload">Upload File (JPG or PDF):</label>
                <input type="file" id="file_upload" name="file_upload" accept=".jpg, .jpeg, .pdf">
                <input type="hidden" name="site" value="<?php echo htmlspecialchars($site); ?>">
                <input type="hidden" name="product" value="<?php echo htmlspecialchars($product); ?>">
                <input type="hidden" name="action" value="add_expense">
                <button type="submit">Submit</button>
                <button type="button" class="close">Cancel</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#addExpenseButton').on('click', function () { $('#expenseModal').show(); });
        $('.close').on('click', function () { $('#expenseModal').hide(); });

        $(window).on('click', function(event) {
            if ($(event.target).is('#expenseModal')) {
                $('#expenseModal').hide();
            }
        });
    </script>
</body>
</html>
