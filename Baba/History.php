<?php
// Include the database connection file
include 'db_connection.php';
session_start();

// Handle form submission for expense addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ex_header'])) {
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

// Handle AJAX request to update the status of the product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status_sp_id'])) {
    $sp_id = intval($_POST['update_status_sp_id']);

    // Update the status of the site_product to "pushed for settlement"
    $update_query = "UPDATE site_product SET status = 'pushed for settlement' WHERE sp_id = $sp_id";

    if ($conn->query($update_query)) {
        echo 'success';  // Return success message
    } else {
        echo 'error';  // Return error message if update fails
    }
    exit;  // Exit to prevent the rest of the page from being rendered
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

// Query to fetch product name and status
$product_query = "SELECT product_name, status FROM site_product WHERE sp_id = $sp_id";
$product_result = $conn->query($product_query);
if ($product_result && $product_result->num_rows > 0) {
    $product_row = $product_result->fetch_assoc();
    $product_name = $product_row['product_name'];
    $product_status = $product_row['status'];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container">
        <div  class="back"> 
            <a href="./Allotted_sites.php"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>    
    <div class="header">
        <h1>Site: <?= htmlspecialchars($site_name) ?></h1>
        <h2>Product: <?= htmlspecialchars($product_name) ?></h2>
        <h2> Status: <span id="productStatus"><?= htmlspecialchars($product_status) ?></span></h2>
    </div>

        



        <h2>
            <?= ucfirst($view) ?> Records
        </h2>
        <div class="toggle">
            <div class="toggle-container">
                <a href="history.php?site_id=<?= $site_id ?>&sp_id=<?= $sp_id ?>&user_id=<?= $user_id ?>&view=allowance"
                    class="<?= $view === 'allowance' ? 'active' : '' ?>">Allowance</a>
                <a href="history.php?site_id=<?= $site_id ?>&sp_id=<?= $sp_id ?>&user_id=<?= $user_id ?>&view=expense"
                    class="<?= $view === 'expense' ? 'active' : '' ?>">Expense</a>
            </div>
        </div>

        <?php if ($view === 'expense' && $_SESSION['user_type'] !== 'admin') : ?>
        <!-- <div class="expense">
                <button onclick="openModal()">+</button>
            </div> -->
        <!-- Push for Settlement Button -->
        <div class="status-update">
            <?php if ($product_status == 'Active') : ?>
            <div class="expense">
                <!-- add expense button  -->
                <button onclick="openModal()">+</button>
                <!-- </div> -->
                <button id="pushForSettlementBtn" data-sp_id="<?= $sp_id ?>">Push for Settlement</button>
                <?php else : ?>
                <button disabled>+</button>
                <button disabled id="pushForSettlementBtn" data-sp_id="<?= $sp_id ?>">Push for Settlement</button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
<!-- </div> -->


            <?php if (!empty($records)) : ?>
            <table>
                <thead>
                    <tr>
                        <?php if ($view === 'allowance') : ?>
                        <th>Amount</th>
                        <th>Created Date</th>
                        <?php else : ?>
                        <th>Header</th>
                        <th>Amount</th>
                        <th>File Path</th>
                        <th>Created Date</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record) : ?>
                    <tr>
                        <?php if ($view === 'allowance') : ?>
                        <td>
                            <?= $record['al_amount'] ?>
                        </td>
                        <td>
                            <?= $record['created_date'] ?>
                        </td>
                        <?php else : ?>
                        <td>
                            <?= $record['ex_header'] ?>
                        </td>
                        <td>
                            <?= $record['ex_amount'] ?>
                        </td>
                        <td><a href="<?= $record['file_path'] ?>" target="_blank">View File</a></td>
                        <td>
                            <?= $record['created_date'] ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <p>No records found.</p>
            <?php endif; ?>

            <!-- Add Expense Modal -->
            <div id="addExpenseModal">
                <div class="modal-content">
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
                        <button type="button" id="clearFileBtn" class="clear-btn" style="margin-right: 10px;">Clear File</button>
                        <div id="fileError" style="color: red; display: none;">Uploaded file exceeds the 35 MB limit.</div>
                        <div class="modal-footer">
                            <button type="submit" id="submitBtn" class="save-btn">Save</button>
                            <button type="button" class="close-btn" onclick="closeModal()">Close</button>
                        </div>
                    </form>
                </div>
            </div>
<script>
    // Get references to elements
const fileInput = document.getElementById('file_path');
const fileError = document.getElementById('fileError');
const clearFileBtn = document.getElementById('clearFileBtn');

// Clear File Button Logic
clearFileBtn.addEventListener('click', function () {
    fileInput.value = ''; // Clear the file input
    fileError.style.display = 'none'; // Hide any error messages
});

</script>
            <!-- file upload script  -->
            
<script>
    document.getElementById('submitBtn').addEventListener('click', function (e) {
        const fileInput = document.getElementById('file_path');
        const fileError = document.getElementById('fileError');

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileSize = file.size; // File size in bytes
            const maxSize = 35 * 1024 * 1024; // 35 MB
            const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (!validImageTypes.includes(file.type)) {
                e.preventDefault(); // Prevent form submission
                fileError.textContent = "Only JPG, PNG, and GIF images are allowed.";
                fileError.style.display = 'block';
            } else if (fileSize > maxSize) {
                e.preventDefault(); // Prevent form submission
                fileError.textContent = "Uploaded file exceeds the 35 MB limit.";
                fileError.style.display = 'block';
            } else {
                fileError.style.display = 'none';
            }
        }
    });
</script>

            <script>
                function openModal() {
                    document.getElementById('addExpenseModal').classList.add('active');
                }

                function closeModal() {
                    document.getElementById('addExpenseModal').classList.remove('active');
                }

                // Update status dynamically when "Push for Settlement" is clicked
                document.getElementById('pushForSettlementBtn').addEventListener('click', function () {
                    var sp_id = this.getAttribute('data-sp_id');
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200 && xhr.responseText === 'success') {
                            // Update the status dynamically
                            document.getElementById('productStatus').textContent = "Pushed for Settlement";
                            // document.getElementById('pushForSettlementBtn').textContent = "Already Pushed";
                            document.getElementById('pushForSettlementBtn').disabled = true;
                            document.querySelector('button[onclick="openModal()"]').disabled = true; // Disable "+" button
                        } else {
                            alert('Failed to update status.');
                        }
                    };
                    xhr.send('update_status_sp_id=' + sp_id);
                });

                // Disable buttons initially if status is not "Active"
                window.addEventListener('DOMContentLoaded', function () {
                    var status = document.getElementById('productStatus').textContent.trim();
                    if (status !== 'Active') {
                        document.getElementById('pushForSettlementBtn').disabled = true;
                        var addExpenseButton = document.querySelector('button[onclick="openModal()"]');
                        if (addExpenseButton) {
                            addExpenseButton.disabled = true;
                        }
                    }
                });

            </script>
             <script>
        // Get today's date
        const today = new Date();

        // Calculate the minimum date (2 days before today)
        const minDate = new Date();
        minDate.setDate(today.getDate() - 2);

        // Format the dates to YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        // Set the min and max attributes for the input field
        const dateInput = document.getElementById('date');
        dateInput.min = formatDate(minDate);
        dateInput.max = formatDate(today);
    </script>
        </div>
</body>

</html>

<?php
$conn->close();
?>