<?php
$routes = array(
    "index.php" => [
        'label' => "Home",
        'icon' => "<i class='bi bi-house-heart'></i>"
    ],
    "rentals.php" => [
        'label' => "Rentals",
        'icon' => "<i class='bi bi-buildings'></i>"
    ],
    "about.php" => [
        'label' => "About Us",
        'icon' => "<i class='bi bi-info-circle'></i>"
    ],
    "contact.php" => [
        'label' => "Contact",
        'icon' => "<i class='bi bi-envelope-at-fill'></i>"
    ]
);

$reqUrl = $_SERVER['REQUEST_URI'];
$activeIdx = str_ends_with($reqUrl, '/') ? 0 : -1;
if ($activeIdx == -1) {
    $c = 0;
    foreach ($routes as $k => $v) {
        if (str_ends_with($reqUrl, $k)) {
            $activeIdx = $c;
        }
        $c += 1;
    }
}

$loggedIn = isset($_SESSION['user_id']);
$isAdmin = $_SESSION['is_admin'] ?? false;
?>

<div class="navbar" id="navbar">
    <div class="nav" id="navbar-expand-button">
        <i class="bi bi-chevron-right"></i> <span class="label">Collapse</span>
    </div>
    <div style="flex-grow: 1;"></div>
    <?php
    $c = 0;
    foreach ($routes as $k => $v) {
        if ($c == $activeIdx) {
            echo
            "<div class='nav active'>
                " . $v['icon'] . "<span class='label'>" . $v['label'] . "</span>
            </div>";
        } else {
            echo
            "<a href='" . $k .  "'>
                <div class='nav'>
                    " . $v['icon'] . "<span class='label'>" . $v['label'] . "</span>
                </div>
            </a>";
        }
        $c += 1;
    }
    ?>
    <div style="flex-grow: 1;"></div>
    <div class="nav" id="user-nav">
        <i class="bi bi-person-circle"></i> <span class="label">User</span>
    </div>
    <div class="user-menu">
        <?php
        if (!$loggedIn) {
            echo '<div class="item" id="login-reg-btn">Login/Register</div>';
        } else {
            if ($isAdmin) {
                echo '<div class="item" id="admin-users-btn">Manage Users</div>
                <div class="item" id="admin-adverts-btn">Pending Advertisements</div>
                <div class="item" id="admin-all-adverts-btn">All Advertisements</div>
                <hr>';
            }
            echo '<div class="item" id="my-favorites-btn">My Favorites</div>
            <div class="item" id="my-props-btn">My Properties</div>
            <div class="item" id="my-ads-btn">My Advertisements</div>
            <hr>
            <div class="item" id="user-settings-btn">Settings</div>
            <div class="item" id="logout-btn">Logout</div>';
        }
        ?>
    </div>
</div>