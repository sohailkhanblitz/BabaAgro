<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connection.php'; // Include your database connection

// Handle the form submission for adding an expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_expense') {
        // Existing code for adding expense
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
    } elseif ($_POST['action'] === 'update_status') {
        // New code for updating status
        $site = $_POST['site'];
        $product = $_POST['product'];

        // Update the allowancemaster table to change status to 'done'
        $stmt = $conn->prepare("UPDATE allowancemaster SET status = 'Pushed For Approval' WHERE site = ? AND product = ?");
        $stmt->bind_param("ss", $site, $product);

        if ($stmt->execute()) {
            // Redirect to the same page to avoid resubmission on refresh
            header("Location: expense.php?site=$site&product=$product");
            exit;
        } else {
            echo "Failed to update status: " . $stmt->error; // Output detailed error
            exit;
        }

        $stmt->close();
    }
}

// The rest of the code remains the same...

// Fetch the selected site and product from the URL
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

// Get user's full name from session
$userFullName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Fetch the status for the selected site
$status = '';
if ($site) {
    $stmt = $conn->prepare("SELECT status FROM allowancemaster WHERE site = ? LIMIT 1");
    $stmt->bind_param("s", $site);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $status = $row['status'];
    }
    $stmt->close();
}

// Fetch the allowance amount from allowancemaster based on site and product
$allowanceAmount = 0; // Initialize variable for allowance amount
if ($site && $product) {
    $stmt = $conn->prepare("SELECT amount FROM allowancemaster WHERE site = ? AND product = ?");
    $stmt->bind_param("ss", $site, $product);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $allowanceAmount = $row['amount']; // Get the allowance amount
    }
    $stmt->close();
}

// Calculate the sum of expenses for the selected site and product
$totalExpenses = 0; // Initialize variable for total expenses
if ($site && $product) {
    $stmt = $conn->prepare("
        SELECT SUM(expense_amount) AS total_expense 
        FROM expense 
        WHERE site = ? AND product = ?
    ");
    $stmt->bind_param("ss", $site, $product);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $totalExpenses = $row['total_expense'] ? $row['total_expense'] : 0; // Get the total expenses
    }
    $stmt->close();
}

// Calculate the remaining balance
$remainingBalance = $allowanceAmount - $totalExpenses;

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
      
    </style>
</head>
<body>
    
    <div class="container">

    <a href="labour.php"><button class="bbtn">Back</button></a>


        <div class="user-info">
            <div>Welcome, <?php echo htmlspecialchars($userFullName); ?></div>
            <div>Status: <?php echo htmlspecialchars($status); ?></div>
        </div>
        <header>
            <h1>Site: <?php echo htmlspecialchars($site); ?> Product: <?php echo htmlspecialchars($product); ?></h1>
            <button id="addExpenseButton" class="add-expense-button" <?php echo $status !== 'active' ? 'disabled' : ''; ?>>Add New Expense</button>
            <form method="POST" action="" style="display:inline;" onsubmit="return confirmMarkAsDone()">
    <input type="hidden" name="site" value="<?php echo htmlspecialchars($site); ?>">
    <input type="hidden" name="product" value="<?php echo htmlspecialchars($product); ?>">
    <button type="submit" name="action" value="update_status" class="update-status-button" <?php echo $status !== 'active' ? 'disabled' : ''; ?>>Push For  Settlement</button>
</form>

        </header>
        <div>
        <div class="totals">
    <p>Total Allowance: <?php echo htmlspecialchars(number_format($allowanceAmount, 2)); ?></p>
    <p>Total Expenses: <?php echo htmlspecialchars(number_format($totalExpenses, 2)); ?></p>
    <p>Remaining Balance: <?php echo htmlspecialchars(number_format($remainingBalance, 2)); ?></p>
</div>

        </div>

        <table class="expense-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Expense Header</th>
                    <th>Amount</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expenses)): ?>
                    <tr><td colspan="4">No expenses found.</td></tr>
                <?php else: ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['date']); ?></td>
                            <td><?php echo htmlspecialchars($expense['expense_header']); ?></td>
                            <td><?php echo htmlspecialchars($expense['expense_amount']); ?></td>
                            <td>
                                <?php if ($expense['file_path']): ?>
                                    <a href="<?php echo htmlspecialchars($expense['file_path']); ?>" target="_blank">View File</a>
                                <?php else: ?>
                                    No File
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for adding expense -->
    <div id="expenseModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Expense</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="site" value="<?php echo htmlspecialchars($site); ?>">
                <input type="hidden" name="product" value="<?php echo htmlspecialchars($product); ?>">
                <label for="expense_header">Expense Header: <span class="required">*</span></label>
                <input type="text" id="expense_header" name="expense_header" required>
                <label for="expense_amount">Expense Amount: <span class="required">*</span></label>
                <input type="number" id="expense_amount" name="expense_amount" required>

                <label for="expense_date">Date:</label>
                <input type="date" id="expense_date" name="expense_date" required>

                <label for="file_upload">File Upload (JPG/PDF):</label><div style="font-size:15px;">The file size limit should not exceed 35MB</div>
                <input type="file" id="file_upload" name="file_upload" accept=".jpg, .jpeg, .pdf">

                <button type="submit" name="action" value="add_expense">Submit</button>
                <!-- <button type="button" class="close">Cancel</button> -->
            </form>
        </div>
    </div>

    <script>
        // Show the modal for adding expense
        document.getElementById("addExpenseButton").onclick = function() {
            document.getElementById("expenseModal").style.display = "block";
        }

        // Close the modal
        document.querySelector(".close").onclick = function() {
            document.getElementById("expenseModal").style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            if (event.target == document.getElementById("expenseModal")) {
                document.getElementById("expenseModal").style.display = "none";
            }
        }
    </script>
    <script>
   
    const today = new Date();
    
    // Format today's date as YYYY-MM-DD
    const formattedToday = today.toISOString().split('T')[0];
    
    // Calculate the date two days before today
    const twoDaysAgo = new Date();
    twoDaysAgo.setDate(today.getDate() - 2);
    const formattedTwoDaysAgo = twoDaysAgo.toISOString().split('T')[0];
    
    // Set min and max attributes for the date input
    const dateInput = document.getElementById("expense_date");
    dateInput.setAttribute("min", formattedTwoDaysAgo);
    dateInput.setAttribute("max", formattedToday);
    dateInput.value = formattedToday; // Set default date to today

    
</script>
<script>
    function confirmMarkAsDone() {
        return confirm("Are you sure you want to push for settlement?");
    }
</script>


</body>
</html>
