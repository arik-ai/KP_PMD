<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .forgot-password-section {
            width: 35%;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .forgot-password-section h1 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-size: 14px;
            color: #666;
        }
        .form-group input {
            width: 90%;
            padding: 10px;
            font-size: 16px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .submit-button {
            width: 15%;
            padding: 10px;
            font-size: 16px;
            background-color: rgb(15, 117, 241);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-password-section">
            <h1>Forgot Password</h1>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $host = 'localhost';
                $username = 'root';
                $password = '';
                $database = 'dpmd';

                // Create connection
                $conn = new mysqli($host, $username, $password, $database);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Get the username
                $user_input = $_POST['username'];

                // Query to find user by username (no email column)
                $sql = "SELECT * FROM users WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $user_input); // Bind only the username
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // User found
                    $row = $result->fetch_assoc();

                    // Generate reset password link (you can implement a real password reset mechanism here)
                    $reset_link = "reset_password.php" . md5($row['username'] . time());

                    // Simulate sending the reset link (since there's no email column in your table)
                    // Here you can manually redirect the user to the reset page, or simulate sending an email.
                    echo "<p style='color: green; text-align: center;'>A password reset link has been generated. Please check your email (if available).</p>";

                    // In this case, you may also display the reset link as a placeholder for testing:
                    echo "<p style='color: green; text-align: center;'>Reset Link: <a href='$reset_link'>$reset_link</a></p>";
                } else {
                    echo "<p style='color: red; text-align: center;'>Username not found!</p>";
                }

                $stmt->close();
                $conn->close();
            }
            ?>


            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Enter Username or Email</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <button type="submit" class="submit-button">Submit</button>
            </form>
            <div class="back-link">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
