<?php
session_start();

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}

require_once "database.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $full_name = $_SESSION["full_name"] ?? '';
  $email = $_SESSION["email"] ?? '';
  $phone = $_SESSION["phone"] ?? '';
  $subject = $_POST["subject"] ?? '';
  $message = $_POST["message"] ?? '';

  // Sanitize inputs
  $full_name = $conn->real_escape_string($full_name);
  $email = $conn->real_escape_string($email);
  $phone = $conn->real_escape_string($phone);
  $subject = $conn->real_escape_string($subject);
  $message = $conn->real_escape_string($message);

  // Insert into contact table
  $sql = "INSERT INTO contact (full_name, email, phone, subject, message) 
            VALUES ('$full_name', '$email', '$phone', '$subject', '$message')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Message sent successfully!');</script>";
  } else {
    echo "<script>alert('Error: " . $conn->error . "');</script>";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Contact</title>
  <link rel="icon" href="images/logo1.png" type="image/png" />

  <!-- Your dark theme CSS -->
  <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/index.css">

  <!-- Favicon -->
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
  <!-- Fontawesome CSS -->
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css" />
  <!-- Hover CSS -->
  <link rel="stylesheet" href="css/hover-min.css" />
  <!-- Custom CSS -->

  <link rel="stylesheet" href="css/cart.css" />

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

  <!-- Contact Section Start -->
  <section class="contact">
    <div class="container">
      <h2 class="text-center" style="color: white;">Get in touch</h2>
      <div class="heading-border"></div>

      <form action="contact.php" method="POST" class="form">
        <fieldset>
          <legend>Contact Us</legend>
          <p class="label">Full Name</p>
          <input type="text"
            value="<?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : ''; ?>"
            class="disabled" disabled />
          <p class="label">Email</p>
          <input type="email"
            value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"
            class="disabled" disabled />
          <p class="label">Phone Number</p>
          <input type="tel" value="<?php echo isset($_SESSION['phone']) ? htmlspecialchars($_SESSION['phone']) : ''; ?>"
            class="disabled" disabled />
          <p class="label">Subject</p>
          <input type="text" name="subject" placeholder="Enter your subject..." required />
          <p class="label">Message</p>
          <textarea rows="5" name="message" placeholder="Enter your message..." required
            style="width: 100%; box-sizing: border-box"></textarea>

          <input type="submit" value="Submit" class="btn-primary" />
        </fieldset>
      </form>
    </div>
  </section>
  <!-- Contact Section End -->

  <!-- Map Section Start -->
  <section class="map">
    <h2 class="text-center">Find Us</h2>
    <div class="heading-border"></div>

    <iframe id="gmap_canvas"
      src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d118103.45687625333!2d91.81986775!3d22.32593435!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sbd!4v1671287502415!5m2!1sen!2sbd"></iframe>
  </section>
  <!-- Map Section End -->

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
    // Hamburger Menu Toggle
    document.addEventListener('DOMContentLoaded', function () {
      const hamburger = document.getElementById("hamburger");
      const navLinks = document.getElementById("navLinks");

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