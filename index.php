<?php

session_start();
if (!isset($_SESSION["user"])) {
  header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YumBite | Home</title>
  <link rel="icon" href="images/logo1.png" type="image/png">
</head>

<body>

  <?php include "navbar.php" ?>

  <section class="hero">
    <h1>Welcome to YumBite</h1>
    <p>Your favorite meals, delivered fresh!</p>
    <a href="menu.html" class="btn">Order Now</a>
  </section>

  <footer>
    <p>&copy; 2025 YumBite. All Rights Reserved.</p>
  </footer>
</body>

</html>