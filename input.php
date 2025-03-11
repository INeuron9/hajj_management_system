<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hajj Travel Agency - Additional Information</title>
  <style>
    /* Global Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-image: url('images/bg_4.jpg'); /* Set the background image */
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

    input[type="text"], input[type="date"] {
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      width: 100%;
      font-size: 1rem;
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
  </style>
</head>
<body>
  <div class="form-container">
    <header>Hajj Travel Agency</header>
    <h1>Complete Your Profile</h1>
    <p>Please provide additional information to complete your registration.</p>

    <?php
      // Database connection
      $servername = "localhost";
      $dbusername = "root";
      $dbpassword = "";
      $dbname = "hajj_database";

      $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
      if ($conn->connect_error) {
        $message = "<p class='message error'>Connection failed: " . $conn->connect_error . "</p>";
      }
      $message = '';

      // Retrieve user_id from the URL
      if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];  // Get user_id from URL
      } else {
        die("Error: User ID not provided.");
      }
      if (isset($_GET['edit']) ) {

        $delete_query = "DELETE FROM userinfo WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    

      // Check if user_id exists in the database
      if ($user_id) {
        // First, check if the user_id exists in the users table
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows == 0) {
            // If user_id does not exist in users table
            $message = "<p class='message error'>The user does not exist.</p>";
        } else {
            // Check if the user_id exists in the userinfo table
            $sql = "SELECT * FROM userinfo WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                // If user_id exists in both users and userinfo table
                $message = "<p class='message error'>Information already exists for this user.</p>";
            } else {
                // If user_id exists in users table but not in userinfo table, show the form
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Get form inputs
                    $first_name = $_POST["first_name"];
                    $last_name = $_POST["last_name"];
                    $date_of_birth = $_POST["date_of_birth"];
                    $country = $_POST["country"];
    
                    // Prepare SQL statement to insert new userinfo
                    $stmt = $conn->prepare("INSERT INTO userinfo (user_id, first_name, last_name, date_of_birth, country) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("issss", $user_id, $first_name, $last_name, $date_of_birth, $country);
                    $stmt->execute();
    
                    if ($stmt->affected_rows > 0) {
                        // Redirect to info.php with user_id as query parameter
                        header("Location: http://localhost/hajj/info.php?user_id=$user_id");
                        exit(); // Make sure the script stops executing after the redirect
                    } else {
                        $message = "<p class='message error'>Failed to create profile. Please try again.</p>";
                    }
    
                    $stmt->close();
                }
            }
        }
        $stmt->close();
    }

      // Close connection
      $conn->close();

      if ($message) {
        echo $message;
      }
    ?>

    <!-- Form to input user data if user_id does not exist -->
    <?php if (empty($message)): ?>
    <form method="POST">
      <label for="first_name">First Name:</label>
      <input type="text" id="first_name" name="first_name" required>

      <label for="last_name">Last Name:</label>
      <input type="text" id="last_name" name="last_name" required>

      <label for="date_of_birth">Date of Birth:</label>
      <input type="date" id="date_of_birth" name="date_of_birth" required>

      <label for="country">Country:</label>
      <input type="text" id="country" name="country" required>

      <button type="submit">Submit</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>
