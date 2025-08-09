<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link id="theme-style" rel="stylesheet" href="css/style-dark.css" />
    <link rel="stylesheet" href="css/login.css" />
    <title>Document</title>
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
                <li><a href="menu.html">Menu</a></li>
                <li><a href="#">Categories</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Theme
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a href="#" onclick="switchTheme('css/style-dark.css')">🌙 Dark Theme</a></li>
                        <li><a href="#" onclick="switchTheme('css/style-light.css')">☀️ Light Theme</a></li>
                    </ul>
                </li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact</a></li>
                <li class="cart"><a href="#">🛒 Cart</a></li>

                <?php if (isset($_SESSION["user"])) { ?>
                    <div>Welcome, <?= $_SESSION["user"] ?></div>
                    <li><a href="logout.php" class="login-btn">Logout ←</a></li>
                <?php } else { ?>
                    <li><a href="login.php" class="login-btn">Login →</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

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