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

  <link rel="stylesheet" href="css/cart.css">
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

        <li><a href="about.html">About Us</a></li>
        <li><a href="contact.html">Contact</a></li>

        <li><a href="login.html" class="login-btn">Login →</a></li>
        <li class="cart"><a href="cart.php">🛒</a></li>
      </ul>

    </nav>
  </header>
  <!-- Navigation Section End -->

  <!-- Food Order Section Start -->
  <section class="order">
    <div class="container">
      <h2 class="text-center">Your Order List</h2>
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
        <!-- <tr>
                    <td>1</td>
                    <td><img src="img/food/p1.jpg" alt="Food"></td>
                    <td>Pizza</td>
                    <td>$ 8.00</td>
                    <td>1</td>
                    <td>$ 8.00</td>
                    <td><a href="#" class="btn-delete">&times;</a></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><img src="img/food/s1.jpg" alt="Food"></td>
                    <td>Sandwich</td>
                    <td>$ 8.00</td>
                    <td>1</td>
                    <td>$ 8.00</td>
                    <td><a href="#" class="btn-delete">&times;</a></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><img src="img/food/b1.jpg" alt="Food"></td>
                    <td>Burder</td>
                    <td>$ 8.00</td>
                    <td>1</td>
                    <td>$ 8.00</td>
                    <td><a href="#" class="btn-delete">&times;</a></td>
                </tr> -->
        <tr>
          <th colspan="5">Total</th>
          <th id="cartTotal">$0.00</th>
          <th></th>
        </tr>
      </table>
      <form action="" class="form">
        <fieldset>
          <legend>Delivery Details</legend>
          <p class="label">Full Name</p>
          <input type="text" placeholder="Enter your name..." required>
          <p class="label">Phone Number</p>
          <input type="contact" placeholder="Enter your phone..." required>
          <p class="label">Email</p>
          <input type="email" placeholder="Enter your email..." required>
          <p class="label">Address</p>
          <input type="text" placeholder="Enter your address..." required>
          <input type="submit" value="Confirm Order" class="btn-primary">
        </fieldset>
      </form>
    </div>
  </section>
  <!-- Food Order Section End -->

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

      // Update cart display on page load
      updateCartDisplay();

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
    });

  </script>
</body>

</html>