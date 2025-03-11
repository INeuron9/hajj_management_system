<?php
// Initialize message variable
$message = '';

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "hajj_database";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    $message = "<p class='message error'>Connection failed: " . $conn->connect_error . "</p>";
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST["username"];
    $password = $_POST["password"];
    $action = $_POST["action"];
    if ($user_name == "admin"){
      header("Location: /hajj/admin_login.php");
      exit; // Don't forget to exit after redirect
    }
    if ($action == "signin") {
        // Sign In logic
        $hash_password=hash('sha256',$password);
        $sql = "SELECT * FROM users WHERE user_name = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_name, $hash_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row["user_id"];
            // Redirect before any HTML output
            header("Location: /hajj/info.php?user_id=" . urlencode($user_id));
            exit; // Don't forget to exit after redirect
        } else {
            $message = "<p class='message error'>Incorrect username or password. Please try again.</p>";
        }

        $stmt->close();
    }

    elseif ($action == "signup") {
        // Signup logic
        // First, check if the username already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $stmt->store_result(); // Store the result to check if the user already exists

        if ($stmt->num_rows > 0) {
            // User already exists
            $message = "<p class='message error'>Username already taken. Please choose another one.</p>";
        } else {
            // Proceed with signup if username doesn't exist
            $stmt->close(); // Close the previous SELECT statement

            try {
                $hash_password=hash('sha256',$password);
                // Attempt to insert the user
                $stmt = $conn->prepare("INSERT INTO users (user_name, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $user_name, $hash_password);
                $stmt->execute();

                // Check if the insertion was successful
                if ($stmt->affected_rows > 0) {
                    $user_id = $stmt->insert_id;  // Get the last inserted user_id
                    header("Location: /hajj/input.php?user_id=" . $user_id);
                    exit; // Don't forget to exit after redirect
                } else {
                    $message = "<p class='message error'>Error creating account. Please try again.</p>";
                }
            } catch (mysqli_sql_exception $e) {
                // Handle the unique constraint violation error
                if ($e->getCode() == 1062) {
                    // 1062 is the error code for duplicate entry (unique constraint violation)
                    $message = "<p class='message error'>Username already taken. Please choose another one.</p>";
                } else {
                    // General error
                    $message = "<p class='message error'>An error occurred. Please try again.</p>";
                }
            }

            $stmt->close(); // Always close the statement after usage
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hajj Travel Agency - Sign In/Sign Up</title>
  <style>
    /* Global Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-image: url('images/background.jpg'); /* Set the background image */
      background-size: cover; /* Make sure the image covers the entire page */
      background-position: center bottom -50px; /* Center the image */
      background-repeat: no-repeat; /* Prevent the image from repeating */
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-size: 16px;
      position: relative; /* Enable positioning for child elements */
    }

    /* Admin Login Button */
    .top-right {
      position: absolute;
      top: 10px;
      right: 10px;
    }

    .top-right button {
      padding: 10px 20px;
      font-size: 0.9rem;
      font-weight: bold;
      color: white;
      background-color: #0066cc;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .top-right button:hover {
      background-color: #004d99; /* Darker blue on hover */
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

    input[type="text"], input[type="password"] {
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

    .toggle-link {
      color: #ff5733; /* Orange-red */
      margin-top: 20px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .toggle-link:hover {
      color: #c0392b; /* Darker red on hover */
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
  <!-- Top-right Button -->
  <div class="top-right">
    <button onclick="window.location.href='http://localhost/hajj/admin_login.php'">Admin Login</button>
  </div>

  <!-- Admin Login Form -->
  <div class="form-container">
    <header>Hajj Travel Agency</header>
    
    <h1>Welcome to Hajj Travel</h1>
    <p>Join us for a memorable and spiritual journey to Mecca. Sign up to start your journey or sign in to manage your bookings.</p>

    <h2>Sign In</h2>
    <form id="authForm" method="POST">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
      
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
      
      <button type="submit" name="action" value="signin">Sign In</button>
    </form>
    
    <p class="toggle-link" onclick="toggleForm()">Don’t have an account? Sign up</p>

    <?php
      // Display message if exists
      if ($message) {
        echo $message;
      }
    ?>
  </div>
<script>
    const authForm = document.getElementById('authForm');
    const formContainer = document.querySelector('.form-container');
    const toggleLink = document.querySelector('.toggle-link');

    function toggleForm() {
      if (formContainer.querySelector('h2').innerText === 'Sign In') {
        formContainer.querySelector('h2').innerText = 'Sign Up';
        toggleLink.innerText = 'Already have an account? Sign in';
        authForm.innerHTML = 
          `<label for="username">Username:</label>
          <input type="text" id="username" name="username" required>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
          <button type="submit" name="action" value="signup">Sign Up</button>`;
      } else {
        formContainer.querySelector('h2').innerText = 'Sign In';
        toggleLink.innerText = 'Don’t have an account? Sign up';
        authForm.innerHTML = 
          `<label for="username">Username:</label>
          <input type="text" id="username" name="username" required>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
          <button type="submit" name="action" value="signin">Sign In</button>`;
      }
    }
</script>
</body>
</html>
