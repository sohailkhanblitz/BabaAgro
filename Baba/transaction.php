<?php
// Include the database connection file
include 'db_connection.php';

// Get user information from URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$first_name = isset($_GET['first_name']) ? htmlspecialchars($_GET['first_name']) : '';
$last_name = isset($_GET['last_name']) ? htmlspecialchars($_GET['last_name']) : '';
$mobile = isset($_GET['mobile']) ? htmlspecialchars($_GET['mobile']) : '';

// Fetch distinct data with Total Allowance and Total Expense
$query = "
    SELECT DISTINCT
        sp.site_id, 
        s.site_name, 
        sp.product_name, 
        sp.sp_id,
        COALESCE((SELECT SUM(am.al_amount) 
                  FROM allowance_master am 
                  WHERE am.sp_id = sp.sp_id AND am.user_id = ?), 0) AS total_allowance,
        COALESCE((SELECT SUM(em.ex_amount) 
                  FROM expense_master em 
                  WHERE em.sp_id = sp.sp_id AND em.user_id = ?), 0) AS total_expense
    FROM 
        allowance_master am
    INNER JOIN 
        site_product sp ON am.sp_id = sp.sp_id
    INNER JOIN 
        sites s ON sp.site_id = s.site_id
    WHERE 
        am.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allowances</title>
    <link rel="stylesheet" href="../Csss/transaction.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
    <div class="back" style="display:flex;">
            <a href="home.php"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>
    <h1>Allowances</h1>
    <p>User: <?php echo "$first_name $last_name ($mobile)"; ?></p>
    <table>
        <thead>
            <tr>
                <th>Site Name</th>
                <th>Product Name</th>
                <th>Total Allowance</th>
                <th>Total Expense</th>
                <th>Avl Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr onclick="redirectToHistory(<?php echo $row['site_id']; ?>, <?php echo $row['sp_id']; ?>, <?php echo $user_id; ?>)">
                        <td><?php echo $row['site_name']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['total_allowance']; ?></td>
                        <td><?php echo $row['total_expense']; ?></td>
                        <td><?php echo $row['total_allowance'] - $row['total_expense']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
    <script>
        function redirectToHistory(siteId, spId, userId) {
            window.location.href = `history.php?site_id=${siteId}&sp_id=${spId}&user_id=${userId}`;
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
