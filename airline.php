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

    $delete_query = "DELETE FROM travel_booking WHERE user_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    $update_query = "UPDATE airline_seats SET status = 0 WHERE status = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $user_id);
    $update_stmt->execute();
}
// Check if the user already has a booking
$check_booking_query = "SELECT * FROM travel_booking WHERE user_id = ?";
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

// Handle AJAX requests for fetching seat numbers
if (isset($_POST['action']) && $_POST['action'] === 'fetch_seats' && isset($_POST['airline'])) {
    $airline = $_POST['airline'];

    // Fetch available seats for the selected airline
    $seats_query = "SELECT seat_id, seat_no FROM airline_seats WHERE airline = ? AND status = 0";
    $stmt = $conn->prepare($seats_query);
    $stmt->bind_param("s", $airline);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate options for the seat dropdown
    $options = '<option value="">--Select Seat--</option>';
    while ($row = $result->fetch_assoc()) {
        $options .= '<option value="' . $row['seat_id'] . '">' . htmlspecialchars($row['seat_no']) . '</option>';
    }
    echo $options;
    exit; // End script execution for AJAX request
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action']) && !$user_has_booking) {
    $airline = $_POST["airline"];
    $seat_id = $_POST["seat_id"];
    $destination = $_POST["destination"];

    // Insert into travel_booking table
    
    $insert_query = "INSERT INTO travel_booking (user_id, seat_id, destination) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $user_id, $seat_id, $destination);

    try {
        if ($stmt->execute()) {
            // Update seat status in airline_seats table
            $update_query = "UPDATE airline_seats SET status = ? WHERE seat_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ii", $user_id, $seat_id);
            $update_stmt->execute();

            $user_has_booking = true;
        } else {
            throw new Exception("Booking failed. Please try again.");
        }
    } catch (mysqli_sql_exception $e) {
        // Handle specific database errors
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            $error_message = "Error: The user ID provided does not exist in the users table.";
        } else {
            $error_message = "Database error: " . $e->getMessage();
        }
    } catch (Exception $e) {
        // Handle general errors
        $error_message = $e->getMessage();
    }
}

// Fetch airlines
$airlines_query = "SELECT DISTINCT airline FROM airline_seats WHERE status = 0";
$airlines_result = $conn->query($airlines_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Booking</title>
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
        <header>Seat Booking</header>

        <!-- Show error message only after form submission if the user has a booking -->
        <?php if ($user_has_booking) { ?>
            <p class='message error'>You have successfully booked a seat.</p>
            <a href="http://localhost/hajj/info.php?user_id=<?php echo $user_id; ?>" class="redirect-button">View Booking Details</a>
        <?php } ?>

        <!-- Show success or error messages -->
        <?php if (isset($error_message)) { echo "<p class='message error'>$error_message</p>"; } ?>

        <?php if (!$user_has_booking) { ?>
            <h1>Book Your Seat</h1>
            <form action="" method="POST">
                <label for="airline">Airline:</label>
                <select name="airline" id="airline" required>
                    <option value="">--Select Airline--</option>
                    <?php while ($row = $airlines_result->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['airline']); ?>">
                            <?php echo htmlspecialchars($row['airline']); ?>
                        </option>
                    <?php } ?>
                </select>
                <br><br>

                <label for="seat_id">Seat:</label>
                <select name="seat_id" id="seat_id" required>
                    <option value="">--Select Seat--</option>
                </select>
                <br><br>

                <label for="destination">Destination:</label>
                <input type="text" name="destination" id="destination" required>
                <br><br>

                <button type="submit">Book Seat</button>
            </form>
        <?php } ?>
    </div>

    <script>
        $(document).ready(function () {
            $('#airline').change(function () {
                var airline = $(this).val();
                if (airline) {
                    $.ajax({
                        url: '', // Same page
                        method: 'POST',
                        data: { action: 'fetch_seats', airline: airline },
                        success: function (data) {
                            $('#seat_id').html(data); // Populate seat dropdown
                        }
                    });
                } else {
                    $('#seat_id').html('<option value="">--Select Seat--</option>');
                }
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
