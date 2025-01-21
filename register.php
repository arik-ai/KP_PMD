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

    // Ambil data dari form
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'operator';

    // Cek apakah username sudah ada
    $sqlCheck = "SELECT * FROM users WHERE username = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $user);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        echo "<script>alert('Username sudah terdaftar!');</script>";
    } else {
        // Masukkan data ke database
        $sqlInsert = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("sss", $user, $pass, $role);

        if ($stmtInsert->execute()) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan, silakan coba lagi.');</script>";
        }

        $stmtInsert->close();
    }

    $stmtCheck->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .register-form {
            width: 40%;
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .register-form h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-size: 14px;
            color: #666;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .register-button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: rgb(15, 117, 241);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .register-button:hover {
            background-color: #0d76e5;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-form">
            <h1>Register</h1>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Sebagai?</label>
                    <select name="role" id="role">
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="register-button">Register</button>
            </form>
            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php" style="color: #007bff; text-decoration: none;">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
