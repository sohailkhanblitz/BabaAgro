<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="container">
        <form action="dashboard.php" method="">

            <h2>Admin Login</h2>
            <label for="name">User Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>


            <button type="submit" name="login" value="admin login" >Submit</button>
            <a href="labour.php">User Login</a>

        </form>
    </div>
</body>

</html>