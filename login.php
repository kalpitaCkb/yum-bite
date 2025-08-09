<?php

session_start();
if (isset($_SESSION["user"])) {
  header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="icon" href="images/logo1.png" type="image/png">

  <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
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
        <li><a href="index.html">Home</a></li>
        <li><a href="menu.html">Menu</a></li>
        <li><a href="#">Categories</a></li>
        <li class="dropdown">
          <input type="checkbox" id="theme-toggle" class="dropdown-checkbox" />
          <label for="theme-toggle" class="dropdown-label">Theme ⬇</label>
          <ul class="dropdown-menu">
            <li>
              <a href="#" onclick="switchTheme('css/style-dark.css')">🌙 Dark Theme</a>
            </li>
            <li>
              <a href="#" onclick="switchTheme('css/style-light.css')">☀️ Light Theme</a>
            </li>
          </ul>
        </li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Contact</a></li>
        <li class="cart"><a href="#">🛒 Cart</a></li>
        <li><a href="login.php" class="login-btn">Login →</a></li>
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
          if (password_verify($password, $user["password"])) {
            session_start();
            $_SESSION["user"] = $user["full_name"];
            header("Location: index.php");
            die();
          } else {
            echo "<div class='error'>Password does not match</div>";
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

        <div class="remember-forget">
          <label><input type="checkbox" /> Remember me</label>
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
</body>

</html>