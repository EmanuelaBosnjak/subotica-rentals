<?php
session_start();

$loggedIn = isset($_SESSION['user_id']);
if (!$loggedIn) {
    header('Location:index.php');
    die();
}
$userId = $_SESSION['user_id'];

$returnUrl = $_GET['return-url'] ?? "index.php";

require_once 'db_config.php';

$adId = $_GET['ad-id'] ?? null;
if ($adId == null) {
    header("Location:$returnUrl");
    die();
}

$query = $dbh->prepare(
    "SELECT
        a.ad_id AS ad_id,
        a.user_id AS user_id,
        a.status AS `status`,
        a.rental_period AS `period`,
        a.rental_price AS price,
        a.description AS ad_desc,
        a.property_id AS prop_id,
        p.name AS `name`,
        t.type_name AS `type`,
        l.location_name AS `location`,
        p.street_address AS `address`,
        p.capacity AS capacity,
        p.parking AS parking,
        p.pets_allowed AS pets,
        p.description AS prop_desc
    FROM 
        advertisements a
    INNER JOIN properties p ON
        p.property_id = a.property_id
    INNER JOIN locations l ON
        p.location_id = l.location_id
    INNER JOIN property_types t ON
        p.type_id = t.type_id
    WHERE ad_id = $adId"
);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
if (empty($results)) {
    header("Location:$returnUrl");
    die();
}
$adData = $results[0];

if ($adData->status != 'PUBLIC' && $adData->user_id != $userId && !$_SESSION['is_admin']) {
    header("Location:$returnUrl");
    die();
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>View Advertisement - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>

    <style>
        main {
            height: 100vh;
            overflow-y: scroll;
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
                View Advertisement
            </h1>
            <span class='specs'>
                <span class='capactiy' title='Capacity'><i class='bi bi-people-fill'></i> <?php echo $adData->capacity; ?></span>
                <span class='parking' title='Parking'><i class='bi bi-p-square-fill'></i> <?php echo ($adData->parking == 1 ? 'Yes' : 'No'); ?></span>
                <span class='pets' title='Pets'>🐱 <?php echo ($adData->pets == 1 ? 'Yes' : 'No'); ?></span>
            </span>
            <a class="button-primary" id="cancel-btn" href="<?php echo $returnUrl ?>">Back</a>
        </div>
        <div class="info-group">
            <div class="title">Property Name</div>
            <div class="data big"><?php echo $adData->name; ?></div>
        </div>
        <div class="info-group">
            <div class="title">Type</div>
            <div class="data"><?php echo $adData->type; ?></div>
        </div>
        <div class="info-group">
            <div class="title">Location</div>
            <div class="data"><?php echo $adData->location; ?></div>
        </div>
        <div class="info-group">
            <div class="title">Street Address</div>
            <div class="data"><?php echo $adData->address; ?></div>
        </div>
        <div class="info-group">
            <div class="title">Property Description</div>
            <div class="data"><?php echo $adData->prop_desc; ?></div>
        </div>
        <div class="info-group">
            <div class="title">Rental Price</div>
            <div class="data big"><?php echo "RSD " . $adData->price . " per month"; ?></div>
        </div>
        <div class="info-group">
            <div class="title">Rental Period</div>
            <div class="data big"><?php echo ($adData->period == 0 ? 'Flexible Period' : "$adData->period Months"); ?></div>
        </div>
        <div class="info-group">
            <div class="title">Advertisement Description</div>
            <div class="data"><?php echo $adData->ad_desc; ?></div>
        </div>
        <div class="photos-row">
            <?php
            $photos = json_decode(file_get_contents("uploads/$adData->prop_id.json"));
            foreach ($photos as $photo) {
                echo "<img src='$photo'>";
            }
            ?>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script src="./post_advert.js"></script>
</body>

</html>