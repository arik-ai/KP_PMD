<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
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
            width: 95%;
            padding: 10px;
            font-size: 16px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: rgb(15, 117, 241);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Forget Password</h1>
        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Konfigurasi database
                $host = 'localhost';
                $username = 'root';
                $password = '';
                $database = 'dpmd';

                // Membuat koneksi ke database
                $conn = new mysqli($host, $username, $password, $database);

                // Cek koneksi
                if ($conn->connect_error) {
                    die("Koneksi gagal: " . $conn->connect_error);
                }

                // Ambil username dari form
                $user = $_POST['username'];

                // Cek apakah username ada di database (case-sensitive)
                $sql = "SELECT * FROM users WHERE BINARY username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $user);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Redirect ke halaman ubah password dengan membawa username sebagai parameter
                    header("Location: reset_password.php?username=" . urlencode($user));
                    exit;
                } else {
                    echo "<p style='color: red; text-align: center;'>Username tidak ditemukan!</p>";
                }

                $stmt->close();
                $conn->close();
            }
            ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Masukkan Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <button type="submit" class="button">Submit</button>
        </form>
    </div>
</body>
</html>
