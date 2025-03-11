<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Hajj Travel Agency</title>
  <style>
    /* Global Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-image: url('images/background.jpg'); /* Background image */
      background-size: cover;
      background-position: center bottom -50px;
      background-repeat: no-repeat;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
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

    /* Container for the form */
    .form-container {
      background-color: rgba(255, 255, 255, 0.90); /* Semi-transparent white background */
      border-radius: 12px;
      padding: 30px;
      max-width: 400px;
      width: 100%;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    header {
      font-size: 1.8rem;
      font-weight: bold;
      color: #003366; /* Dark blue */
      margin-bottom: 20px;
    }

    h1 {
      color: #003366;
      font-size: 1.4rem;
      margin-bottom: 15px;
    }

    label {
      font-size: 1rem;
      text-align: left;
      margin-bottom: 0.5rem;
      display: block;
      color: #003366;
    }

    input[type="text"], input[type="password"] {
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      width: 100%;
      font-size: 1rem;
    }

    button[type="submit"] {
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

    button[type="submit"]:hover {
      background-color: #004d99; /* Darker blue on hover */
    }

    .message {
      font-size: 0.9rem;
      margin-top: 15px;
    }

    .message.error {
      color: #dc3545; /* Red for errors */
    }

    .message.success {
      color: #28a745; /* Green for success */
    }

    /* Responsive Design */
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
    <button onclick="window.location.href='http://localhost/hajj/index.php'">User Login</button>
  </div>
  <!-- Admin Login Form -->
  <div class="form-container">
    <header>Admin Login</header>
    <h1>Welcome Back</h1>
    <p>Please sign in to manage the Hajj travel bookings.</p>

    <form id="authForm" method="POST">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <button type="submit" name="action" value="signin">Sign In</button>
    </form>

    <?php
      // Initialize message variable
      $message = '';

      $servername = "localhost";
      $dbusername = "root";
      $dbpassword = "";
      $dbname = "hajj_database";

      // Create connection
      $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

      if ($conn->connect_error) {
        $message = "<p class='message error'>Connection failed: " . $conn->connect_error . "</p>";
      }

      // Handle login submissions
      if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "signin") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $hash_password = hash('sha256',$password);

        // Verify admin login
        $sql = "SELECT * FROM users WHERE user_name = ? AND password = ? AND user_id = 0" ;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hash_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          header("Location: /hajj/admin_dashboard.php");
          exit;
        } else {
          $message = "<p class='message error'>Invalid credentials. Please try again.</p>";
        }

        $stmt->close();
      }

      $conn->close();

      // Display error message if exists
      if ($message) {
        echo $message;
      }
    ?>
  </div>
</body>
</html>
