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

// Handle order submission
if (isset($_POST["confirm_order"])) {
  // Retrieve cart from a hidden input field (populated by JavaScript)
  $cart_json = $_POST['cart_data'] ?? '[]';
  $cart = json_decode($cart_json, true);
  $amount = 0;

  // Calculate total amount from cart
  foreach ($cart as $item) {
    $amount += $item['price'] * $item['quantity'];
  }

  if (empty($cart)) {
    array_push($errors, "Cart is empty");
  } else {
    // Start transaction
    $conn->begin_transaction();

    try {
      $user_id = $_SESSION['user_id']; // Fetch from session or default
      $status = "Pending";

      // Insert order details
      $order_sql = "INSERT INTO orders (user_id, food_id, quantity, amount, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
      $order_stmt = mysqli_stmt_init($conn);
      if (mysqli_stmt_prepare($order_stmt, $order_sql)) {
        foreach ($cart as $item) {
          $food_id = $item['id'];
          $quantity = $item['quantity'];
          $item_amount = $item['price'] * $item['quantity'];
          mysqli_stmt_bind_param($order_stmt, "iiids", $user_id, $food_id, $quantity, $item_amount, $status);
          mysqli_stmt_execute($order_stmt);
        }
        mysqli_stmt_close($order_stmt);

        // Clear cart
        echo "<script>localStorage.removeItem('cart');</script>";
        $success = "Order placed successfully!";
      } else {
        throw new Exception("Failed to prepare order insertion");
      }

      $conn->commit();
    } catch (Exception $e) {
      $conn->rollback();
      array_push($errors, "Error placing order: " . $e->getMessage());
    }
  }
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
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/index.css">

  <!-- Favicon -->
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
  <!-- Fontawesome CSS -->
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">
  <!-- Hover CSS -->
  <link rel="stylesheet" href="css/hover-min.css">
  <!-- Custom CSS -->

  <link rel="stylesheet" href="css/cart.css">

  <style>
    form input.disabled {
      cursor: not-allowed;
      background-color: #ddd;
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
  <section class="order">
    <div class="container">
      <h2 class="text-center">Your Order List</h2>
      <div class="table-container">
        <table class="tbl-full" border="0">
          <tr>
            <th>S.N.</th>
            <th>Food</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
          </tr>
          <tbody id="cartItems"></tbody>
          <tr>
            <th colspan="5">Total</th>
            <th id="cartTotal">$0.00</th>
            <th></th>
          </tr>
        </table>
      </div>
      <form action="" method="POST" class="form" id="orderForm">
        <fieldset>
          <legend>Delivery Details</legend>
          <p class="label">Full Name</p>
          <input type="text" name="fullName" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>"
            class="disabled" disabled>
          <p class="label">Phone Number</p>
          <input type="tel" name="phone" placeholder="Enter your phone..." required>
          <p class="label">Email</p>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>"
            class="disabled" disabled>
          <p class="label">Address</p>
          <input type="text" name="address" placeholder="Enter your address..." required>

          <!-- Added hidden input for cart data -->
          <input type="hidden" id="cart_data" name="cart_data" value="">

          <input type="submit" name="confirm_order" value="Confirm Order" class="btn-primary">
        </fieldset>
      </form>
      <?php
      if (!empty($errors)) {
        foreach ($errors as $error) {
          echo "<p style='color:red;'>$error</p>";
        }
      }
      if (!empty($success)) {
        echo "<p style='color:green;'>$success</p>";
      }
      ?>
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
    // Cart Management
    function getCart() {
      return JSON.parse(localStorage.getItem('cart')) || [];
    }

    function saveCart(cart) {
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartDisplay();
    }

    function updateCartDisplay() {
      const cart = getCart();
      const cartItems = document.getElementById('cartItems');
      const cartTotal = document.getElementById('cartTotal');
      cartItems.innerHTML = '';

      let total = 0;
      cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${index + 1}</td>
          <td><img src="${item.image}" alt="${item.name}" style="width: 60px; height: 60px;"></td>
          <td>${item.name}</td>
          <td>TK. ${item.price.toFixed(2)}</td>
          <td>
            <div class="quantity-control">
              <button class="minus-btn" data-id="${item.id}">-</button>
              <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-id="${item.id}">
              <button class="plus-btn" data-id="${item.id}">+</button>
            </div>
          </td>
          <td>TK. ${itemTotal.toFixed(2)}</td>
          <td><a href="#" class="btn-delete" data-id="${item.id}">&times;</a></td>
        `;
        cartItems.appendChild(row);
      });

      cartTotal.textContent = `TK. ${total.toFixed(2)}`;

      const cartDataInput = document.getElementById('cart_data');
      if (cartDataInput) {
        cartDataInput.value = JSON.stringify(cart);
        console.log('Cart updated in hidden input:', cart); // Debug log
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      const hamburger = document.getElementById("hamburger");
      const navLinks = document.getElementById("navLinks");

      // Hamburger toggle
      hamburger.addEventListener("click", function () {
        hamburger.classList.toggle("active");
        navLinks.classList.toggle("show");
      });

      // Close menu on link click
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

      // Update cart display on page load
      updateCartDisplay();

      // Update cart data before form submission
      orderForm.addEventListener('submit', function (e) {
        const cartDataInput = document.getElementById('cart_data');
        const cart = getCart();
        if (cart.length === 0) {
          e.preventDefault();
          alert('Your cart is empty!');
          return;
        }
        cartDataInput.value = JSON.stringify(cart);
        console.log('Cart data set for submission:', cart);
      });

      // Event delegation for quantity controls and delete buttons
      document.addEventListener('click', function (e) {
        const id = e.target.getAttribute('data-id');
        const cart = getCart();

        if (e.target.classList.contains('plus-btn')) {
          const item = cart.find(item => item.id === id);
          if (item) {
            item.quantity++;
            saveCart(cart);
          }
        } else if (e.target.classList.contains('minus-btn')) {
          const item = cart.find(item => item.id === id);
          if (item && item.quantity > 1) {
            item.quantity--;
            saveCart(cart);
          }
        } else if (e.target.classList.contains('btn-delete')) {
          e.preventDefault();
          const updatedCart = cart.filter(item => item.id !== id);
          saveCart(updatedCart);
        }
      });

      // Handle quantity input changes
      document.addEventListener('change', function (e) {
        if (e.target.classList.contains('quantity-input')) {
          const id = e.target.getAttribute('data-id');
          const value = parseInt(e.target.value);
          const cart = getCart();
          const item = cart.find(item => item.id === id);

          if (item) {
            item.quantity = Math.max(1, value);
            saveCart(cart);
          }
        }
      });

      // Ensure cart data is set on page load
      const initialCart = getCart();
      if (initialCart.length > 0) {
        document.getElementById('cart_data').value = JSON.stringify(initialCart);
        console.log('Initial cart set:', initialCart); // Debug log
      }
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