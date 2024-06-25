<?php
session_start();

$loggedIn = isset($_SESSION['user_id']);
$loginTime = $_SESSION['login_time'] ?? null;
?>

<!doctype html>
<html lang="en">

<head>
    <title>Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
    <link rel="stylesheet" href="./carousel.css">
</head>

<body>
    <header>
        <?php
        require_once('navbar.php');
        if (!$loggedIn) require_once('login_popup.php');
        ?>
    </header>

    <main style="height: 100vh;">
        <div class="home-content">
            <h1>Subotica Rentals</h1>
            <blockquote class="blockquote">
                <p>Find your perfect fit, effortlessly.</p>
            </blockquote>
            <p>
                Looking for a hassle-free way to rent or lease a property? Welcome to Subotica Rentals, your one-stop shop for connecting with incredible places to live. Our user-friendly platform makes finding your dream home or advertising your rental property a breeze.
            </p>
            <a class="button-primary" id="post-ad-btn" href="<?php echo ($loggedIn ? "post_advert.php" : "#"); ?>">Post an Ad today!</a>
            <?php
            if ($loggedIn) {
                echo "
                <script>
                    var loginTime = $loginTime;
                </script>";
            }
            ?>
            <h3>Popular Listings</h3>
            <div class="carousel">
                <div class="item">
                    <div class="blur-bg" style="background-image: url('images/sample_property_0.webp');"></div>
                    <div class="foreground-img"><img src="./images/sample_property_0.webp"></div>
                    <span class="title"> Property #1 </span>
                    <span class="description"> Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. </span>
                </div>
                <div class="item">
                    <div class="blur-bg" style="background-image: url('images/sample_property_1.webp');"></div>
                    <div class="foreground-img"><img src="./images/sample_property_1.webp"></div>
                    <span class="title"> Property #2 </span>
                    <span class="description"> Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. </span>
                </div>
                <div class="item">
                    <div class="blur-bg" style="background-image: url('images/sample_property_2.webp');"></div>
                    <div class="foreground-img"><img src="./images/sample_property_2.webp"></div>
                    <span class="title"> Property #3 </span>
                    <span class="description"> Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. </span>
                </div>
                <div class="item">
                    <div class="blur-bg" style="background-image: url('images/sample_property_3.webp');"></div>
                    <div class="foreground-img"><img src="./images/sample_property_3.webp"></div>
                    <span class="title"> Property #4 </span>
                    <span class="description"> Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. </span>
                </div>
                <div class="item">
                    <div class="blur-bg" style="background-image: url('images/sample_property_4.webp');"></div>
                    <div class="foreground-img"><img src="./images/sample_property_4.webp"></div>
                    <span class="title"> Property #5 </span>
                    <span class="description"> Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. </span>
                </div>
                <div class="control" id="prev"><i class="bi bi-arrow-left"></i></div>
                <div class="control" id="next"><i class="bi bi-arrow-right"></i></div>
            </div>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script src="./carousel.js"></script>
</body>

</html>