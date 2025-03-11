<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Information</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-image: url('images/bg_3.jpg');
      background-size: cover;
      background-position: center bottom -100px;
      background-repeat: no-repeat;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-size: 16px;
      position: relative;
    }

    .form-container {
      width: 700px; /* Increased width */
      max-width: 90%; /* Allow scaling on smaller screens */
      background-color: rgba(255, 255, 255, 0.9);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    header {
      font-size: 2.2rem; /* Increased font size */
      font-weight: bold;
      color: #000000;
      width: 100%;
      text-align: center;
      margin-bottom: 30px; /* Increased margin */
    }

    .button-container {
      display: flex;
      justify-content: space-between;
      width: 100%;
      margin-bottom: 30px;
    }

    .button-container button {
      background-color: #28a745;
      color: white;
      padding: 15px 30px; /* Increased padding */
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1.1rem; /* Increased font size */
      font-weight: bold;
      transition: background-color 0.3s ease;
      width: 22%; /* Adjusted width for a balanced layout */
    }

    .button-container button:hover {
      background-color: #218838;
    }

    .info-container {
      display: none; /* Hide the sections by default */
      margin-top: 20px;
      text-align: left;
      width: 100%;
    }

    .info-container.active {
      display: block; /* Show the section when it has the 'active' class */
    }

    .info-container p {
      font-size: 1.2rem; /* Increased font size */
      color: #333;
      margin-bottom: 15px; /* Increased margin for better spacing */
    }

    .redirect-button, .edit-button {
      display: inline-block;
      background-color: #28a745;
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-size: 1.1rem; /* Increased font size */
      font-weight: bold;
      margin-top: 20px; /* Increased margin */
    }

    .redirect-button:hover, .edit-button:hover {
      background-color: #218838;
    }

    .logout-button {
            position: absolute;
            top: 20px; /* Positioned at the top right corner */
            right: 20px;
            background-color: #ff5733; /* Orange-red background color */
            color: white;
            padding: 12px 24px; /* Adjusted padding for the button */
            border: none;
            border-radius: 8px; /* Rounded corners */
            cursor: pointer;
            text-decoration: none;
            font-size: 1.1rem; /* Slightly larger font */
            font-weight: bold;
            transition: background-color 0.3s ease; /* Smooth color transition on hover */
            }
        .logout-button:hover {
            background-color: #c0392b; /* Darker red on hover */
        }

  </style>
</head>
<body>
  <a href="http://localhost/hajj/index.php" class="logout-button">Logout</a>
  <div class="form-container">
    
    <header>Retrieve User Information</header>

    <div class="button-container">
      <button onclick="showInfo('personal')">Personal Info</button>
      <button onclick="showInfo('travel')">Travel Info</button>
      <button onclick="showInfo('room')">Room Info</button>
      <button onclick="showInfo('food')">Food Info</button>
    </div>

    <div id="personal" class="info-container">
      <?php
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        if ($user_id) {
          $conn = new mysqli("localhost", "root", "", "hajj_database");

          if ($conn->connect_error) {
            die("<p class='message error'>Connection failed: " . $conn->connect_error . "</p>");
          }

          $stmt = $conn->prepare("SELECT * FROM userinfo WHERE user_id = ?");
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<p><strong>User ID:</strong> " . htmlspecialchars($user_id) . "</p>";
              echo "<p><strong>First Name:</strong> " . htmlspecialchars($row["first_name"]) . "</p>";
              echo "<p><strong>Last Name:</strong> " . htmlspecialchars($row["last_name"]) . "</p>";
              echo "<p><strong>Date of Birth:</strong> " . htmlspecialchars($row["date_of_birth"]) . "</p>";
              echo "<p><strong>Country:</strong> " . htmlspecialchars($row["country"]) . "</p>";
              echo "<a href='http://localhost/hajj/input.php?user_id=" . $user_id . "&edit=1' class='edit-button'>Edit Details</a>";
            }
          }
          $stmt->close();
          $conn->close();
        }
      ?>
    </div>

    <div id="travel" class="info-container">
      <?php
        if ($user_id) {
          $conn = new mysqli("localhost", "root", "", "hajj_database");
          $stmt = $conn->prepare("SELECT * FROM travel_booking t LEFT JOIN airline_seats a ON t.seat_id = a.seat_id WHERE t.user_id = ?");
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<p><strong>Seat ID:</strong> " . htmlspecialchars($row["seat_id"]) . "</p>";
              echo "<p><strong>Airline:</strong> " . htmlspecialchars($row["airline"]) . "</p>";
              echo "<p><strong>Seat #:</strong> " . htmlspecialchars($row["seat_no"]) . "</p>";
              echo "<p><strong>Destination:</strong> " . htmlspecialchars($row["destination"]) . "</p>";
              echo "<a href='http://localhost/hajj/airline.php?user_id=" . $user_id . "&edit=1' class='edit-button'>Edit Details</a>";
            }
          } else {
            echo "<p>No travel booking made.</p>";
            echo "<a href='http://localhost/hajj/airline.php?user_id=" . $user_id . "' class='redirect-button'>Book a Seat</a>";
          }

          $stmt->close();
          $conn->close();
        }
      ?>
    </div>

    <div id="room" class="info-container">
      <?php
        if ($user_id) {
          $conn = new mysqli("localhost", "root", "", "hajj_database");
          $stmt = $conn->prepare("SELECT * FROM room_booking r LEFT JOIN hotel_details h ON r.room_id = h.room_id WHERE r.user_id = ?");
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<p><strong>Room ID:</strong> " . htmlspecialchars($row["room_id"]) . "</p>";
              echo "<p><strong>Hotel:</strong> " . htmlspecialchars($row["hotel"]) . "</p>";
              echo "<p><strong>Room #:</strong> " . htmlspecialchars($row["room_no"]) . "</p>";
              echo "<p><strong>Days:</strong> " . htmlspecialchars($row["stay_days"]) . "</p>";
              echo "<a href='http://localhost/hajj/room.php?user_id=" . $user_id . "&edit=1' class='edit-button'>Edit Details</a>";
            }
          } else {
            echo "<p>No room booking made.</p>";
            echo "<a href='http://localhost/hajj/room.php?user_id=" . $user_id . "' class='redirect-button'>Book a Room</a>";
          }

          $stmt->close();
          $conn->close();
        }
      ?>
    </div>

    <div id="food" class="info-container">
      <?php
        if ($user_id) {
          $conn = new mysqli("localhost", "root", "", "hajj_database");
          $stmt = $conn->prepare("SELECT * FROM food_booking f LEFT JOIN restaurant_details d ON f.restaurant_id = d.restaurant_id WHERE f.user_id = ?");
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<p><strong>Restaurant ID:</strong> " . htmlspecialchars($row["restaurant_id"]) . "</p>";
              echo "<p><strong>Restaurant:</strong> " . htmlspecialchars($row["name"]) . "</p>";
              echo "<p><strong>Location:</strong> " . htmlspecialchars($row["location"]) . "</p>";
              echo "<p><strong>Contact:</strong> " . htmlspecialchars($row["contact"]) . "</p>";
              echo "<p><strong>Preference:</strong> " . htmlspecialchars($row["preference"]) . "</p>";
              echo "<p><strong>Method:</strong> " . htmlspecialchars($row["venue"]) . "</p>";
              echo "<a href='http://localhost/hajj/food.php?user_id=" . $user_id . "&edit=1' class='edit-button'>Edit Details</a>";
            }
          } else {
            echo "<p>No restaurant booking made.</p>";
            echo "<a href='http://localhost/hajj/food.php?user_id=" . $user_id . "' class='redirect-button'>Book a Restaurant</a>";
          }

          $stmt->close();
          $conn->close();
        }
      ?>
    </div>
  </div>

  <script>
    function showInfo(section) {
      const sections = document.querySelectorAll('.info-container');
      sections.forEach(function(section) {
        section.classList.remove('active');
      });
      document.getElementById(section).classList.add('active');
    }
  </script>
</body>
</html>
