<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Home</title>
  <link rel="icon" href="images/logo1.png" type="image/png">

  <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/menu.css">

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
        <li><a href="login.php" class="login-btn">Login →</a></li>
        <li class="cart"><a href="cart.php">🛒</a></li>
      </ul>
    </nav>
  </header>

  <div class="notification" id="cartNotification">Item added to cart!</div>

  <?php

  require_once "database.php";

  // get the search keyword
  
  $search = isset($_POST['search']) && $_POST['search'] !== '' ? $_POST['search'] : '';
  $sort = isset($_POST['sort']) ? $_POST['sort'] : 'top_rated';
  $cuisines = isset($_POST['cuisine']) ? (array) $_POST['cuisine'] : [];

  // Handle clear actions
  if (isset($_POST['clear_filter'])) {
    $sort = 'top_rated';
    $cuisines = [];
  }

  // Build the SQL query
  $sql = "SELECT * FROM food WHERE 1=1"; // Base query
  
  // Add search condition
  if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR cuisine LIKE '%$search%' OR restaurant LIKE '%$search%')";
  }

  // Add cuisine filter
  if (!empty($cuisines)) {
    $cuisine_conditions = [];
    foreach ($cuisines as $cuisine) {
      $cuisine = mysqli_real_escape_string($conn, $cuisine);
      $cuisine_conditions[] = "cuisine = '$cuisine'";
    }
    $sql .= " AND (" . implode(" OR ", $cuisine_conditions) . ")";
  }

  // Add sorting
  if ($sort == 'top_rated') {
    $sql .= " ORDER BY price DESC"; // Assuming price could be a proxy for rating if no rating column exists
  } elseif ($sort == 'fastest') {
    $sql .= " ORDER BY price ASC"; // Placeholder; replace with actual delivery_time if available
  }

  $res = mysqli_query($conn, $sql);

  $count = mysqli_num_rows($res);

  ?>

  <!-- fOOD MEnu Section Starts Here -->
  <section class="food-menu-container">
    <div class="filter-container">
      <div class="filter">
        <p class="filter-text">Filters</p>
        <form action="menu.php" method="POST">
          <div class="sort">
            <p>Sort By</p>
            <div>
              <input type="radio" name="sort" id="top_rated" value="top_rated" <?php echo ($sort == 'top_rated') ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="top_rated">Top Rated</label>
            </div>
            <div>
              <input type="radio" name="sort" id="fastest" value="fastest" <?php echo ($sort == 'fastest') ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="fastest">Fastest Delivery</label>
            </div>
          </div>
          <div class="cuisine">
            <p>Cuisines</p>
            <div>
              <input type="checkbox" name="cuisine[]" id="american" value="american" <?php echo in_array('american', $cuisines) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="american">American</label><br>
            </div>
            <div>
              <input type="checkbox" name="cuisine[]" id="bangla" value="bangla" <?php echo in_array('bangla', $cuisines) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="bangla">Bangla</label><br>
            </div>
            <div>
              <input type="checkbox" name="cuisine[]" id="chinese" value="chinese" <?php echo in_array('chinese', $cuisines) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="chinese">Chinese</label><br>
            </div>
            <div>
              <input type="checkbox" name="cuisine[]" id="indian" value="indian" <?php echo in_array('indian', $cuisines) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="indian">Indian</label><br>
            </div>
            <div>
              <input type="checkbox" name="cuisine[]" id="italian" value="italian" <?php echo in_array('italian', $cuisines) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="italian">Italian</label><br>
            </div>
            <div>
              <input type="checkbox" name="cuisine[]" id="japanese" value="japanese" <?php echo in_array('japanese', $cuisines) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="japanese">Japanese</label><br>
            </div>
            <div>
              <input type="checkbox" name="cuisine[]" id="mexican" value="mexican" <?php echo in_array('mexican', $cuisines) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label for="mexican">Mexican</label><br>
            </div>
          </div>
          <input type="submit" name="clear_filter" value="Clear Filter" class="btn btn-primary">
        </form>
      </div>
    </div>
    <div class="right-container">
      <!-- fOOD sEARCH Section Starts Here -->
      <section class="food-search">
        <div class="container">
          <form action="menu.php" method="POST" id="searchForm">
            <div class="search-box">
              <input type="search" name="search" id="searchInput" placeholder="Search for Food.."
                value="<?php echo $search; ?>">
              <input type="submit" name="search-submit" value="Search" class="btn btn-primary" style="padding: 1% 2%;">
            </div>
            <div>
            </div>
          </form>
        </div>
      </section>
      <!-- fOOD sEARCH Section Ends Here -->
      <div class="food-menu">
        <h2 class="text-center">Food Menu</h2>
        <div class="container">

          <?php
          // check whether food is available or not
          
          if ($count > 0) {

            while ($row = mysqli_fetch_array($res)) {
              // get the details
              $id = $row["id"];
              $name = $row["name"];
              $price = $row["price"];
              $description = $row["description"];
              $category = $row["category_title"];
              $restaurant = $row["restaurant"];
              $cuisine = $row["cuisine"];
              $image = $row["image"];
              ?>

              <div class="food-menu-box">
                <div class="food-menu-img">
                  <?php

                  if ($image == "") {
                    echo "<div>Image not available</div>";
                  } else {
                    ?>

                    <img src="uploads/<?php echo $image; ?>" alt="Chicke Hawain Pizza" class="img-responsive img-curve">

                    <?php
                  }

                  ?>
                </div>
                <div class="food-menu-desc">
                  <p class="food-cuisine"><?php echo $cuisine; ?></p>
                  <h4><?php echo $name ?></h4>
                  <p class="food-price">TK. <?php echo $price; ?><span class="dot"></span><?php echo $category; ?></p>
                  <p class="food-detail">
                    <?php echo $description ?>
                  </p>
                  <h4><?php echo $restaurant; ?></h4>
                  <div class="button">
                    <a href="#" class="btn btn-primary quick-view" data-id="<?php echo $id; ?>"
                      data-name="<?php echo htmlspecialchars($name); ?>"
                      data-price="<?php echo htmlspecialchars($price); ?>"
                      data-category="<?php echo htmlspecialchars($category); ?>"
                      data-description="<?php echo htmlspecialchars($description); ?>"
                      data-restaurant="<?php echo htmlspecialchars($restaurant); ?>"
                      data-image="<?php echo $image ? 'uploads/' . htmlspecialchars($image) : ''; ?>">Quick View</a>
                    <!-- <a href="#" class="btn btn-primary" data-id="<?php echo $id; ?>">Add to cart</a> -->
                    <a href="#" class="btn btn-primary add-to-cart" data-id="<?php echo $id; ?>"
                      data-name="<?php echo htmlspecialchars($name); ?>"
                      data-price="<?php echo htmlspecialchars($price); ?>"
                      data-image="<?php echo $image ? 'Uploads/' . htmlspecialchars($image) : ''; ?>">Add to cart</a>
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
    </div>
  </section>
  <!-- fOOD Menu Section Ends Here -->

  <!-- Modal Structure -->
  <div id="foodModal" class="modal">
    <div class="modal-content">
      <span class="close-btn">&times;</span>
      <div id="modalImageContainer"></div>
      <h4 id="modalName"></h4>
      <p id="modalPrice"></p>
      <p id="modalCategory"></p>
      <p id="modalDescription"></p>
      <p id="modalRestaurant"></p>
      <!-- <a href="#" class="btn btn-primary">Add to Cart</a> -->
      <div class="cart-control">
        <div class="quantity-control">
          <button class="minus-btn">-</button>
          <input type="number" class="quantity-input" value="1" min="1">
          <button class="plus-btn">+</button>
        </div>
        <a href="#" class="btn-primary add-to-cart">Add to Cart</a>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 YumBite. All Rights Reserved.</p>
  </footer>

  <script>
    // Theme Switcher
    function switchTheme(path) {
      document.getElementById("theme-style").setAttribute("href", path);
      localStorage.setItem("theme", path);
    }

    // Cart Management
    function getCart() {
      return JSON.parse(localStorage.getItem('cart')) || [];
    }

    function saveCart(cart) {
      localStorage.setItem('cart', JSON.stringify(cart));
    }

    function addToCart(item) {
      const cart = getCart();
      const existingItem = cart.find(cartItem => cartItem.id === item.id);
      if (existingItem) {
        existingItem.quantity += item.quantity;
      } else {
        cart.push(item);
      }
      saveCart(cart);
      showNotification();
    }

    function showNotification() {
      const notification = document.getElementById('cartNotification');
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
      const quickViewButtons = document.querySelectorAll(".quick-view");

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

      // Handle search input clear (browser clear button or manual deletion)
      searchInput.addEventListener('input', function (e) {
        console.log("Input event triggered, value:", e.target.value); // Debugging
        if (e.target.value === '') {
          console.log("Search input cleared, submitting form"); // Debugging
          searchForm.submit(); // Submit form to reload page
        }
      });

      // Handle browser's default clear button (for WebKit browsers)
      searchInput.addEventListener('search', function (e) {
        console.log("Search event triggered, value:", e.target.value); // Debugging
        if (e.target.value === '') {
          console.log("Search input cleared via clear button, submitting form"); // Debugging
          searchForm.submit(); // Submit form to reload page
        }
      });

      // Quick View Modal Functionality
      quickViewButtons.forEach(button => {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          const name = this.getAttribute('data-name');
          const price = this.getAttribute('data-price');
          const category = this.getAttribute('data-category');
          const description = this.getAttribute('data-description');
          const restaurant = this.getAttribute('data-restaurant');
          const image = this.getAttribute('data-image');
          const id = this.getAttribute('data-id');

          // Populate modal content
          document.getElementById('modalName').textContent = name;
          document.getElementById('modalPrice').textContent = `TK. ${price}`;
          document.getElementById('modalCategory').textContent = `${category}`;
          document.getElementById('modalDescription').textContent = description;
          document.getElementById('modalRestaurant').textContent = `${restaurant}`;

          // Handle image
          const imageContainer = document.getElementById('modalImageContainer');
          imageContainer.innerHTML = ''; // Clear previous content
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

          // Update modal cart control with item ID
          const modalCartControl = modal.querySelector('.cart-control');
          modalCartControl.querySelector('.add-to-cart').setAttribute('data-id', id);
          modalCartControl.querySelector('.add-to-cart').setAttribute('data-name', name);
          modalCartControl.querySelector('.add-to-cart').setAttribute('data-price', price);
          modalCartControl.querySelector('.add-to-cart').setAttribute('data-image', image);
          modalCartControl.querySelector('.quantity-input').setAttribute('data-id', id);
          modalCartControl.querySelector('.plus-btn').setAttribute('data-id', id);
          modalCartControl.querySelector('.minus-btn').setAttribute('data-id', id);

          // Initialize minus button state
          const minusBtn = modalCartControl.querySelector('.minus-btn');
          minusBtn.disabled = true; // Disable by default since quantity starts at 1

          // Show modal
          modal.style.display = 'flex';
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

      // Cart functionality
      const addToCartButtons = document.querySelectorAll('.add-to-cart');
      addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          const cartControl = this.parentElement;
          const quantityControl = cartControl.querySelector('.quantity-control');
          const quantity = parseInt(cartControl.querySelector('.quantity-input')?.value || 1);
          const item = {
            id: this.getAttribute('data-id'),
            name: this.getAttribute('data-name'),
            price: parseFloat(this.getAttribute('data-price')),
            image: this.getAttribute('data-image'),
            quantity: quantity
          };

          addToCart(item);

          // if (quantityControl) {
          //   quantityControl.style.display = 'flex';
          // }
        });
      });

      // Quantity controls
      function updateButtonState(input) {
        const cartControl = input.parentElement.parentElement;
        const minusBtn = cartControl.querySelector('.minus-btn');
        const value = parseInt(input.value);
        minusBtn.disabled = value <= 1;
      }

      document.querySelectorAll('.plus-btn').forEach(button => {
        button.addEventListener('click', function () {
          const cartControl = this.parentElement.parentElement;
          const input = cartControl.querySelector('.quantity-input');
          let value = parseInt(input.value);
          input.value = value + 1;
          updateButtonState(input);
        });
      });

      document.querySelectorAll('.minus-btn').forEach(button => {
        button.addEventListener('click', function () {
          const cartControl = this.parentElement.parentElement;
          const quantityControl = cartControl.querySelector('.quantity-control');
          const addToCartBtn = cartControl.querySelector('.add-to-cart');
          const input = cartControl.querySelector('.quantity-input');
          let value = parseInt(input.value);

          if (value > 1) {
            input.value = value - 1;
            updateButtonState(input);
          }
        });
      });

      // Ensure quantity stays at minimum 1 and update button state
      document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function () {
          if (this.value < 1) {
            this.value = 1;
          }
          updateButtonState(this);
        });
      });

      // Initialize button state on page load
      document.querySelectorAll('.quantity-input').forEach(input => {
        updateButtonState(input);
      });

    });
  </script>
</body>

</html>