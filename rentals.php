<?php
session_start();

$loggedIn = isset($_SESSION['user_id']);

require_once 'db_config.php';
$query = $dbh->prepare(
    "SELECT
        a.ad_id AS id,
        p.property_id AS property_id,
        p.name AS name,
        p.type_id AS type_id,
        t.type_name AS type,
        p.location_id AS location_id,
        l.location_name AS location,
        p.street_address AS addr,
        p.capacity AS capacity,
        p.parking AS parking,
        p.pets_allowed AS pets,
        a.rental_period AS period,
        a.rental_price AS price,
        a.description AS description,
        (a.user_id = :user_id) AS is_owner
    FROM
        advertisements a
    INNER JOIN properties p ON
        p.property_id = a.property_id
    INNER JOIN property_types t ON
        t.type_id = p.type_id
    INNER JOIN locations l ON
        l.location_id = p.location_id
    INNER JOIN user_accounts u ON
        a.user_id = u.user_id
    WHERE
        a.status = 'PUBLIC' AND
        u.account_state NOT LIKE 'BLOCKED'"
);
$userId = $_SESSION['user_id'] ?? -1;
$query->bindParam(':user_id', $userId, PDO::PARAM_INT);
$query->execute();
$adverts = $query->fetchAll(PDO::FETCH_OBJ);

$query = $dbh->prepare("SELECT * FROM locations");
$query->execute();
$locations = $query->fetchAll(PDO::FETCH_OBJ);

