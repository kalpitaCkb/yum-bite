<?php
session_start();

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

require_once "database.php";

$errors = array();
$success = "";
$user_email = $_SESSION["email"];

// Fetch user data
$sql = "SELECT full_name, phone, email, address FROM users WHERE email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $user_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    die("Something went wrong with fetching user data");
}

// Handle account info update
if (isset($_POST["update_info"])) {
    $full_name = $_POST["full_name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $sql = "UPDATE users SET full_name = ?, phone = ?, email = ?, address = ? WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $full_name, $phone, $email, $address, $user_email);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Profile updated successfully";
            // Update session email if changed
            if ($email !== $user_email) {
                $_SESSION["email"] = $email;
            }
            // Refresh user data
            $sql = "SELECT full_name, phone, email, address FROM users WHERE email = ?";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user_data = mysqli_fetch_assoc($result);
            }
        } else {
            array_push($errors, "Failed to update profile");
        }
        mysqli_stmt_close($stmt);
    } else {
        die("Something went wrong with update preparation");
    }
}

// Handle password update
if (isset($_POST["update_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        array_push($errors, "All password fields are required");
    } elseif ($new_password !== $confirm_password) {
        array_push($errors, "New passwords do not match");
    } elseif (strlen($new_password) < 8) {
        array_push($errors, "New password must be at least 8 characters long");
    } else {
        // Verify current password
        $sql = "SELECT password FROM users WHERE email = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $user_email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if (password_verify($current_password, $user['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE email = ?";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $user_email);
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "Password updated successfully";
                    } else {
                        array_push($errors, "Failed to update password");
                    }
                } else {
                    array_push($errors, "Something went wrong with password update preparation");
                }
            } else {
                array_push($errors, "Current password is incorrect");
            }
            mysqli_stmt_close($stmt);
        } else {
            array_push($errors, "Something went wrong with password verification");
        }
    }
}

