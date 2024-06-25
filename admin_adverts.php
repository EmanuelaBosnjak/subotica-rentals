<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        $task = $formData['task'];
        require_once 'db_config.php';
        if ($task == 'approve' || $task == 'block') {
            $query = $dbh->prepare(
                "SELECT * FROM user_accounts 
                WHERE user_id = :user_id AND account_state = 'ADMIN'"
            );
            $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                $status = ($task == 'approve' ? 'PUBLIC' : 'BLOCKED');
                $query = $dbh->prepare(
                    "UPDATE advertisements SET
                    status = '$status' WHERE ad_id = :ad_id"
                );
                $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
                if ($query->execute()) {
                    $response['status'] = 200;
                    $response['message'] = 'updated';
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'bad data';
                }
            } else {
                $response['status'] = 403;
                $response['message'] = 'unauthorized';
            }
        } else {
            $response['status'] = 400;
            $response['message'] = 'invalid data';
        }
    } catch (Exception $e) {
        echo $e;
    } finally {
        $response['echo'] = ob_get_contents();
        ob_end_clean();
        echo json_encode($response);
        die();
    }
}

session_start();

$loggedIn = isset($_SESSION['user_id']);
if (!$loggedIn || !$_SESSION['is_admin']) {
    header('Location:index.php');
    die();
}

require_once 'db_config.php';
$userId = $_SESSION['user_id'];
$query = $dbh->prepare(
    "SELECT
        a.ad_id AS id,
        p.property_id AS property_id,
        p.name AS name,
        t.type_name AS type,
        l.location_name AS location,
        p.street_address AS addr,
        p.capacity AS capacity,
        p.parking AS parking,
        p.pets_allowed AS pets,
        a.status AS status,
        a.rental_period AS period,
        a.rental_price AS price,
        a.description AS description
    FROM
        advertisements a
    INNER JOIN properties p ON
        p.property_id = a.property_id
    INNER JOIN property_types t ON
        t.type_id = p.type_id
    INNER JOIN locations l ON
        l.location_id = p.location_id
    INNER JOIN user_accounts u ON
        u.user_id = a.user_id
    WHERE
        a.status = 'PENDING'"
);
$query->execute();
$adverts = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Pending Advertisements - Subotica Rentals</title>
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
                Approval Pending Advertisements
            </h1>
        </div>
        <div class="advert-list">
            <?php
            if (empty($adverts)) {
                echo '<span id="no-ads-text">All caught up!</span>';
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
                    echo
                    "<div class='item'>
                        <div class='photo-reel'>
                            $photosStr
                            <a href='#' class='prev-btn'><i class='bi bi-arrow-left'></i></a>
                            <a href='#' class='next-btn'><i class='bi bi-arrow-right'></i></a>
                        </div>
                        <div class='info'>
                            <span class='name'>$ad->name</span>
                            <span class='type'>$ad->type</span>
                            <span class='location'>$ad->location</span>
                            <span class='steet-addr'>$ad->addr</span>
                            <span class='specs'>
                                <span class='capacity' title='Capacity'><i class='bi bi-people-fill'></i> $ad->capacity</span>
                                <span class='parking' title='Parking'><i class='bi bi-p-square-fill'></i> " . ($ad->parking == 1 ? 'Yes' : 'No') . "</span>
                                <span class='pets' title='Pets'>🐱" . ($ad->pets == 1 ? 'Yes' : 'No') . "</span>
                            </span>
                            <span class='price-period'>RSD $ad->price p/m, " . ($ad->period == 0 ? 'Flexible Period' : "$ad->period Months") . "</span>
                            <span class='description'>$ad->description</span>
                            <span class='status $ad->status'></span>
                            <span class='btn-bar'>
                                <a href='#' id='$ad->id-view' data-ad-id='$ad->id' class='button-primary button-sm'><i class='bi bi-eye-fill'></i> View</a>
                                <a href='#' id='$ad->id-approve' data-ad-id='$ad->id' class='button-primary button-sm'><i class='bi bi-check2-all'></i> Approve</a>
                                <a href='#' id='$ad->id-block' data-ad-id='$ad->id' class='button-primary button-sm button-delete'><i class='bi bi-ban'></i> Block</a>
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
    <script src="./admin_adverts.js"></script>
</body>

</html>