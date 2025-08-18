<?php
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "database.php";

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Status updated successfully!";
        } else {
            $error = "Error updating status: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement";
    }
}

// Fetch orders with user and food details
$sql = "SELECT o.id, u.full_name, f.name AS food_name, o.quantity, o.amount, o.status, o.created_at
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN food f ON o.food_id = f.id
        ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YumBite | Admin Orders</title>
    <link rel="icon" href="images/logo1.png" type="image/png">
    <link id="theme-style" rel="stylesheet" href="css/style-dark.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/signup.css">
    <style>
        .logout:hover {
            color: #ffa500;
        }

        .admin-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #ff6f61;
            color: white;
        }

        .btn {
            background-color: #ff6f61;
            color: white;
            padding: 5px 10px;
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
                padding: 20px 30px;
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

    <div class="admin-container">
        <h1 class="text-center">All Orders</h1>

        <?php
        if (isset($success)) {
            echo "<div class='success'>$success</div>";
        }
        if (isset($error)) {
            echo "<div class='error'>$error</div>";
        }
        ?>

        <table>
            <tr>
                <!-- <th>ID</th> -->
                <th>User</th>
                <th>Food</th>
                <th>Quantity</th>
                <th>Amount (BDT)</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <!-- <td><?php echo htmlspecialchars($row['id']); ?></td> -->
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending
                                </option>
                                <option value="Accepted" <?php echo $row['status'] == 'Accepted' ? 'selected' : ''; ?>>
                                    Accepted</option>
                                <option value="Preparing" <?php echo $row['status'] == 'Preparing' ? 'selected' : ''; ?>>
                                    Preparing</option>
                                <option value="Out for Delivery" <?php echo $row['status'] == 'Out for Delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                <option value="Completed" <?php echo $row['status'] == 'Completed' ? 'selected' : ''; ?>>
                                    Completed</option>
                                <option value="Cancelled" <?php echo $row['status'] == 'Cancelled' ? 'selected' : ''; ?>>
                                    Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn">Update</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
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
<?php mysqli_close($conn); ?>