<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Home</title>
  <link rel="icon" href="images/logo1.png" type="image/png">

  <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
  <link rel="stylesheet" href="css/signup.css" />
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
            <li><a href="#" onclick="switchTheme('css/style-dark.css')">🌙 Dark Theme</a></li>
            <li><a href="#" onclick="switchTheme('css/style-light.css')">☀️ Light Theme</a></li>
          </ul>
        </li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Contact</a></li>
        <li class="cart"><a href="#">🛒 Cart</a></li>
        <li><a href="login.html" class="login-btn">Login →</a></li>
      </ul>
    </nav>
  </header>

  <div class="wrapper-container">
    <div class="wrapper">
      <?php
      if (isset($_POST["submit"])) {
        $fullName = $_POST["fullname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $errors = array();

        if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
          array_push($errors, "All Fields are required");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          array_push($errors, "Email is not valid");
        }
        if (strlen($password) < 8) {
          array_push($errors, "Password must be at least 8 characters long");
        }
        if ($password != $confirmPassword) {
          array_push($errors, "Password does not match");
        }

        require_once "database.php";

        $sql = "SELECT * FROM users WHERE email = '$email'";

        $result = mysqli_query($conn, $sql);

        $rowCount = mysqli_num_rows($result);

        if ($rowCount > 0) {
          array_push($errors, "Email already exists!");
        }

        if (count($errors) > 0) {
          foreach ($errors as $error) {
            echo "<div class='error'>$error</div>";
          }
        } else {
          $sql = "INSERT INTO users (full_name, email, password) VALUES ( ?, ?, ? )";
          $stmt = mysqli_stmt_init($conn);
          $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
          
          if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
            mysqli_stmt_execute($stmt);
            echo "<div class='success'>Account created successfully</div>";
            header("Location: login.php");
          } else {
            die("Something went wrong");
          }
        }
      }
      ?>

      <form action="signup.php" method="post">
        <h1>Sign Up</h1>

        <div class="input-box">
          <input type="text" name="fullname" placeholder="Full Name" />
        </div>

        <div class="input-box">
          <input type="email" name="email" placeholder="Email" />
        </div>

        <div class="input-box">
          <input type="password" name="password" placeholder="Password" />
        </div>

        <div class="input-box">
          <input type="password" name="confirm_password" placeholder="Confirm Password" />
        </div>

        <div>
          <input type="submit" value="Signup" name="submit" class="btn">
        </div>

        <div class="register-link">
          <p>Already have an account? <a href="login.php">Login</a></p>
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