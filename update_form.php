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

// Get the table and record to update
$table = $_POST['table'] ?? null;
$updateId = $_POST['update_id'] ?? null;

$columns = [];
$updateData = [];

if ($table && $updateId) {
    // Fetch the column names for the selected table
    $result = $conn->query("SHOW COLUMNS FROM $table");
    while ($row = $result->fetch_assoc()) {
        if ($row['Key'] == 'PRI') {
            $primaryKey = $row['Field']; // Identify primary key column
        } else {
            $columns[] = $row['Field']; // Add non-primary key columns to the array
        }
    }

    // Fetch the existing record data to prefill the form
    $primaryKey = '';
    $result = $conn->query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
    while ($row = $result->fetch_assoc()) {
        $primaryKey = $row['Column_name'];
    }

    $query = "SELECT * FROM $table WHERE $primaryKey = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $updateId); // assuming the primary key is an integer
    $stmt->execute();
    $result = $stmt->get_result();
    $updateData = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating the record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
    if (isset($_POST['update_values'])) {
        $updateValues = $_POST['update_values'];
        
        if (!empty($updateValues)) {
            // Create the SET clause for SQL
            $setValues = [];
            foreach ($updateValues as $column => $value) {
                $setValues[] = "$column = ?";
            }
            $setClause = implode(", ", $setValues);

            try {
                // Prepare the UPDATE SQL query
                $stmt = $conn->prepare("UPDATE $table SET $setClause WHERE $primaryKey = ?");
                
                // Bind parameters dynamically (assuming all values are strings except the ID)
                $types = str_repeat('s', count($updateValues)) . 'i';  // 's' for string fields, 'i' for integer ID
                $values = array_merge(array_values($updateValues), [$updateId]);

                $stmt->bind_param($types, ...$values);
                $stmt->execute();
                $stmt->close();

                // Redirect to the page with table and update_id so we stay on the same record page
                header("Location: modify.php?table=$table");
                exit();
            } catch (mysqli_sql_exception $e) {
                // Check if error is related to foreign key constraints
                if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                    $errorMessage = "Cannot update/delete the record due to foreign key constraints.";
                } else {
                    $errorMessage = "Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Record</title>
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
    <h2>Update Record in <?= ucfirst($table) ?></h2>
    
    <?php if ($table && $updateData): ?>
        <form method="POST" action="">
            <input type="hidden" name="table" value="<?= $table ?>">
            <input type="hidden" name="update_id" value="<?= $updateId ?>">

            <?php foreach ($columns as $column): ?>
                <label for="<?= $column ?>"><?= ucfirst($column) ?>:</label>
                <input type="text" id="<?= $column ?>" name="update_values[<?= $column ?>]" value="<?= htmlspecialchars($updateData[$column]) ?>" required>
            <?php endforeach; ?>

            <button type="submit" name="action" value="update">Update Record</button>
        </form>
    <?php else: ?>
        <p>No data found for this record.</p>
    <?php endif; ?>
    <a href="modify.php?table=<?= $table ?>" class="button">Back to Records</a>

</div>

</body>
</html>
