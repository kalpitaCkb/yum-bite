<?php
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/signup.css">
    <style>
        .logout:hover {
            color: #ffa500;
        }

        .admin-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .admin-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: white;
        }

        .stats-card {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .stat-box {
            background-color: #1b1b1b;
            border-radius: 5px;
            text-align: center;
            width: 30%;
            overflow: hidden;
            color: white;
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
            cursor: pointer;
        }

        .stat-box a:hover {
            display: block;
            background-color: #3f3d3dff;
            color: #fff;
            padding: 10px 0;
        }

        .form {
            width: 100%;
            background-color: #1f1f1f;
            padding: 40px;
            border-radius: 5px;
            box-sizing: border-box;
            margin-top: 40px;
        }

        form fieldset {
            border: 1px solid white;
            margin: 5%;
            padding: 3%;
            border-radius: 5px;
        }

        form .label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
            color: white;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="email"],
        form input[type="password"],
        form input[type="file"],
        form select,
        form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #fc8906;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form textarea {
            min-height: 100px;
        }

        form input[type="file"] {
            background-color: #fff;
            color: #1b1b1b;
        }

        form input[type="submit"] {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background-color: #fa913b;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #e6ae5a;
        }

        .btn {
            background-color: #ff6f61;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #e55a50;
        }

        .error,
        .success {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        /* for smaller devices */

        @media screen and (max-width: 768px) {
            .admin-container {
                /* margin: 50px auto; */
                padding: 20px 30px;
            }

            .admin-container h1 {
                margin-bottom: 20px;
            }

            .stats-card {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
            }

            .stat-box h4 {
                font-size: 1em;
            }

            .stat-box p {
                font-size: 1.3em;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <img src="images/logo1.png" alt="Logo1" class="logo1" />
                <img src="images/logo.png" alt="YumBite" />
            </div>

            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <ul class="nav-links" id="navLinks">
                <li><a href="admin.php">Home</a></li>
                <li><a href="admin_menu.php">Menu</a></li>
                <li><a href="admin_orders.php">Orders</a></li>
                <li><a href="user_reviews.php">Reviews</a></li>
                <a href="logout.php" class="login-btn logout">Logout →</a>
            </ul>
        </nav>
    </header>

    <?php
    require_once "database.php";

    // Fetch totals
    $order_sql = "SELECT COUNT(*) as total_orders FROM orders";
    $order_res = mysqli_query($conn, $order_sql);
    $total_orders = mysqli_fetch_assoc($order_res)['total_orders'] ?? 0;

    $food_sql = "SELECT COUNT(*) as total_food FROM food";
    $food_res = mysqli_query($conn, $food_sql);
    $total_food = mysqli_fetch_assoc($food_res)['total_food'] ?? 0;

    $contact_sql = "SELECT COUNT(*) as total_reviews FROM contact";
    $contact_res = mysqli_query($conn, $contact_sql);
    $total_reviews = mysqli_fetch_assoc($contact_res)['total_reviews'] ?? 0;

    // Fetch categories, cuisines, and restaurants for select inputs
    $category_sql = "SELECT id, title FROM categories";
    $category_res = mysqli_query($conn, $category_sql);
    $categories = mysqli_fetch_all($category_res, MYSQLI_ASSOC);

    $cuisine_sql = "SELECT id, name FROM cuisines";
    $cuisine_res = mysqli_query($conn, $cuisine_sql);
    $cuisines = mysqli_fetch_all($cuisine_res, MYSQLI_ASSOC);

    $restaurant_sql = "SELECT id, name FROM restaurants";
    $restaurant_res = mysqli_query($conn, $restaurant_sql);
    $restaurants = mysqli_fetch_all($restaurant_res, MYSQLI_ASSOC);
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
                <h4>Total Reviews</h4>
                <p><?php echo $total_reviews; ?></p>
                <a href="user_reviews.php">More Info →</a>
            </div>
        </div>

        <?php
        if (isset($_GET['success'])) {
            echo "<div class='success'>" . htmlspecialchars($_GET['success']) . "</div>";
        }
        if (isset($_GET['error'])) {
            echo "<div class='error'>" . htmlspecialchars($_GET['error']) . "</div>";
        }
        ?>

        <form action="add_food.php" method="POST" enctype="multipart/form-data" class="form" id="profileForm">
            <fieldset>
                <legend>Add New Food Item</legend>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- <div class="form-group">
                    <label for="foodname">Food Name</label>
                    <input type="text" name="foodname" id="foodname" required>
                </div> -->

                <p class="label">Food Name</p>
                <input type="text" name="foodname" id="foodname" required>

                <!-- <div class="form-group">
                    <label for="price">Price (BDT)</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" required>
                </div> -->

                <p class="label">Price (BDT)</p>
                <input type="number" name="price" id="price" step="0.01" min="0" required>

                <!-- <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" required></textarea>
                </div> -->

                <p class="label">Description</p>
                <textarea name="description" id="description" required></textarea>

                <p class="label">Description</p>
                <select name="category" id="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id']); ?>">
                            <?php echo htmlspecialchars($cat['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- <div class="form-group">
                    <label for="restaurant">Restaurant</label>
                </div> -->

                <p class="label">Restaurant</p>
                <select name="restaurant" id="restaurant" required>
                    <option value="">Select Restaurant</option>
                    <?php foreach ($restaurants as $rest): ?>
                        <option value="<?php echo htmlspecialchars($rest['id']); ?>">
                            <?php echo htmlspecialchars($rest['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- <div class="form-group">
                    <label for="cuisine">Cuisine</label>
                </div> -->

                <p class="label">Restaurant</p>
                <select name="cuisine" id="cuisine" required>
                    <option value="">Select Cuisine</option>
                    <?php foreach ($cuisines as $cui): ?>
                        <option value="<?php echo htmlspecialchars($cui['id']); ?>">
                            <?php echo htmlspecialchars($cui['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- <div class="form-group">
                    <label for="images">Upload Food Image (JPG/PNG, max 2MB)</label>
                </div> -->

                <p class="label">Upload Food Image (JPG/PNG, max 2MB)</p>
                <input type="file" name="image" id="image" accept="image/jpeg,image/png" required>

                <input type="submit" value="Add Food" class="btn btn-primary">
            </fieldset>
        </form>

        <!-- <div class="form-group">
            <a href="admin_orders.php" class="btn">View All Orders</a>
        </div> -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const hamburger = document.getElementById("hamburger");
            const navLinks = document.getElementById("navLinks");

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