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
            background-image: url('images/background.jpg'); /* Set the background image */
            background-size: cover; /* Make sure the image covers the entire page */
            background-position: center bottom -50px; /* Center the image */
            background-repeat: no-repeat; /* Prevent the image from repeating */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container for the buttons */
        .button-container {
            background-color: rgba(255, 255, 255, 0.80); /* Semi-transparent white background */
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #003366;
            font-size: 1.5rem;
            margin-bottom: 15px;
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
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #004d99; /* Darker blue on hover */
        }

    </style>
</head>
<body>
    <a href="http://localhost/hajj/admin_login.php" class="logout-button">Logout</a>
    <div class="button-container">
        <h1>Admin Dashboard</h1>
        <button onclick="location.href='http://localhost/hajj/records.php'">View Records</button>
        <button onclick="location.href='http://localhost/hajj/modify.php'">Make Changes</button>
    </div>
</body>
</html>
