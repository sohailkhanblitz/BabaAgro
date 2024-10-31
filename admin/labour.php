<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="../css/labour.css">
</head>
<body>
    <div class="container">
        <form action="expense.php" method="">
            <h2>User Login</h2>

            <label for="number">Enter Mobile:</label>
            <div class="input-group">
                <input type="number" id="number" name="number" required>
                <button class="btn1" type="submit">Search</button>
            </div>

            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" readonly><br><br>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" readonly><br><br>

            <label for="user">User Type:</label>
            <input type="text" id="user" name="user" readonly><br><br>
            
            <button class="btn2" type="submit">Add Expense</button>
        </form>
    </div>
</body>
</html>
