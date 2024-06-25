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

$propId = $_GET['property-id'] ?? null;
if ($propId == null) {
    header("Location:$returnUrl");
    die();
}

$query = $dbh->prepare(
    "SELECT
        u.first_name AS fname,
        u.last_name AS lname,
        u.email AS email,
        u.phone_number AS phone,
        r.rented_on AS rented_on,
        r.rental_period AS `period`,
        r.rental_price AS price
    FROM 
        rented_by r
    INNER JOIN user_accounts u ON
        r.user_id = u.user_id
    WHERE r.property_id = :propId"
);
$query->bindParam(':propId', $propId, PDO::PARAM_INT);
$query->execute();
$history = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <title>View Rental History - Subotica Rentals</title>
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
                View Rental History
            </h1>
            <a class="button-primary" id="cancel-btn" href="<?php echo $returnUrl ?>">Back</a>
        </div>
        <div class="rent-history-list">
            <?php
            if (empty($history)) {
                echo "<div id='no-hist-text'>No Tenants yet!</div>";
            } else {
                foreach ($history as $point) {
                    $period = ($point->period == 0 ? "Flexible" : "$point->period Months");
                    echo "<div class='item'>
                        <span class='name'>$point->fname $point->lname</span>
                        <span class='info'>
                            <span class='rented-on'>$point->rented_on</span>
                            <span class='period'>$period</span>
                            <span class='price'>$point->price</span>
                        </span>
                        <span class='contact-list'>
                            <a class='email' href='mailto:$point->email'>$point->email</a>
                            <a class='phone' href='mailto:$point->phone'>$point->phone</a>
                        </span>
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
    <script src="./post_advert.js"></script>
</body>

</html>