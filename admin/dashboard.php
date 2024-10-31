<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Management Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Expense Manager</h2>
            <nav>
                <ul>
                    <li><a href="#">Dashboard</a></li>
                    <li><a href="add_user.php">Add User</a></li>
                    <li><a href="add_allowonss.html">Add Allowonss</a></li>
                    <!-- <li><a href="#">View Expenses</a></li> -->
                    <!-- <li><a href="#">Reports</a></li>
                    <li><a href="#">Settings</a></li> -->
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <header>
                <h1>Dashboard</h1>
            </header>
           
                
                <!-- Add Expense Form -->
                <div class="form-container">
                    <h2>Search User</h2>
                    <form action="#" method="post">
                        <label for="adduser">Search User:</label>
                        <input type="text" id="adduser" name="adduser" required><br><br>
                       
                        <!-- <label for="amount">Amount:</label>
                        <input type="number" id="amount" name="amount" step="0.01" required> -->
<!-- 
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="3" placeholder="Enter details"></textarea> -->

                        <button type="submit">Search User</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
