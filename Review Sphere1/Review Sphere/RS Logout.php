<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // ถ้าไม่ได้เข้าสู่ระบบ ให้ไปที่หน้าเข้าสู่ระบบ
    exit();
}

// ออกจากระบบ
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Sphere</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('your-image-url-here'); /* ใส่ URL รูปพื้นหลังของคุณตรงนี้ */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh; /* ตั้งให้สูงเต็มหน้าจอ */
        }

        .navbar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.7);
            position: relative;
        }

        .menu-icon {
            font-size: 30px;
            color: white;
            cursor: pointer;
        }

        .menu {
            position: absolute;
            top: 50px;
            left: 20px;
            background-color: white;
            display: none;
            flex-direction: column;
            border-radius: 10px;
        }

        .menu a {
            text-decoration: none;
            padding: 10px;
            color: black;
            border-bottom: 1px solid #ccc;
        }

        .menu a:last-child {
            border-bottom: none;
        }

        .menu a:hover {
            background-color: #ddd;
        }

        .search-bar {
            margin-left: 20px;
            background-color: white;
            border-radius: 25px;
            display: flex;
            align-items: center;
            padding: 5px 10px;
        }

        .search-bar input {
            border: none;
            padding: 10px;
            border-radius: 25px;
            outline: none;
        }

        .logout {
            margin-left: auto;
            background-color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            color: black;
        }

        .content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            height: calc(100vh - 60px);
            padding-left: 20px;
            color: white;
        }

        .content h1 {
            font-size: 60px;
            margin: 0;
            color: red;
        }

        .content p {
            font-size: 20px;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <!-- Menu Icon -->
    <div class="menu-icon">&#9776;</div>
    
    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" placeholder="Search...">
    </div>

    <!-- Logout Button -->
    <a href="?logout=true" class="logout">Logout</a>
</div>

<div class="content">
    <h1>REVIEW SPHERE</h1>
    <p>NAVBAR</p>
    <p>REVIEW</p>
</div>

</body>
</html>
