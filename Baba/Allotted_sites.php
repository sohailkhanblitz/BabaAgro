<?php
// Start session and connect to the database
session_start();
include 'db_connection.php'; // Your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch all sites and products where allowances are assigned to the user
$sql = "
    SELECT 
        sp.sp_id,
        s.site_id,
        s.site_name, 
        sp.product_name, 
        (
            SELECT 
                SUM(am.al_amount) 
            FROM 
                allowance_master am 
            WHERE 
                am.sp_id = sp.sp_id AND am.user_id = ?
        ) AS total_allowance,
        (
            SELECT 
                COALESCE(SUM(em.ex_amount), 0) 
            FROM 
                expense_master em 
            WHERE 
                em.sp_id = sp.sp_id AND em.user_id = ?
        ) AS total_expense
    FROM 
        site_product sp
    JOIN 
        sites s ON sp.site_id = s.site_id
    WHERE 
        sp.sp_id IN (
            SELECT DISTINCT sp_id FROM allowance_master WHERE user_id = ?
        )
    GROUP BY 
        sp.sp_id, s.site_id, s.site_name, sp.product_name
";

$stmt = $conn->prepare($sql);

// Bind all three occurrences of $user_id
$stmt->bind_param("iii", $user_id, $user_id, $user_id);

$stmt->execute();
$result = $stmt->get_result();


// Organize data into an array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Sites and Products</title>
   
    <script>
        function redirectToHistory(userId, spId, siteId) {
            window.location.href = `history.php?user_id=${userId}&sp_id=${spId}&site_id=${siteId}`;
        }
    </script>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
<link rel="stylesheet" href="../Csss/allotted.css">
</head>
<body>
    <div class="container">
        
    <a href="./logout.php"><img src="../power-on.png" alt=""></a>
<?php
echo "Welcome, " . htmlspecialchars($_SESSION['logged_in_user']);
?>
<h1>Assigned Sites and Products</h1>
 
    <?php if (!empty($data)): ?>
        <table>
            <thead>
                <tr>
                    <th>Site - Product</th>
                    <th>Total Allowance Amount</th>
                    <th>Total Expense</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr onclick="redirectToHistory(<?php echo $user_id; ?>, <?php echo $row['sp_id']; ?>, <?php echo $row['site_id']; ?>)">
                        <td><?php echo htmlspecialchars($row['site_name'] . ' - ' . $row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['total_allowance'], 2)); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['total_expense'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No sites and products found with allowances assigned to you.</p>
    <?php endif; ?>
    </div>
</body>
</html>
