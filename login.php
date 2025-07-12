<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM login WHERE username='$username' AND password='$password'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['log'] = true;

        if ($user['role'] == 'admin') {
            header("Location: index.php");
        } elseif ($user['role'] == 'gudang') {
            header("Location: barangmasuk.php");
        }
        exit();
    } else {
        echo "<script>alert('Login gagal. Periksa username dan password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sinar Jaya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #c4c4c4;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: #e6e6e6;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            width: 450px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 30px;
            font-weight: bold;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            font-size: 12px;
            margin-bottom: 5px;
            display: block;
            color: #000;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-bottom: 1px solid #333;
            background: transparent;
            outline: none;
            color: #000;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            width: 40%;
            padding: 10px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>SINAR JAYA</h2>
        <form method="post">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" name="login">LOGIN</button>
        </form>
    </div>
</body>
</html>