$query = $dbh->prepare("SELECT * FROM property_types");
$query->execute();
$types = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Rental Advertisements - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>

    <style>
        main {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-title {
            flex-shrink: 0;
            padding: 5px;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
            align-content: center;

            >.button-primary {
                margin: unset;
            }
        }
    </style>
    <script>
        class Advert {
            constructor(element, name, type_id, type, location_id, location, addr, capacity, parking, pets, period, price) {
                this.element = element;
                this.name = name;
                this.type_id = type_id;
                this.type = type;
                this.location_id = location_id;
                this.location = location;
                this.addr = addr;
                this.capacity = capacity;
                this.parking = parking;
                this.pets = pets;
                this.period = period;
                this.price = price;
            }

            matchLocationId(id) {
                return this.location_id == id;
            }

            matchTypeId(id) {
                return this.type_id == id;
            }

            checkPrice(val) {
                return this.price <= val;
            }

            checkPeriod(val) {
                return this.period <= val;
            }

            checkCapacity(val) {
                return this.capacity >= val;
            }

            hasParking() {
                return this.parking;
            }

            hasPets() {
                return this.pets;
            }

            hasFlexible() {
                return this.period == 0;
            }
        }
        var ads = [];
    </script>
</head>

<body>
    <header>
        <?php
        require_once('navbar.php');
        if (!$loggedIn) require_once('login_popup.php');
        ?>
    </header>

    <main>
        <div class="page-title">
            <h1>
                Rental Advertisements
            </h1>
        </div>
        <div class="filters-section">
            <span class="search-box">
                <a href="#" id="expand-filters-btn"><i class="bi bi-filter"></i></a>
                <input type="search" placeholder="Filter by name, location, address..." name="search-term" id="search-term">
                <a href="#" id="search-btn"><i class="bi bi-search"></i></a>
            </span>
            <div class="filters">
                <span class="inp-group">
                    <label for="location-id">Location: </label>
                    <select name="location-id" id="location-id">
                        <option value="" hidden>Select a Location</option>
                        <?php
                        foreach ($locations as $loc) {
                            echo "<option value='$loc->location_id'>$loc->location_name</option>";
                        }
                        ?>
                    </select>
                </span>
                <span class="inp-group">
                    <label for="type-id">Type: </label>
                    <select name="type-id" id="type-id">
                        <option value="" hidden>Select a type</option>
                        <?php
                        foreach ($types as $type) {
                            echo "<option value='$type->type_id'>$type->type_name</option>";
                        }
                        ?>
                    </select>
                </span>
                <input placeholder="Maximum Price (RSD)" name="max-price" id="max-price" type="number" min=0>
                <input placeholder="Maximum Period (Months)" name="max-period" id="max-period" type="number" min=0>
                <input placeholder="Minimum Capacity" name="min-capacity" id="min-capacity" type="number" min=1>
                <span style="display: flex; justify-content:space-between;">
                    <div class="checkbox-group">
                        <input type="checkbox" name="parking" id="parking">
                        <label>Parking</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="pets" id="pets">
                        <label>Pets</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="flexible" id="flexible">
                        <label>Flexible Period</label>
                    </div>
                </span>
                <span style="display: flex; gap: 10px;">
                    <a class="button-primary" style="flex-grow: 1" href="#" id="apply-filters-btn">Apply Filters</a>
                    <a class="button-primary" href="#" id="clear-filters-btn"><i class="bi bi-x-lg"></i></a>
                </span>
            </div>
        </div>
        <div class="advert-list">
            <?php
            if (empty($adverts)) {
                echo '<span id="no-ads-text">No Ads posted yet!</span>';
            } else {
                foreach ($adverts as $ad) {
                    $photos = json_decode(file_get_contents("uploads/$ad->property_id.json"));
                    $photosStr = "";
                    $first = true;
                    foreach ($photos as $photo) {
                        if ($first) {
                            $photosStr .= "<img class='active' src='$photo'>" . PHP_EOL;
                            $first = false;
                        } else {
                            $photosStr .= "<img src='$photo'>" . PHP_EOL;
                        }
                    }
                    $actions = ($ad->is_owner ? ''
                        : "<a href='#' id='$ad->id-fav' data-ad-id='$ad->id' class='button-primary button-sm'><i class='bi bi-star'></i> +Fav</a>
                        <a href='#' id='$ad->id-contact' data-ad-id='$ad->id' class='button-primary button-sm'><i class='bi bi-envelope-paper-fill'></i> Contact</a>");
                    echo
                    "<div class='item' id='$ad->id'>
                        <div class='photo-reel'>
                            $photosStr
                            <a href='#' class='prev-btn'><i class='bi bi-arrow-left'></i></a>
                            <a href='#' class='next-btn'><i class='bi bi-arrow-right'></i></a>
                        </div>
                        <div class='info'>
                            <span class='name'>$ad->name</span>
                            <span class='type'>$ad->type</span>
                            <span class='location'>$ad->location</span>
                            <span class='specs'>
                                <span class='capactiy' title='Capacity'><i class='bi bi-people-fill'></i> $ad->capacity</span>
                                <span class='parking' title='Parking'><i class='bi bi-p-square-fill'></i> " . ($ad->parking == 1 ? 'Yes' : 'No') . "</span>
                                <span class='pets' title='Pets'>🐱" . ($ad->pets == 1 ? 'Yes' : 'No') . "</span>
                            </span>
                            <span class='price-period'>RSD $ad->price p/m, " . ($ad->period == 0 ? 'Flexible Period' : "$ad->period Months") . "</span>
                            <span class='description'>$ad->description</span>
                            <span class='btn-bar'>
                                <a href='#' id='$ad->id-view' data-ad-id='$ad->id' class='button-primary button-sm'><i class='bi bi-eye-fill'></i> View</a>
                                $actions
                            </span>
                        </div>
                    </div>";
                    echo "<script>
                        ads.push(
                            new Advert(
                                $('#$ad->id'),
                                '$ad->name',
                                $ad->type_id,
                                '$ad->type',
                                $ad->location_id,
                                '$ad->location',
                                '$ad->addr',
                                $ad->capacity,
                                ($ad->parking == 1 ? true : false), 
                                ($ad->pets == 1 ? true : false),
                                $ad->period,
                                $ad->price
                            )
                        );
                    </script>";
                }
            }
            ?>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script src="./rentals.js"></script>
    <script>
        $(".advert-list>.item>.info>.btn-bar>a[id$='view']").click(function(e) {
            e.preventDefault();
            <?php
            if ($loggedIn) {
                echo 'let id = e.target.id;
                if (id != "") {
                    let adId = $(`#${id}`).data("ad-id");
                    window.location.href = "view_advertisement.php?return-url=rentals.php&ad-id=" + adId;
                }';
            } else {
                echo "showLoginRegPopup();";
            }
            ?>
        });

        $(".advert-list>.item>.info>.btn-bar>a[id$='fav']").click(function(e) {
            e.preventDefault();
            console.log('clicked fav');
            return;
            let id = e.target.id;
            if (id != '') {
                let adId = $(`#${id}`).data('ad-id');
                window.location.href = "view_advertisement.php?return-url=rentals.php&ad-id=" + adId;
            }
        });

        $(".advert-list>.item>.info>.btn-bar>a[id$='contact']").click(function(e) {
            e.preventDefault();
            <?php
            if ($loggedIn) {
                echo 'let id = e.target.id;
                if (id != "") {
                    let adId = $(`#${id}`).data("ad-id");
                    window.location.href = "advert_contact.php?return-url=rentals.php&ad-id=" + adId;
                }';
            } else {
                echo "showLoginRegPopup();";
            }
            ?>
        });
    </script>
</body>

</html>