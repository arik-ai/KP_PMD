<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        <h1>Reset Password</h1>
        <?php
        if (isset($_GET['username'])) {
            $user = $_GET['username'];

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

                // Ambil password baru dari form
                $newPassword = $_POST['new_password'];

                // Update password di database
                $updateSql = "UPDATE users SET password = ? WHERE username = ?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("ss", $newPassword, $user);

                if ($stmt->execute()) {
                    // Menampilkan alert jika password berhasil diubah
                    echo "<script>alert('Password berhasil diubah!'); window.location.href = 'login.php';</script>";
                } else {
                    echo "<p class='message' style='color: red;'>Gagal mengubah password. Silakan coba lagi.</p>";
                }

                $stmt->close();
                $conn->close();
            }
        } else {
            echo "<p class='message' style='color: red;'>Akses tidak valid!</p>";
            exit;
        }
        ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="new_password">Masukkan Password Baru</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <button type="submit" class="button">Ubah Password</button>
        </form>
    </div>
</body>
</html>
