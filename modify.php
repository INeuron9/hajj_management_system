<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "hajj_database";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all tables dynamically
$tableNames = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tableNames[] = $row[0];
}

// Get the table selected from the URL or dropdown
$table = $_GET['table'] ?? null; // Get selected table from URL
$columns = [];
$primaryKey = '';

// Fetch column names and primary key based on selected table
if ($table) {
    $result = $conn->query("SHOW COLUMNS FROM $table");
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
        if ($row['Key'] == 'PRI') {
            $primaryKey = $row['Field'];  // Identify primary key column
        }
    }
}

// Handle Add functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    // Ensure add_values is set
    if (isset($_POST['add_values']) && is_array($_POST['add_values'])) {
        $values = $_POST['add_values'];

        // Ensure that there are values to insert
        if (!empty($values)) {
            $columnsList = implode(", ", array_keys($values));
            $placeholders = implode(", ", array_fill(0, count($values), '?'));

            // Prepare the SQL statement
            $stmt = $conn->prepare("INSERT INTO $table ($columnsList) VALUES ($placeholders)");

            // Dynamically bind the values to the statement (assuming all are strings)
            $types = str_repeat('s', count($values));  // Assuming all values are strings
            $stmt->bind_param($types, ...array_values($values));

            // Execute the statement
            $stmt->execute();
            $stmt->close();

            // Redirect to the same page to prevent form resubmission
            header("Location: modify.php?table=$table");
            exit();
        } else {
            echo "No values to insert.";
        }
    } else {
       # echo "No values provided for insertion.";
    }
}


// Handle Update functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
    $updateValues = $_POST['update_values'] ?? [];  // Make sure we get the update values
    $updateId = $_POST['update_id'];

    if (!empty($updateValues)) {
        $setValues = [];

        // Dynamically prepare the SET clause for the update query
        foreach ($updateValues as $column => $value) {
            $setValues[] = "$column = ?";
        }

        $setClause = implode(", ", $setValues);

        // Prepare the UPDATE query
        $stmt = $conn->prepare("UPDATE $table SET $setClause WHERE $primaryKey = ?");
        
        // Bind parameters dynamically (assuming all values are strings)
        $types = str_repeat('s', count($updateValues)) . 'i';  // 's' for strings, 'i' for integer ID
        $values = array_merge(array_values($updateValues), [$updateId]);

        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();

        header("Location: modify.php?table=$table"); // Redirect to refresh the page
        exit();
    } else {
        echo "No update values provided.";
    }
}

// Handle Delete functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $deleteId = $_POST['delete_id'];

    // Check if the delete_id is 0 and show an error message
    if ($deleteId == 0) {
        $errorMessage = "Attempt was made to delete the admin. Not allowed.";
    } else {
        try {
            // Prepare the DELETE SQL query
            $stmt = $conn->prepare("DELETE FROM $table WHERE $primaryKey = ?");
            $stmt->bind_param("i", $deleteId);
            $stmt->execute();
            $stmt->close();

            // Redirect to refresh the page
            header("Location: modify.php?table=$table");
            exit();
        } catch (mysqli_sql_exception $e) {
            // Check if error is related to foreign key constraints
            if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                $errorMessage = "Cannot delete the record due to foreign key constraints.";
            } else {
                $errorMessage = "Error: " . $e->getMessage();
            }
        }
    }
}


// Fetch data from selected table (for Update/Delete)
$data = [];
if ($table) {
    $result = $conn->query("SELECT * FROM $table");
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Modify Records</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-image: url('images/background.jpg'); /* Set your background image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 16px;
            padding: 20px;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            padding: 20px;
            max-width: 1000px;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        header {
            font-size: 2rem;
            font-weight: bold;
            color: #003366;
            text-align: center;
            margin-bottom: 20px;
        }
        /* Top-right button */
        .top-right {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .top-right button {
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            background-color: #004d99; /* Dark blue */
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .top-right button:hover {
            background-color: #003366; /* Darker blue */
        }
        form {
            margin-bottom: 30px;
        }

        label {
            font-size: 1rem;
            margin-bottom: 5px;
            display: inline-block;
            color: #003366;
        }

        select, button {
            padding: 10px;
            margin: 10px 5px 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        button {
            background-color: #0066cc;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #004d99;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #003366;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            header {
                font-size: 1.5rem;
            }
        }
        .error {
            color: red;
            font-weight: bold;
            margin-top: 20px;
        }
        
    </style>
</head>
<body>
<!-- Top-right Button -->
<div class="top-right">
    <button onclick="window.location.href='http://localhost/hajj/admin_dashboard.php'">Dashboard</button>
  </div>
  <?php if (isset($errorMessage)): ?>
    <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>

<div class="container">
    <h2>Modify Database Records</h2>

    <!-- Table selection (always visible) -->
    <form method="GET" action="">
        <label for="table">Select Table:</label>
        <select name="table" id="table" onchange="this.form.submit()">
            <option value="">--Select Table--</option>
            <?php foreach ($tableNames as $tableName): ?>
                <option value="<?= $tableName ?>" <?= $table === $tableName ? 'selected' : '' ?>>
                    <?= ucfirst($tableName) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($table): ?>
        <!-- Add button (opens form to add a new entry) -->
        <form method="GET" action="add_entry.php">
            <input type="hidden" name="table" value="<?= $table ?>">
            <button type="submit">Add New Entry</button>
        </form>


        <!-- Display table data (for Update/Delete) -->
        <?php if (!empty($data)): ?>
            <h3>Existing Records in <?= ucfirst($table) ?></h3>
            <table>
                <thead>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <th><?= ucfirst($column) ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td><?= $row[$column] ?></td>
                            <?php endforeach; ?>
                            <td>
                                <!-- Update button -->
                                <form method="POST" action="update_form.php" style="display:inline;">
                                    <input type="hidden" name="table" value="<?= $table ?>">
                                    <input type="hidden" name="update_id" value="<?= $row[$primaryKey] ?>">
                                    <button type="submit" name="action" value="update">Update</button>
                                </form>

                                <!-- Delete button -->
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="table" value="<?= $table ?>">
                                    <input type="hidden" name="delete_id" value="<?= $row[$primaryKey] ?>">
                                    <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>
