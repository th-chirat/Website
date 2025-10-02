<?php
session_start();

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "review"; // เปลี่ยนตามชื่อผู้ใช้ MySQL ของคุณ
$password = "123456"; // เปลี่ยนตามรหัสผ่าน MySQL ของคุณ
$dbname = "review";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าผู้ใช้ส่งแบบฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = md5($_POST['password']); // แฮชรหัสผ่านด้วย MD5

    // ตรวจสอบผู้ใช้จากฐานข้อมูล
    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // หากชื่อผู้ใช้และรหัสผ่านถูกต้อง
        $_SESSION['username'] = $user;
        header("Location: index.php"); // เปลี่ยนเส้นทางไปยังหน้าแรก
        exit();
    } else {
        // หากไม่ถูกต้อง
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Review Sphere</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .login-container input[type="text"], .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container input[type="submit"]:hover {
            background-color: #555;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($error)) { echo '<p class="error">'.$error.'</p>'; } ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Sign In">
    </form>
</div>

</body>
</html>
