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

// Get the table name passed from modify.php
$table = $_GET['table'] ?? null;
$columns = [];
$primaryKey = '';

// Fetch column names excluding the primary key column
if ($table) {
    $result = $conn->query("SHOW COLUMNS FROM $table");
    while ($row = $result->fetch_assoc()) {
        if ($row['Key'] == 'PRI') {
            $primaryKey = $row['Field']; // Identify primary key column
        } else {
            $columns[] = $row['Field']; // Add non-primary key columns to the array
        }
    }
}


// Handle the form submission (inserting data into the table)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $values = $_POST['add_values'];
    
    // Ensure that the values are provided
    if (!empty($values)) {
        $columnsList = implode(", ", array_keys($values));
        $placeholders = implode(", ", array_fill(0, count($values), '?'));
        
    // Prepare the SQL statement
        if($table=="users"){

        }
        else{
            $stmt = $conn->prepare("INSERT INTO $table ($columnsList) VALUES ($placeholders)");
        }

        // Dynamically bind the values to the statement (assuming all are strings)
        $types = str_repeat('s', count($values));  // Assuming all values are strings
        $stmt->bind_param($types, ...array_values($values));

        // Execute the statement
        $stmt->execute();
        $stmt->close();

        // Redirect to modify.php after successful insertion
        header("Location: modify.php?table=$table");
        exit();
    } else {
        echo "Please fill in all the fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Entry</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        form { margin-top: 20px; }
        label, input { margin: 10px 0; padding: 8px; display: block; }
        button { padding: 10px 20px; margin-top: 10px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
         /* Global Reset */
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-image: url('images/bg_2.jpg'); /* Set the background image */
            background-size: cover; /* Make sure the image covers the entire page */
            background-position: center bottom -50px; /* Center the image */
            background-repeat: no-repeat; /* Prevent the image from repeating */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-size: 16px;
        }

        /* Container for the form */
        .form-container {
            background-color: rgba(255, 255, 255, 0.80); /* Semi-transparent white background */
            border-radius: 12px;
            padding: 30px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        header {
            font-size: 2rem;
            font-weight: bold;
            color: #003366; /* Dark blue */
            margin-bottom: 20px;
        }

        h1 {
            color: #003366;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        p {
            color: #444;
            margin-bottom: 20px;
        }

        /* Form styling */
        label {
            font-size: 1rem;
            text-align: left;
            margin-bottom: 0.5rem;
            display: block;
            color: #003366;
        }

        select, input[type="text"] {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            font-size: 1rem;
            background-color: #f8f8f8;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        select:focus, input[type="text"]:focus {
            background-color: #fff;
            border-color: #0066cc; /* Vibrant blue on focus */
            outline: none;
        }

        button {
            padding: 12px;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            background-color: #0066cc; /* Vibrant blue */
            color: white;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #004d99; /* Darker blue on hover */
        }

        .message {
            font-size: 0.9rem;
            margin-top: 15px;
        }

        .message.success {
            color: #28a745; /* Green for success */
        }

        .message.error {
            color: #dc3545; /* Red for errors */
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }

            header {
                font-size: 1.5rem;
            }

            h1 {
                font-size: 1.2rem;
            }
        }
        /* Add button styles */
        .redirect-button {
            display: inline-block;
            margin-top: 15px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .redirect-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Entry to <?= ucfirst($table) ?></h2>

    <!-- Form to add data -->
    <form method="POST" action="add_entry.php?table=<?= $table ?>">
        <input type="hidden" name="table" value="<?= $table ?>">

        <?php foreach ($columns as $column): ?>
            <div>
                <label for="<?= $column ?>"><?= ucfirst($column) ?>:</label>
                <input type="text" name="add_values[<?= $column ?>]" id="<?= $column ?>" required>
            </div>
        <?php endforeach; ?>

        <button type="submit" name="action" value="add">Add Entry</button>
    </form>

    <br>
    <a href="modify.php?table=<?= $table ?>">Back to Records</a>

</div>

</body>
</html>
