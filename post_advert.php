<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        $task = $formData['task'] ?? null;
        echo "task = $task\n";
        if ($task == 'publish' || $task == 'draft') {
            echo "entered if";
            require_once 'db_config.php';
            $sql = "INSERT INTO advertisements
            (user_id, property_id, status, rental_period, rental_price, description)
            VALUES
            (:user_id, :property_id, :status, :rental_period, :rental_price, :description)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
            $query->bindParam(':property_id', $formData['propertyId'], PDO::PARAM_INT);
            $status = ($task == 'publish' ? 'PENDING' : 'DRAFT');
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':rental_period', $formData['rentalPeriod'], PDO::PARAM_INT);
            $query->bindParam(':rental_price', $formData['rentalPrice'], PDO::PARAM_INT);
            $query->bindParam(':description', $formData['description'], PDO::PARAM_STR);
            echo "will try to execute query now";
            if ($query->execute()) {
                $response['status'] = 201;
                $response['message'] = 'created';
            } else {
                $response['status'] = 400;
                $response['message'] = 'bad data';
            }
            echo "query executed hehe";
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
if (!$loggedIn) {
    header('Location:index.php');
    die();
}

$returnUrl = $_GET['return-url'] ?? "index.php";

require_once 'db_config.php';
$userId = $_SESSION['user_id'];
$query = $dbh->prepare(
    "SELECT
        p.property_id AS id,
        p.name AS name,
        t.type_name AS type,
        l.location_name AS location
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
    <title>Post Advertisement - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main style="height:100vh; overflow-y: scroll;">
        <h1>Post Advertisement</h1>
        <form id="ad-form">
            <input id="user-id" value="<?php echo $_SESSION['user_id']; ?>" name="userId" style="display: none;">
            <div style="display: flex; justify-content:space-between; flex-wrap:wrap;">
                <label for="property-id">Select Property to Advertise</label>
                <a class="button-primary button-sm" href="add_property.php?return-url=post_advert.php"><i class="bi bi-house-heart"></i> Add Property</a>
            </div>
            <input id="property-id" value="-1" name="property-id" style="display: none;">
            <div class="property-selector">
                <?php
                if (empty($properties)) {
                    echo '<span id="no-prop-text">No Properties added yet!</span>';
                } else {
                    foreach ($properties as $prop) {
                        $allJson = file_get_contents("uploads/$prop->id.json");
                        $photo = json_decode($allJson)[0];
                        $query = $dbh->prepare("SELECT * FROM advertisements WHERE property_id = :prop_id;");
                        $query->bindParam(':prop_id', $prop->id, PDO::PARAM_INT);
                        $query->execute();
                        $posted = ($query->rowCount() > 0 ? ' posted' : '');
                        echo
                        "<div id='prop_$prop->id' class='item$posted'>
                            <img src='$photo'>
                            <span class='name'>$prop->name</span>
                            <span class='type'>$prop->type</span>
                            <span class='location'>$prop->location</span>
                        </div>";
                    }
                }
                ?>
            </div>
            <span class="input-error" id="property-id-error"></span>
            <label for="rental-period">Rental Period (in Months)</label>
            <input title="Enter 0 for flexible period" value="0" id="rental-period" name="rental-period" type="number">
            <span class="input-error" id="rental-period-error"></span>
            <label for="rental-price">Rental Price (per month)</label>
            <input id="rental-price" name="rental-price" type="number">
            <span class="input-error" id="rental-price-error"></span>
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
            <span class="input-error" id="description-error"></span>
        </form>
        <div style="display: flex; gap: 16px; margin-top:16px; flex-wrap:wrap;">
            <input class="button-primary" type="submit" form="ad-form" value="Publish">
            <a id="save-draft-btn" class="button-primary" href="#">Save Draft</a>
            <div style="flex-grow: 1;"></div>
            <a class="button-primary" id="cancel-btn" href="<?php echo $returnUrl ?>">Cancel</a>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script src="./post_advert.js"></script>
</body>

</html>