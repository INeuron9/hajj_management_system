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

// Function to get column names for a table
function getColumnNames($table, $conn) {
    $columns = [];
    $result = $conn->query("SHOW COLUMNS FROM $table");
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

// Get table and selected columns from the form
$table = $_POST['table'] ?? null;
$columns = $_POST['columns'] ?? [];
$data = [];

// Fetch data based on the selected table and columns
if ($table && !empty($columns)) {
    $columnsList = implode(',', $columns);
    $query = "SELECT $columnsList FROM $table";
    $result = $conn->query($query);
    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Fetch all table names dynamically
$tableNames = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tableNames[] = $row[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            max-width: 800px;
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
    </style>
</head>
<body>
    <!-- Top-right Button -->
<div class="top-right">
    <button onclick="window.location.href='http://localhost/hajj/admin_dashboard.php'">Dashboard</button>
  </div>
    <div class="container">
        <header>Admin Dashboard</header>
        <form method="POST" action="">
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
            <form method="POST" action="">
                <input type="hidden" name="table" value="<?= $table ?>">
                <label>Select Columns:</label>
                <div class="checkbox-group">
                    <?php foreach (getColumnNames($table, $conn) as $column): ?>
                        <label>
                            <input type="checkbox" name="columns[]" value="<?= $column ?>" 
                                   <?= in_array($column, $columns) ? 'checked' : '' ?>>
                            <?= ucfirst($column) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit">Show</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($data)): ?>
            <table>
                <thead>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <th><?= ucfirst($column) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td><?= $row[$column] ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
