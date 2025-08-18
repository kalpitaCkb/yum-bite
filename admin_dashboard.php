<?php
session_start();

// Admin check
// if (!isset($_SESSION['user']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
//     header("Location: login.php");
//     exit();
// }

// CSRF token for form security
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YumBite | Admin Panel</title>
    <link rel="icon" href="images/logo1.png" type="image/png">
    <link id="theme-style" rel="stylesheet" href="css/style-dark.css">
    <link rel="stylesheet" href="css/signup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .admin-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-card {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .stat-box {
            background-color: #1b1b1b;
            border-radius: 8px;
            text-align: center;
            width: 30%;
            overflow: hidden;
        }

        .stat-box h4 {
            margin: 0;
            font-size: 1.2em;
            margin-top: 15px;
        }

        .stat-box p {
            margin: 5px 0;
            font-size: 1.5em;
            font-weight: bold;
        }

        .stat-box a {
            display: block;
            background-color: #444242ff;
            color: #fff;
            padding: 10px 0;
            text-decoration: none;
        }

        .stat-box a:hover {
            display: block;
            background-color: #3f3d3dff;
            color: #fff;
            padding: 10px 0;
        }

    </style>
</head>

<body>
    <?php include "navbar.php"; ?>

    <?php
    require_once "database.php";

    // Fetch totals
    $order_sql = "SELECT COUNT(*) as total_orders FROM orders";
    $order_res = mysqli_query($conn, $order_sql);
    $total_orders = mysqli_fetch_assoc($order_res)['total_orders'] ?? 0;

    $food_sql = "SELECT COUNT(*) as total_food FROM food";
    $food_res = mysqli_query($conn, $food_sql);
    $total_food = mysqli_fetch_assoc($food_res)['total_food'] ?? 0;

    $user_sql = "SELECT COUNT(*) as total_users FROM users";
    $user_res = mysqli_query($conn, $user_sql);
    $total_users = mysqli_fetch_assoc($user_res)['total_users'] ?? 0;

    ?>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        <div class="stats-card">
            <div class="stat-box">
                <h4>Total Orders</h4>
                <p><?php echo $total_orders; ?></p>
                <a href="admin_orders.php">More Info →</a>
            </div>
            <div class="stat-box">
                <h4>Total Food Items</h4>
                <p><?php echo $total_food; ?></p>
                <a href="admin_menu.php">More Info →</a>
            </div>
            <div class="stat-box">
                <h4>Total Users</h4>
                <p><?php echo $total_users; ?></p>
                <a>More Info →</a>
            </div>
        </div>
    </div>
    
    <script>
        function switchTheme(path) {
            document.getElementById("theme-style").setAttribute("href", path);
            localStorage.setItem("theme", path);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const hamburger = document.getElementById("hamburger");
            const navLinks = document.getElementById("navLinks");

            const savedTheme = localStorage.getItem("theme");
            if (savedTheme) {
                switchTheme(savedTheme);
            }

            hamburger.addEventListener("click", function () {
                hamburger.classList.toggle("active");
                navLinks.classList.toggle("show");
            });

            document.querySelectorAll('.nav-links a').forEach(link => {
                link.addEventListener('click', () => {
                    hamburger.classList.remove('active');
                    navLinks.classList.remove('show');
                });
            });
        });
    </script>
</body>

</html>