// Fetch orders for the current user
$error = "";
$sql = "SELECT o.id, f.name AS food_name, o.quantity, o.amount, o.status, o.created_at
        FROM orders o
        JOIN food f ON o.food_id = f.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    $error = "Prepare failed: " . mysqli_error($conn);
} else {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    if (!mysqli_stmt_execute($stmt)) {
        $error = "Execute failed: " . mysqli_stmt_error($stmt);
    } else {
        $result = mysqli_stmt_get_result($stmt);
        if (!$result || mysqli_num_rows($result) == 0) {
            $error = "No orders found for user_id " . htmlspecialchars($_SESSION['user_id']);
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>YumBite | Home</title>
    <link rel="icon" href="images/logo1.png" type="image/png">

    <!-- Your dark theme CSS -->
    <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">
    <!-- Hover CSS -->
    <link rel="stylesheet" href="css/hover-min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <!-- Navigation Section Start -->
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
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="#" onclick="openAbout()">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li>
                    <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "user"): ?>
                        <div class="profile-circle" id="profileCircle">
                            <?php echo strtoupper(substr($_SESSION["full_name"], 0, 1)); ?>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="profile.php">My Account</a>
                                <a href="logout.php">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="login-btn">Login →</a>
                    <?php endif; ?>
                </li>
                <li class="cart"><a href="cart.php">🛒</a></li>
            </ul>
        </nav>
    </header>

    <!-- Navigation Section End -->

    <!-- Food Order Section Start -->
    <section class="account-container">
        <div class="left-content">
            <div class="active">Profile</div>
            <div>Orders</div>
        </div>
        <div class="right-content">
            <?php
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='error'>$error</div>";
                }
            }
            if (!empty($success)) {
                echo "<div class='success'>$success</div>";
            }
            ?>
            <div id="account-info" class="active">
                <form action="" method="POST" class="form" id="profileForm">
                    <fieldset>
                        <legend>Account Information</legend>
                        <p class="label">Full Name</p>
                        <input type="text" name="full_name"
                            value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" disabled>
                        <p class="label">Phone Number</p>
                        <input type="tel" name="phone"
                            value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" disabled>
                        <p class="label">Email</p>
                        <input type="email" name="email"
                            value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" disabled>
                        <p class="label">Address</p>
                        <input type="text" name="address"
                            value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>" disabled>
                        <input type="button" value="Edit Information" class="btn-primary" id="editButton">
                        <input type="submit" value="Update Information" class="btn-primary" id="updateButton"
                            name="update_info" style="display: none;">
                    </fieldset>
                </form>

                <br>
                <br>

                <form action="" method="POST" class="form" id="passwordForm">
                    <fieldset>
                        <legend>Update Password</legend>
                        <p class="label">Current Password</p>
                        <input type="password" name="current_password">
                        <p class="label">New Password</p>
                        <input type="password" name="new_password">
                        <p class="label">Confirm New Password</p>
                        <input type="password" name="confirm_password">
                        <input type="submit" value="Update Password" class="btn-primary" name="update_password">
                    </fieldset>
                </form>
            </div>

            <div id="order-info">
                <?php if (isset($error) && !empty($error)) { ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Food</th>
                        <th>Quantity</th>
                        <th>Amount (BDT)</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                    <?php if (isset($result) && mysqli_num_rows($result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6">No orders found.</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </section>
    <!-- Food Order Section End -->

    <!-- Triggered by clicking "About Us" link in nav -->
    <!-- About Us Popup -->
    <div class="popup" id="aboutPopup">
        <div class="popup-content">
            <span onclick="closeAbout()">&times;</span>
            <h2>About Us</h2>
            <p>
                Launched in 2021, Our technology platform connects customers,<br>
                restaurant partners and delivery partners, serving their multiple needs. <br>
                Customers use our platform to search and discover restaurants, read and write customer
                generated reviews and view and upload photos,<br> order food delivery, book a table and make
                payments while dining-out at restaurants. On the other hand,<br> we provide restaurant partners
                with industry-specific marketing tools which enable them to engage and acquire customers<br> to
                grow their business while also providing a reliable <br>and efficient last mile delivery service.
                We also operate a one-stop procurement solution, <br>Hyperpure, which supplies high quality ingredients
                and kitchen products to restaurant partners.<br> We also provide our delivery partners with transparent
                and flexible earning opportunities.
            </p>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 YumBite. All Rights Reserved.</p>
    </footer>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <!-- Jquery UI -->
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <!-- Custom JS -->

    <script>
        // Hamburger toggle
        hamburger.addEventListener("click", function () {
            hamburger.classList.toggle("active");
            navLinks.classList.toggle("show");
        });

        // Close menu on link click (mobile UX)
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navLinks.classList.remove('show');
            });
        });

        // Profile dropdown toggle
        document.getElementById('profileCircle')?.addEventListener('click', function () {
            const dropdown = document.getElementById('dropdownMenu');
            dropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const profileCircle = document.getElementById('profileCircle');
            const dropdown = document.getElementById('dropdownMenu');
            if (profileCircle && dropdown && !profileCircle.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Edit/Update functionality
        document.getElementById('editButton').addEventListener('click', function () {
            // Enable input fields
            document.querySelectorAll('#profileForm input[type="text"], #profileForm input[type="tel"], #profileForm input[type="email"]').forEach(input => {
                input.disabled = false;
            });
            // Toggle buttons
            this.style.display = 'none';
            document.getElementById('updateButton').style.display = 'inline-block';
        });

        document.querySelectorAll('.left-content div').forEach(item => {
            item.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.left-content div').forEach(i => i.classList.remove('active'));
                item.classList.add('active');

                // Remove active class from all sections
                document.querySelectorAll('.right-content div').forEach(section => section.classList.remove('active'));

                // Map tab text to section ID
                const sectionMap = {
                    'Profile': 'account-info',
                    'Orders': 'order-info'
                };

                // Add active class to the corresponding section
                const sectionId = sectionMap[item.textContent];
                if (sectionId) {
                    document.getElementById(sectionId).classList.add('active');
                }
            });
        });
    </script>

    <script>
        function openAbout() {
            document.getElementById("aboutPopup").style.display = "block";
        }

        function closeAbout() {
            document.getElementById("aboutPopup").style.display = "none";
        }
    </script>

</body>

</html>