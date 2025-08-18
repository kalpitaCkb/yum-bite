<?php

session_start();
if (isset($_SESSION["email"])) {
  header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>YumBite | Login</title>
  <link rel="icon" href="images/logo1.png" type="image/png">

  <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/login.css" />
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
          <?php if (isset($_SESSION["full_name"])): ?>
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

  <div class="wrapper-container">
    <div class="wrapper">

      <?php

      if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        require_once "database.php";

        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

        if ($user) {
          if ($user["role"] === "admin") {
            // Direct password comparison for admin
            if ($password === $user["password"]) {
              session_start();
              $_SESSION["user_id"] = $user["id"];
              $_SESSION["email"] = $user["email"];
              $_SESSION["full_name"] = $user["full_name"];
              $_SESSION["role"] = $user["role"];
              header("Location: admin.php");
              die();
            } else {
              echo "<div class='error'>Password does not match</div>";
            }
          } else {
            // Password hash verification for regular users
            if (password_verify($password, $user["password"])) {
              session_start();
              $_SESSION["user_id"] = $user["id"];
              $_SESSION["email"] = $user["email"];
              $_SESSION["full_name"] = $user["full_name"];
              $_SESSION["phone"] = $user["phone"];
              $_SESSION["role"] = $user["role"];
              header("Location: index.php");
              die();
            } else {
              echo "<div class='error'>Password does not match</div>";
            }
          }
        } else {
          echo "<div class='error'>Email does not exists</div>";
        }
      }

      ?>

      <form action="login.php" method="post">
        <h1>Login</h1>

        <div class="input-box">
          <input type="email" name="email" placeholder="Email" />
        </div>

        <div class="input-box">
          <input type="password" name="password" placeholder="Password" />
        </div>

        <div>
          <input type="submit" value="Login" name="login" class="btn">
        </div>

        <div class="register-link">
          <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
      </form>
    </div>
  </div>

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

  <script>
    // Theme Switcher
    function switchTheme(path) {
      document.getElementById("theme-style").setAttribute("href", path);
      localStorage.setItem("theme", path);
    }

    // Hamburger Menu Toggle
    document.addEventListener('DOMContentLoaded', function () {
      const hamburger = document.getElementById("hamburger");
      const navLinks = document.getElementById("navLinks");

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