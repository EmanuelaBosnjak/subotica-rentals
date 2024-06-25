<?php
session_start();

$loggedIn = isset($_SESSION['user_id']);
if (!$loggedIn) {
    header('Location:index.php');
    die();
}

require_once 'db_config.php';
$userId = $_SESSION['user_id'];
$query = $dbh->prepare(
    "SELECT
        p.property_id AS id,
        p.name AS name,
        t.type_name AS type,
        l.location_name AS location,
        p.street_address AS addr,
        p.capacity AS capacity,
        p.parking AS parking,
        p.pets_allowed AS pets,
        p.description AS description
    FROM
        properties p
    INNER JOIN property_types t ON
        t.type_id = p.type_id
    INNER JOIN locations l ON
        l.location_id = p.location_id
    WHERE
        user_id = $userId"
);
$query->execute();
$properties = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <title>My Properties - Subotica Rentals</title>
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
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main>
        <div class="page-title">
            <h1>
                My Properties
            </h1>
            <a class="button-primary" href="add_property.php?return-url=my_properties.php"><i class="bi bi-house-heart"></i> Add Property</a>
        </div>
        <div class="property-list">
            <?php
            if (empty($properties)) {
                echo '<span id="no-prop-text">No Properties added yet!</span>';
            } else {
                foreach ($properties as $prop) {
                    $photos = json_decode(file_get_contents("uploads/$prop->id.json"));
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
                    echo
                    "<div class='item'>
                        <div class='photo-reel'>
                            $photosStr
                            <a href='#' class='prev-btn'><i class='bi bi-arrow-left'></i></a>
                            <a href='#' class='next-btn'><i class='bi bi-arrow-right'></i></a>
                        </div>
                        <div class='info'>
                            <span class='name'>$prop->name</span>
                            <span class='type'>$prop->type</span>
                            <span class='location'>$prop->location</span>
                            <span class='street-address'>$prop->addr</span>
                            <span class='specs'>
                                <span class='capactiy' title='Capacity'><i class='bi bi-people-fill'></i> $prop->capacity</span>
                                <span class='parking' title='Parking'><i class='bi bi-p-square-fill'></i> " . ($prop->parking == 1 ? 'Yes' : 'No') . "</span>
                                <span class='pets' title='Pets'>🐱" . ($prop->pets == 1 ? 'Yes' : 'No') . "</span>
                            </span>
                            <span class='description'>$prop->description</span>
                            <span class='btn-bar'>
                                <a href='#' id='$prop->id-hist' data-prop-id='$prop->id' class='button-primary button-sm'><i class='bi bi-clock-history'></i> Tenants</a>
                                <a href='#' id='$prop->id-edit' data-prop-id='$prop->id' class='button-primary button-sm'><i class='bi bi-pencil-square'></i> Edit</a>
                                <a href='#' id='$prop->id-delete' data-prop-id='$prop->id' class='button-primary button-sm button-delete'><i class='bi bi-trash-fill'></i> Delete</a>
                            </span>
                        </div>
                    </div>";
                }
            }
            ?>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script>
        var userId = <?php echo $userId; ?>;
    </script>
    <script src="./my_properties.js"></script>
</body>

</html>