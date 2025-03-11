<?php
// Database connection
$host = "localhost";
$user = "root";
$password = ""; // Update with your database password
$dbname = "hajj_database"; // Replace with your database name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user_id from URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die("Error: User ID not provided.");
}
$user_id = intval($_GET['user_id']); // Convert to integer for safety
// Get edit flag from URL
if (isset($_GET['edit']) ) {

    $delete_query = "DELETE FROM food_booking WHERE user_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}
// Check if the user already has a booking
$check_booking_query = "SELECT * FROM food_booking WHERE user_id = ?";
$check_stmt = $conn->prepare($check_booking_query);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // User already has a booking
    $user_has_booking = true;
} else {
    $user_has_booking = false;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$user_has_booking) {
    $restaurant_id = $_POST["restaurant_id"];
    $preference = $_POST["preference"];
    $venue = $_POST["venue"];

    // Insert into food_booking table
    $insert_query = "INSERT INTO food_booking (user_id, restaurant_id, preference, venue) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iiss", $user_id, $restaurant_id, $preference, $venue);

    try {
        if ($stmt->execute()) {
            $user_has_booking = true;
        } else {
            throw new Exception("Booking failed. Please try again.");
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Fetch restaurants
$restaurants_query = "SELECT restaurant_id, name FROM restaurant_details";
$restaurants_result = $conn->query($restaurants_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Booking</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
         /* Global Reset */
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-image: url('images/bg_5.jpg'); /* Set the background image */
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
        /* Styling for form inputs */
        input[type="number"], select, input[type="text"] {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            font-size: 1rem;
            background-color: #f8f8f8;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        input[type="number"]:focus, select:focus, input[type="text"]:focus {
            background-color: #fff;
            border-color: #0066cc; /* Vibrant blue on focus */
            outline: none;
        }

        input[type="number"] {
            -moz-appearance: textfield; /* Removes spinner in Firefox */
        }
    </style>
</head>
<body>
    <div class="form-container">
        <header>Food Booking</header>

        <!-- Show error message only after form submission if the user has a booking -->
        <?php if ($user_has_booking) { ?>
            <p class='message error'>You have successfully booked a restaurant.</p>
            <a href="http://localhost/hajj/info.php?user_id=<?php echo $user_id; ?>" class="redirect-button">View Booking Details</a>
        <?php } ?>

        <!-- Show success or error messages -->
        <?php if (isset($error_message)) { echo "<p class='message error'>$error_message</p>"; } ?>

        <?php if (!$user_has_booking) { ?>
            <h1>Book Your Food Preference</h1>
            <form action="" method="POST">
                <label for="restaurant_id">Restaurant:</label>
                <select name="restaurant_id" id="restaurant_id" required>
                    <option value="">--Select Restaurant--</option>
                    <?php while ($row = $restaurants_result->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['restaurant_id']); ?>">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                    <?php } ?>
                </select>
                <br><br>

                <label for="preference">Preference:</label>
                <select name="preference" id="preference" required>
                    <option value="Vegan Only">Vegan Only</option>
                    <option value="Non-Vegan Only">Non-Vegan Only</option>
                    <option value="Vegan & Non-Vegan (Mix)">Vegan & Non-Vegan (Mix)</option>
                </select>
                <br><br>

                <label for="venue">Venue:</label>
                <select name="venue" id="venue" required>
                    <option value="Room Delivery">Room Delivery</option>
                    <option value="Walk-in">Walk-in</option>
                </select>
                <br><br>

                <button type="submit">Book Food</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
