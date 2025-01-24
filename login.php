<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-section {
            width: 35%;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .login-section h1 {
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
        .login-button {
            width: 15%;
            padding: 10px;
            font-size: 16px;
            background-color: rgb(15, 117, 241);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .image-section {
            width: 65%;
            background-image: url('pmk.png'); 
            background-size: cover;
            background-position: center;
        }
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo img {
            height: 300px;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-section">
            <div class="logo">
                <img src="logo.png" alt="Logo"> 
            </div>
            <h1>Login</h1>
            <?php
                session_start();
                include 'db.php'; // Ensure this file connects to your database

                // Check if the form is submitted
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $username = mysqli_real_escape_string($conn, $_POST['username']);
                    $password = mysqli_real_escape_string($conn, $_POST['password']);
                    
                    // Query to check if user exists
                    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
                    $result = mysqli_query($conn, $query);

                    // If user exists, set session variables
                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['role'] = $row['role']; // assuming the 'role' field exists

                        // Redirect to main page/dashboard
                        header("Location: index.php");
                        exit;
                    } else {
                        echo "Invalid username or password.";
                    }
                }
                ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="forgot-password-link">
                    <p><a href="forget.php" style="text-decoration: none; color: #007bff;">Forget Password?</a></p>
                </div>

                <button type="submit" class="login-button">Login</button>
            </form>
            <br>
            <p><a href="register.php" style="text-decoration: none; color: #007bff;">Belum mempunyai Akun? Registrasi Sekarang</a></p>
        </div>
        <div class="image-section"></div>
    </div>
</body>
</html>
