<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="../css/expense.css">
</head>
<body>
    <div class="container">
        <form action="#" method="">
            <h2>Add Expense</h2>

       
            <label for="site">Select Site:</label>
            <select name="site" required id="select">
                <option selected disabled>Select Sites</option>
                <option value="1">Site1</option>
                <option value="2">Site2</option>
                <option value="3">Site3</option>
                <option value="4">Site4</option>
            </select><br><br>

            <label for="product">Select Product:</label>
            <select name="product" required id="select">
                <option selected disabled>Select Product</option>
                <option value="1">Product1</option>
                <option value="2">Product2</option>
                <option value="3">Product3</option>
                <option value="4">Product4</option>
            </select><br><br>

           
            <label for="text">Total Allowenss:</label>
            <input type="text" id="text" name="text" readonly>

            <!-- New Expense Fields -->
            <label for="expense_amount">Expense Amount:</label>
            <input type="number" id="expense_amount" name="expense_amount" step="0.01" required><br><br>

            <label for="expense_header">Expense Header:</label>
            <textarea id="expense_header" name="expense_header" rows="3" placeholder="Header (optional)"></textarea><br><br>
            
            <label for="file">File Upload:</label>
            <input type="file" id="file" name="file" required>


            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
