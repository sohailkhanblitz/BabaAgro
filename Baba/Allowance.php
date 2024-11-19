<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Allowance</title>
  <link rel="stylesheet" href="../Csss/Allowance.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-items">
      <a href="./Home.php">Home</a>
      <a href="./Sites.php">Sites</a>
      <a href="./Add_user.php">Add User</a>
      <a href="./Allowance.php">Add Allowance</a>
      <a href="./logout.php" style="float: right; color: red;">Logout</a>

    </div>
  </nav>

  <!-- Form Section -->
  <div class="form-container">
    <h2>Add Allowance</h2>
    <form action="allowance.php" method="POST">
      <!-- User Dropdown -->
      <label for="user">Select User:</label>
      <select name="user_id" id="user" required>
        <option value="">-- Select User --</option>
        <?php
        include 'db_connection.php'; // Ensure database connection file is included
        $users = mysqli_query($conn, "SELECT user_id, CONCAT(first_name, ' ', last_name) AS full_name FROM user_master");
        while ($row = mysqli_fetch_assoc($users)) {
          echo "<option value='{$row['user_id']}'>{$row['full_name']}</option>";
        }
        ?>
      </select>
      
      <!-- Site Dropdown -->
      <label for="site">Select Site:</label>
      <select name="site_id" id="site" required>
        <option value="">-- Select Site --</option>
        <?php
        $sites = mysqli_query($conn, "SELECT site_id, site_name FROM sites");
        while ($row = mysqli_fetch_assoc($sites)) {
          echo "<option value='{$row['site_id']}'>{$row['site_name']}</option>";
        }
        ?>
      </select>
      
      <!-- Product Dropdown -->
      <label for="product">Select Product:</label>
      <select name="sp_id" id="product" required>
        <option value="">-- Select Product --</option>
      </select>
      
      <!-- Amount Input -->
      <label for="amount">Allowance Amount:</label>
      <input type="number" name="amount" id="amount" placeholder="Enter Amount" required>

      <!-- Submit Button -->
      <button type="submit" name="submit">Submit</button>
    </form>
  </div>

  <script>
    $(document).ready(function() {
      // Fetch products based on selected site
      $('#site').change(function() {
        var siteId = $(this).val();
        $('#product').html('<option value="">Loading...</option>');
        if (siteId) {
          $.ajax({
            url: 'allowance.php', // The same file handles the request
            type: 'POST',
            data: { site_id: siteId, fetch_products: true },
            success: function(response) {
              $('#product').html(response);
            }
          });
        } else {
          $('#product').html('<option value="">-- Select Product --</option>');
        }
      });
    });
  </script>
</body>
</html>

<?php
include 'db_connection.php'; // Ensure database connection
session_start();
if (!isset($_SESSION['admin_mobile'])) {
  header("Location: login.php");
  exit();
}

// Fetch products based on site_id
if (isset($_POST['fetch_products'])) {
    $site_id = $_POST['site_id'];
    $products = mysqli_query($conn, "SELECT sp_id, product_name FROM site_product WHERE site_id = '$site_id'");
    echo "<option value=''>-- Select Product --</option>";
    while ($row = mysqli_fetch_assoc($products)) {
        echo "<option value='{$row['sp_id']}'>{$row['product_name']}</option>";
    }
    exit; // Stop further execution for AJAX requests
}

// Insert data into allowance_master table
if (isset($_POST['submit'])) {
    $user_id = $_POST['user_id'];
    $sp_id = $_POST['sp_id'];
    $amount = $_POST['amount'];
    $created_date = date('Y-m-d H:i:s');
    $updated_date = date('Y-m-d H:i:s');
    $created_by = $_SESSION['admin_id']; // Replace with the session user's ID
    $updated_by = $_SESSION['admin_id']; // Replace with the session user's ID

    $query = "INSERT INTO allowance_master (user_id, sp_id, al_amount, date, created_date, created_by, updated_date, updated_by)
              VALUES ('$user_id', '$sp_id', '$amount', '$created_date', '$created_date', '$created_by', '$updated_date', '$updated_by')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Allowance added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>
