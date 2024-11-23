<?php
// Include the database connection file
include 'db_connection.php';
session_start();
if (!isset($_SESSION['admin_mobile'])) {
    header("Location: login.php");
    exit();
}
// Handle "Add Site" form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Get form data
    $site_name = $conn->real_escape_string($_POST['site_name']);
    $site_description = $conn->real_escape_string($_POST['site_description']);
    $created_date = date('Y-m-d H:i:s');
    $created_by = $_SESSION['admin_id'];
    $updated_date = date('Y-m-d H:i:s');
    $updated_by = $_SESSION['admin_id'];
    
    // Insert query for adding a site
    $sql = "INSERT INTO sites (site_name, site_description, created_date, created_by, updated_date, updated_by) 
            VALUES ('$site_name', '$site_description', '$created_date', '$created_by', '$updated_date', '$updated_by')";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }

    $conn->close();
}

// Handle "Add Product" form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Get form data
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $site_id = intval($_POST['site_id']);
    $status = $conn->real_escape_string($_POST['status']);  // Added status field
    $created_date = date('Y-m-d H:i:s');
    $created_by = $_SESSION['admin_id'];
    $updated_date = date('Y-m-d H:i:s');
    $updated_by = $_SESSION['admin_id'];
    
    // Insert query for adding a product
    $sql = "INSERT INTO site_product (site_id, product_name, status, created_date, created_by, updated_date, updated_by) 
            VALUES ('$site_id', '$product_name', '$status', '$created_date', '$created_by', '$updated_date', '$updated_by')";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success_product=1");
        exit();
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }

    $conn->close();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_status'])) {
  if (!$conn) {
      die("Database connection failed: " . mysqli_connect_error());
  }

  // Get form data
  $product_id = intval($_POST['product_id']);
  $new_status = $conn->real_escape_string($_POST['new_status']);
  $updated_date = date('Y-m-d H:i:s');
  $updated_by = $_SESSION['admin_id'];

  // Update query for changing product status
  $sql = "UPDATE site_product 
          SET status = '$new_status', updated_date = '$updated_date', updated_by = '$updated_by' 
          WHERE sp_id = $product_id";

  if ($conn->query($sql) === TRUE) {
      header("Location: " . $_SERVER['PHP_SELF'] . "?status_updated=1");
      exit();
  } else {
      echo "<p>Error updating status: " . $conn->error . "</p>";
  }

  $conn->close();
}


// Fetch site details with associated products (ensure we get each product on a new row)
$sites = [];
if ($conn) {
    $result = $conn->query("SELECT s.site_id, s.site_name, s.site_description, s.created_date, sp.product_name, sp.status ,sp.sp_id
                            FROM sites s
                            LEFT JOIN site_product sp ON s.site_id = sp.site_id
                            ORDER BY s.site_name ");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sites[] = $row;
        }
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Site</title>
  <link rel="stylesheet" href="../Csss/Sites.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 
</head>
<body>
  <nav class="navbar">
    <div class="nav-items">
    <a href="./logout.php"><i class="fas fa-sign-out-alt"></i></a>
      <a href="./Home.php">Home</a>
      <a href="./Sites.php">Sites</a>
      <a href="./Add_user.php">User</a>
      <a href="./Allowance.php">Allowance</a>
    

    </div>
  </nav>

  <div class="container">
    <h2 style=" text-align: center;">Add Site Details</h2>
    <form action="" method="post">
      <label for="site_name">Site Name:</label>
      <input type="text" id="site_name" name="site_name" required>

      <label for="site_description">Site Description:</label>
      <textarea id="site_description" name="site_description" rows="4" required></textarea>

      <button type="submit" name="submit">Add Site</button>
    </form>

  
    <!-- Display success messages -->
    <?php if (isset($_GET['success'])): ?>
      <div class="site" id="success-site">
         <p>Site added successfully!</p>
      </div>
    <?php endif; ?>
    <?php if (isset($_GET['success_product'])): ?>
      <div class="product" id="success-product">
          <p>Product added successfully!</p>
      </div>
    <?php endif; ?>
    <?php if (isset($_GET['status_updated'])): ?>
      <div class="status" id="status-updated">
         <p>Status updated successfully!</p>
     </div>
    <?php endif; ?>



    <!-- Display sites in a table -->
   
    <?php if (!empty($sites)): ?>
      <h3>Sites</h3>
      <div class="table-responsive">
      <table border="1">
      <thead>
  <tr>
    <th>Site</th>
    <th>Desc</th>
    <th>Product</th>
    <th>Status</th>
    <th>Change Status</th> <!-- New column -->
    <th>Add</th>
  </tr>
</thead>
<tbody>
  <?php
    foreach ($sites as $site):
  ?>
    <tr>
      <td><?php echo htmlspecialchars($site['site_name']); ?></td>
      <td><?php echo htmlspecialchars($site['site_description']); ?></td>
      <td><?php echo $site['product_name'] ? htmlspecialchars($site['product_name']) : 'No products added yet.'; ?></td>
      <td><?php echo htmlspecialchars($site['status']); ?></td>
      <td>
        <!-- Dropdown for changing status -->
        <form method="post" action="">
          <input type="hidden" name="product_id" value="<?php echo $site['sp_id']; ?>">
          <select name="new_status" onchange="this.form.submit()">
            <option value="Active" <?php echo ($site['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo ($site['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
            
            <!-- <option value="pushed for settlement" <?php echo ($site['status'] == 'pushed for settlement') ? 'selected' : ''; ?>>pushed for settlement</option> -->
          </select>
        </form>
      </td>
      <td>
        <button type="button" onclick="openModal(<?php echo $site['site_id']; ?>)">+</button>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>

      </table>
    </div>
    <?php else: ?>
      <p class="nosite">No sites found.</p>
    <?php endif; ?>
  </div>

  <!-- Modal for adding product -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2 class="add">Add Product</h2>
      <form action="" method="post">
        <input type="hidden" id="modal_site_id" name="site_id">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required>

        <!-- Dropdown for Status -->
        <label for="status">Status:</label>
        <select id="status" name="status" required>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>

        <button type="submit" name="add_product">Add Product</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(site_id) {
        document.getElementById("modal_site_id").value = site_id;
        document.getElementById("productModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("productModal").style.display = "none";
    }
  </script>



<script>
    // Wait for the DOM to load
    document.addEventListener('DOMContentLoaded', function() {
        // Find all elements with a success message
        const successMessages = document.querySelectorAll('.site, .product, .status');
        
        // Set a timeout to remove them after 3 seconds
        setTimeout(() => {
            successMessages.forEach(message => {
                message.style.display = 'none';
            });
        }, 3000); // 3000 milliseconds = 3 seconds
    });
</script>









</body>
</html>
