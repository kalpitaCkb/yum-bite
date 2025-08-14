<?php

session_start();
// if (!isset($_SESSION["user"])) {
//   header("Location: login.php");
// }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Home</title>
  <link rel="icon" href="images/logo1.png" type="image/png">

  <!-- Swiper CSS -->
  <!-- <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" /> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Your dark theme CSS -->
  <link rel="stylesheet" href="css/style-dark.css">
  <link rel="stylesheet" href="css/index.css">
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
        <li><a href="contact.html">Contact</a></li>
        <li>
          <?php if (isset($_SESSION["user"])): ?>
            <div class="profile-circle" id="profileCircle">
              <?php echo strtoupper(substr($_SESSION["user"], 0, 1)); ?>
              <div class="dropdown-menu" id="dropdownMenu">
                <a href="account.php">My Account</a>
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

  <section class="hero">
    <h1>Welcome to YumBite</h1>
    <p>Your favorite meals, delivered fresh!</p>
    <a href="menu.php" class="btn">Order Now</a>
  </section>

  <!-- Slider Section -->
  <section class="slider-section">

    <h2>🍽️ Best Deals</h2>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="images/best-deals/pizza.jpg" alt="Deal 1" />
          <p>50% Off Pizza</p>
        </div>
        <div class="swiper-slide"><img src="images/best-deals/drinks-with-burger.avif" alt="Deal 2" />
          <p>Free Drink with Burger</p>
        </div>
        <div class="swiper-slide"><img src="images/best-deals/combo.webp" alt="Deal 3" />
          <p>Combo Meals</p>
        </div>
        <div class="swiper-slide"><img src="images/best-deals/b1g1.jpg" alt="Deal 4" />
          <p>Buy 1 Get 1 Free</p>
        </div>
        <div class="swiper-slide"><img src="images/best-deals/dessert.jpg" alt="Deal 5" />
          <p>20% Off Desserts</p>
        </div>
        <div class="swiper-slide"><img src="images/best-deals/biriyani.jpeg" alt="Deal 6" />
          <p>Free Delivery Over 500 BDT</p>
        </div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>

    <h2>🍴 Top Categories</h2>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="images/top-categories/pizza.jpg" alt="Pizza" />
          <p>Pizza</p>
        </div>
        <div class="swiper-slide"><img src="images/top-categories/burger.jpg" alt="Burger" />
          <p>Burger</p>
        </div>
        <div class="swiper-slide"><img src="images/top-categories/chicken.webp" alt="Chicken Fry" />
          <p>Chicken Fry</p>
        </div>
        <div class="swiper-slide"><img src="images/top-categories/biryani.jpg" alt="Biryani" />
          <p>Biryani</p>
        </div>
        <div class="swiper-slide"><img src="images/top-categories/nachos.jpg" alt="Nachos" />
          <p>Nachos</p>
        </div>
        <div class="swiper-slide"><img src="images/top-categories/sushi.webp" alt="Sushi" />
          <p>Sushi</p>
        </div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>


    <h2>📍 Nearby Restaurants</h2>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="images/nearby-restaurants/snackza.jpg" alt="Snackza" />
          <p>Snackza</p>
        </div>
        <div class="swiper-slide"><img src="images/nearby-restaurants/sultans-dine.png" alt="Sultan's Dine" />
          <p>Sultan's Dine</p>
        </div>
        <div class="swiper-slide"><img src="images/nearby-restaurants/wabi-sabi.png" alt="Wabi Sabi" />
          <p>Wabi Sabi</p>
        </div>
        <div class="swiper-slide"><img src="images/nearby-restaurants/club-grille.jpg" alt="Club Grille" />
          <p>Club Grille</p>
        </div>
        <div class="swiper-slide"><img src="images/nearby-restaurants/vrctg.jpg" alt="VR Chittagong" />
          <p>VR Chittagong</p>
        </div>
        <div class="swiper-slide"><img src="images/nearby-restaurants/kfc.png" alt="KFC" />
          <p>KFC</p>
        </div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>

    <h2>⭐ Top Rated Restaurants</h2>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="images/top-rated/the-arrosto.jpg" alt="The Arrosto" />
          <p>The Arrosto</p>
        </div>
        <div class="swiper-slide"><img src="images/top-rated/pizzaburg.jpg" alt="PizzaBurg" />
          <p>PizzaBurg</p>
        </div>
        <div class="swiper-slide"><img src="images/top-rated/the-ambrosia.jpg" alt="Ambrosia Restaurant" />
          <p>Ambrosia Restaurant</p>
        </div>
        <div class="swiper-slide"><img src="images/top-rated/segafredo.jpg" alt="Segafredo Espresso Chittagong" />
          <p>Segafredo Espresso Chittagong</p>
        </div>
        <div class="swiper-slide"><img src="images/nearby-restaurants/wabi-sabi.png" alt="Wabi Sabi" />
          <p>Wabi Sabi</p>
        </div>
        <div class="swiper-slide"><img src="images/top-rated/tslr.jpg" alt="The Sky Lounge & Restaurant" />
          <p>The Sky Lounge & Restaurant</p>
        </div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>

    <!-- <h2>🍛 Indian Cuisine</h2>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="images/indian1.jpg" alt="Paneer Tikka"/><p>Paneer Tikka</p></div>
        <div class="swiper-slide"><img src="images/indian2.jpg" alt="Biryani"/><p>Biryani</p></div>
        <div class="swiper-slide"><img src="images/indian3.jpg" alt="Dosa"/><p>Dosa</p></div>
        <div class="swiper-slide"><img src="images/indian4.jpg" alt="Butter Chicken"/><p>Butter Chicken</p></div>
        <div class="swiper-slide"><img src="images/indian5.jpg" alt="Chaat"/><p>Chaat</p></div>
        <div class="swiper-slide"><img src="images/indian6.jpg" alt="Naan"/><p>Naan</p></div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-pagination"></div>
    </div> -->

    <!-- <h2>🍣 Asian Cuisine</h2>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="images/asian1.jpg" alt="Sushi"/><p>Sushi</p></div>
        <div class="swiper-slide"><img src="images/asian2.jpg" alt="Ramen"/><p>Ramen</p></div>
        <div class="swiper-slide"><img src="images/asian3.jpg" alt="Thai Curry"/><p>Thai Curry</p></div>
        <div class="swiper-slide"><img src="images/asian4.jpg" alt="Dim Sum"/><p>Dim Sum</p></div>
        <div class="swiper-slide"><img src="images/asian5.jpg" alt="Bibimbap"/><p>Bibimbap</p></div>
        <div class="swiper-slide"><img src="images/asian6.jpg" alt="Pho"/><p>Pho</p></div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-pagination"></div>
    </div> -->

    <!-- <h2>🍕 Fast Food Picks</h2>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="images/fastfood1.jpg" alt="Burgers"/><p>Burgers</p></div>
        <div class="swiper-slide"><img src="images/fastfood2.jpg" alt="Fries"/><p>Fries</p></div>
        <div class="swiper-slide"><img src="images/fastfood3.jpg" alt="Tacos"/><p>Tacos</p></div>
        <div class="swiper-slide"><img src="images/fastfood4.jpg" alt="Hot Dogs"/><p>Hot Dogs</p></div>
        <div class="swiper-slide"><img src="images/fastfood5.jpg" alt="Pizza"/><p>Pizza</p></div>
        <div class="swiper-slide"><img src="images/fastfood6.jpg" alt="Onion Rings"/><p>Onion Rings</p></div>
      </div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-pagination"></div>
    </div> -->

  </section>

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

  <!-- Swiper JS -->
  <!-- <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


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

    // Initialize Swiper sliders
    const sliders = document.querySelectorAll('.mySwiper');
    sliders.forEach(swiperContainer => {
      new Swiper(swiperContainer, {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        simulateTouch: true,
        grabCursor: true,
        pagination: {
          el: swiperContainer.querySelector('.swiper-pagination'),
          clickable: true,
        },
        navigation: {
          nextEl: swiperContainer.querySelector('.swiper-button-next'),
          prevEl: swiperContainer.querySelector('.swiper-button-prev'),
        },
        breakpoints: {
          480: {
            slidesPerView: 2,
          },
          768: {
            slidesPerView: 3,
          },
          1200: {
            slidesPerView: 4
          }
        },
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