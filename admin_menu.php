<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>YumBite | Admin Menu</title>
    <link rel="icon" href="images/logo1.png" type="image/png">

    <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_menu.css">

    <style>
        .logout:hover {
            color: #ffa500;
        }

        .food-menu h2 {
            margin-top: 40px;
        }

        @media screen and (max-width: 768px) {
            .food-menu h2 {
                margin-top: 0;
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

    // Fetch categories, restaurants, and cuisines for modal
    $category_sql = "SELECT id, title FROM categories";
    $category_res = mysqli_query($conn, $category_sql);
    $categories = mysqli_fetch_all($category_res, MYSQLI_ASSOC);

    $restaurant_sql = "SELECT id, name FROM restaurants";
    $restaurant_res = mysqli_query($conn, $restaurant_sql);
    $restaurants = mysqli_fetch_all($restaurant_res, MYSQLI_ASSOC);

    $cuisine_sql = "SELECT id, name FROM cuisines";
    $cuisine_res = mysqli_query($conn, $cuisine_sql);
    $cuisines = mysqli_fetch_all($cuisine_res, MYSQLI_ASSOC);

    // Handle Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);
        $category_title = mysqli_real_escape_string($conn, $_POST['category']);
        $cuisine_name = mysqli_real_escape_string($conn, $_POST['cuisine']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $restaurant_name = mysqli_real_escape_string($conn, $_POST['restaurant']);
        $current_image = ''; // To store the existing image
    
        // Fetch current image
        $current_image_query = "SELECT image FROM food WHERE id = ?";
        if ($img_stmt = mysqli_prepare($conn, $current_image_query)) {
            mysqli_stmt_bind_param($img_stmt, "i", $id);
            mysqli_stmt_execute($img_stmt);
            $result = mysqli_stmt_get_result($img_stmt);
            $row = mysqli_fetch_assoc($result);
            $current_image = $row ? $row['image'] : '';
            mysqli_stmt_close($img_stmt);
        }

        $image = $current_image; // Default to current image
    
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
            $image = $_FILES['image']['name'];
            $target = "uploads/" . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        }

        // Get IDs based on names
        // $category_id = 0;
        $category_query = "SELECT id FROM categories WHERE title = ? LIMIT 1";
        if ($category_stmt = mysqli_prepare($conn, $category_query)) {
            mysqli_stmt_bind_param($category_stmt, "s", $category_title);
            mysqli_stmt_execute($category_stmt);
            $result = mysqli_stmt_get_result($category_stmt);
            $row = mysqli_fetch_assoc($result);
            $category_id = $row ? $row['id'] : 0;
            mysqli_stmt_close($category_stmt);
        }

        // $restaurant_id = 0;
        $restaurant_query = "SELECT id FROM restaurants WHERE name = ? LIMIT 1";
        if ($restaurant_stmt = mysqli_prepare($conn, $restaurant_query)) {
            mysqli_stmt_bind_param($restaurant_stmt, "s", $restaurant_name);
            mysqli_stmt_execute($restaurant_stmt);
            $result = mysqli_stmt_get_result($restaurant_stmt);
            $row = mysqli_fetch_assoc($result);
            $restaurant_id = $row ? $row['id'] : 0;
            mysqli_stmt_close($restaurant_stmt);
        }

        // $cuisine_id = 0;
        $cuisine_query = "SELECT id FROM cuisines WHERE name = ? LIMIT 1";
        if ($cuisine_stmt = mysqli_prepare($conn, $cuisine_query)) {
            mysqli_stmt_bind_param($cuisine_stmt, "s", $cuisine_name);
            mysqli_stmt_execute($cuisine_stmt);
            $result = mysqli_stmt_get_result($cuisine_stmt);
            $row = mysqli_fetch_assoc($result);
            $cuisine_id = $row ? $row['id'] : 0;
            mysqli_stmt_close($cuisine_stmt);
        }

        // Validate IDs
        if (!$category_id || !$restaurant_id || !$cuisine_id) {
            echo "<script>alert('Invalid category, restaurant, or cuisine name.');</script>";
            exit();
        }

        // Update query with prepared statement
        $sql = "UPDATE food SET name = ?, price = ?, category_id = ?, cuisine_id = ?, description = ?, restaurant_id = ?, image = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sdiisisi", $name, $price, $category_id, $cuisine_id, $description, $restaurant_id, $image, $id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if ($success) {
                header("Location: admin_menu.php?status=updated");
                exit();
            } else {
                echo "<script>alert('Error updating food item: " . mysqli_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Error preparing update statement: " . mysqli_error($conn) . "');</script>";
        }
    }

    // Handle Delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $sql = "DELETE FROM food WHERE id = '$id'";
        $success = mysqli_query($conn, $sql);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    // Display notification if updated
    if (isset($_GET['status']) && $_GET['status'] === 'updated') {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { 
            var notification = document.getElementById('cartNotification'); 
            notification.textContent = 'Food item updated successfully!'; 
            notification.style.display = 'block'; 
            setTimeout(() => { notification.style.display = 'none'; }, 2000); 
        });</script>";
    }

    // Fetch food items
    // Fetch food items with JOINs
    $sql = "SELECT f.id, f.name, f.price, f.description, f.image, c.title AS category, r.name AS restaurant, cu.name AS cuisine 
            FROM food f 
            LEFT JOIN categories c ON f.category_id = c.id 
            LEFT JOIN restaurants r ON f.restaurant_id = r.id 
            LEFT JOIN cuisines cu ON f.cuisine_id = cu.id";
    $res = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($res);
    ?>

    <div class="notification" id="cartNotification">Action completed!</div>

    <?php
    require_once "database.php";

    // // Build the SQL query with JOINs
    $sql = "SELECT f.id, f.name, f.price, f.description, f.image, c.title AS category, r.name AS restaurant, cu.name AS cuisine 
            FROM food f 
            LEFT JOIN categories c ON f.category_id = c.id 
            LEFT JOIN restaurants r ON f.restaurant_id = r.id 
            LEFT JOIN cuisines cu ON f.cuisine_id = cu.id";

    $res = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($res);
    ?>

    <!-- fOOD MEnu Section Starts Here -->
    <section class="food-menu-container">
        <div class="food-menu">
            <h2 class="text-center">Food Menu</h2>
            <div class="container">
                <?php
                if ($count > 0) {
                    while ($row = mysqli_fetch_array($res)) {
                        $id = $row["id"];
                        $name = $row["name"];
                        $price = $row["price"];
                        $description = $row["description"];
                        $category_title = $row["category"];
                        $restaurant_name = $row["restaurant"];
                        $cuisine_name = $row["cuisine"];
                        $image = $row["image"];
                        ?>
                        <div class="food-menu-box">
                            <div class="food-menu-img">
                                <?php
                                if ($image == "") {
                                    echo "<div>Image not available</div>";
                                } else {
                                    ?>
                                    <img src="uploads/<?php echo $image; ?>" alt="<?php echo $name; ?>"
                                        class="img-responsive img-curve">
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="food-menu-desc">
                                <p class="food-cuisine"><?php echo $cuisine_name; ?></p>
                                <h4><?php echo $name ?></h4>
                                <p class="food-price">TK. <?php echo $price; ?><span
                                        class="dot"></span><?php echo $category_title; ?>
                                </p>
                                <p class="food-detail"><?php echo $description ?></p>
                                <h4><?php echo $restaurant_name; ?></h4>
                                <div class="button">
                                    <a href="#" class="btn btn-primary update-btn" data-id="<?php echo $id; ?>"
                                        data-name="<?php echo htmlspecialchars($name); ?>"
                                        data-price="<?php echo htmlspecialchars($price); ?>"
                                        data-category="<?php echo htmlspecialchars($category_title); ?>"
                                        data-description="<?php echo htmlspecialchars($description); ?>"
                                        data-restaurant="<?php echo htmlspecialchars($restaurant_name); ?>"
                                        data-cuisine="<?php echo htmlspecialchars($cuisine_name); ?>"
                                        data-image="<?php echo $image ? 'uploads/' . htmlspecialchars($image) : ''; ?>">Update</a>
                                    <a href="#" class="btn btn-primary" data-id="<?php echo $id; ?>"
                                        data-name="<?php echo htmlspecialchars($name); ?>">Delete</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div>Food not found</div>";
                }
                ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
    <!-- fOOD Menu Section Ends Here -->

    <!-- Modal Structure -->
    <div id="foodModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h4>Update Food Item</h4>
            <form id="updateForm" enctype="multipart/form-data" method="POST">
                <input type="hidden" name="id" id="modalId">
                <div id="modalImageContainer"></div>
                <label for="imageUpload">Update Image:</label>
                <input type="file" id="imageUpload" name="image" accept="image/*">
                <label for="modalName">Name:</label>
                <input type="text" id="modalName" name="name" required>
                <label for="modalPrice">Price (TK):</label>
                <input type="number" id="modalPrice" name="price" step="0.01" min="0" required>
                <label for="modalCategory">Category:</label>
                <select id="modalCategory" name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['title']); ?>">
                            <?php echo htmlspecialchars($cat['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="modalCuisine">Cuisine:</label>
                <select id="modalCuisine" name="cuisine" required>
                    <?php foreach ($cuisines as $cui): ?>
                        <option value="<?php echo htmlspecialchars($cui['name']); ?>">
                            <?php echo htmlspecialchars($cui['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="modalDescription">Description:</label>
                <textarea id="modalDescription" name="description" required></textarea>
                <label for="modalRestaurant">Restaurant:</label>
                <select id="modalRestaurant" name="restaurant" required>
                    <?php foreach ($restaurants as $rest): ?>
                        <option value="<?php echo htmlspecialchars($rest['name']); ?>">
                            <?php echo htmlspecialchars($rest['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Theme Switcher
        function switchTheme(path) {
            document.getElementById("theme-style").setAttribute("href", path);
            localStorage.setItem("theme", path);
        }

        // Notification
        function showNotification(message) {
            const notification = document.getElementById('cartNotification');
            notification.textContent = message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 2000);
        }

        // Hamburger Menu Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const hamburger = document.getElementById("hamburger");
            const navLinks = document.getElementById("navLinks");
            const searchInput = document.getElementById("searchInput");
            const searchForm = document.getElementById("searchForm");
            const modal = document.getElementById("foodModal");
            const closeBtn = document.querySelector(".close-btn");
            const updateButtons = document.querySelectorAll(".update-btn");
            const deleteButtons = document.querySelectorAll(".delete-btn");

            // Restore Theme
            const savedTheme = localStorage.getItem("theme");
            if (savedTheme) {
                switchTheme(savedTheme);
            }

            // Toggle Hamburger Menu
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

            // Update Modal Functionality
            updateButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const price = this.getAttribute('data-price');
                    const category = this.getAttribute('data-category');
                    const description = this.getAttribute('data-description');
                    const restaurant = this.getAttribute('data-restaurant');
                    const cuisine = this.getAttribute('data-cuisine');
                    const image = this.getAttribute('data-image');

                    // Populate modal form
                    document.getElementById('modalId').value = id;
                    document.getElementById('modalName').value = name;
                    document.getElementById('modalPrice').value = price;
                    document.getElementById('modalCategory').value = category;
                    document.getElementById('modalCuisine').value = cuisine;
                    document.getElementById('modalDescription').value = description;
                    document.getElementById('modalRestaurant').value = restaurant;

                    // Handle image
                    const imageContainer = document.getElementById('modalImageContainer');
                    imageContainer.innerHTML = '';
                    if (image) {
                        const img = document.createElement('img');
                        img.src = image;
                        img.alt = name;
                        imageContainer.appendChild(img);
                    } else {
                        const noImage = document.createElement('div');
                        noImage.className = 'no-image';
                        noImage.textContent = 'Image not available';
                        imageContainer.appendChild(noImage);
                    }

                    // Show modal
                    modal.style.display = 'flex';
                });
            });

            // Delete Functionality
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    if (confirm(`Are you sure you want to delete "${name}"?`)) {
                        // Send AJAX request to delete
                        fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=delete&id=${id}`
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showNotification('Food item deleted successfully!');
                                    // Remove the item from the DOM
                                    button.closest('.food-menu-box').remove();
                                } else {
                                    showNotification('Error deleting food item.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('Error deleting food item.');
                            });
                    }
                });
            });

            // Close modal
            closeBtn.addEventListener('click', function () {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside
            window.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Form submission
            document.getElementById('updateForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'update');

                fetch('admin_menu.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (response.ok) {
                            showNotification('Food item updated successfully!');
                            modal.style.display = 'none';
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            showNotification('Error updating food item.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error updating food item.');
                    });
            });
        });
    </script>
</body>

</html>