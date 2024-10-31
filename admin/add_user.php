<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/add_user.css">
</head>

<body>
    <div class="container">
        <form action="expense.php" method="">
            <h2>Add New User</h2>




            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" readonly><br><br>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" readonly><br><br>

            <label for="number">Enter Mobile:</label>
            <input type="number" id="number" name="number" required><br><br>

            <label for="Email">Enter Email:</label>
            <input type="email" id="email" name="email"><br><br>



            <label for="user">User Role:</label>
            <input type="text" id="user" name="user" readonly><br><br>

            <button class="adduser" type="submit">Add User</button>
        </form>
    </div>
</body>

</html>