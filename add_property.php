<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        require_once 'db_config.php';
        $sql = "INSERT INTO properties 
        (user_id, `name`, type_id, location_id, street_address, capacity, parking, pets_allowed, `description`)
        VALUES
        (:user_id, :name, :type_id, :location_id, :street_address, :capacity, :parking, :pets_allowed, :description)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
        $query->bindParam(':name', $formData['propName'], PDO::PARAM_STR);
        $query->bindParam(':type_id', $formData['propType'], PDO::PARAM_INT);
        $query->bindParam(':location_id', $formData['locationId'], PDO::PARAM_INT);
        $query->bindParam(':street_address', $formData['streetAddr'], PDO::PARAM_STR);
        $query->bindParam(':capacity', $formData['capacity'], PDO::PARAM_INT);
        $query->bindParam(':parking', $formData['parking'], PDO::PARAM_BOOL);
        $query->bindParam(':pets_allowed', $formData['pets'], PDO::PARAM_BOOL);
        $query->bindParam(':description', $formData['description'], PDO::PARAM_STR);
        echo "will try to execute query now";
        if ($query->execute()) {
            $lastId = $dbh->lastInsertId();
            $photosJsonStr = json_encode($formData['photos']);
            file_put_contents("uploads/$lastId.json", $photosJsonStr);
            $response['status'] = 201;
            $response['message'] = 'created';
        } else {
            $response['status'] = 400;
            $response['message'] = 'bad data';
        }
        echo "query executed ig";
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
$query = $dbh->prepare("SELECT * FROM property_types");
$query->execute();
$propertyTypes = $query->fetchAll(PDO::FETCH_OBJ);
$query = $dbh->prepare("SELECT * FROM locations ORDER BY location_name ASC");
$query->execute();
$locations = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Add Property - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main style="height:100vh; overflow-y: scroll;">
        <h1>Add Property</h1>
        <form id="prop-form">
            <input id="user-id" value="<?php echo $_SESSION['user_id']; ?>" name="userId" style="display: none;">
            <label for="prop-name">Name of Property</label>
            <input id="prop-name" name="prop-name">
            <span class="input-error" id="prop-name-error"></span>
            <label for="prop-type">Type of Property</label>
            <select name="prop-type" id="prop-type">
                <option value="" hidden>Select a Type</option>
                <?php
                foreach ($propertyTypes as $type) {
                    echo "<option value='$type->type_id' title='$type->description'>
                        $type->type_name
                        </option>";
                }
                ?>
            </select>
            <span class="input-error" id="prop-type-error"></span>
            <label for="location-id">Location in Subotica</label>
            <input value="-1" id="location-id" name="location-id" style="display: none;">
            <div class="location-selector">
                <?php
                if (empty($locations)) {
                    echo '<span id="no-loc-text">No Locations Available!</span>';
                } else {
                    foreach ($locations as $loc) {
                        echo "<div class='item' id='loc_$loc->location_id'>
                            $loc->iframe
                            </div>";
                    }
                }
                ?>
            </div>
            <span class="input-error" id="location-id-error"></span>
            <label for="street-addr">Street Address</label>
            <textarea id="street-addr" name="street-addr" rows="3"></textarea>
            <span class="input-error" id="street-addr-error"></span>
            <label for="capacity">Capacity (Approx. No. of Residents)</label>
            <input id="capacity" value=1 name="capacity" type="number">
            <span class="input-error" id="capacity-error"></span>
            <div class="checkbox-group">
                <input type="checkbox" id="parking" name="parking">
                <label for="parking">Parking Available?</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="pets" name="pets">
                <label for="pets">Pets Allowed?</label>
            </div>
            <div style="display: flex; justify-content:space-between; flex-wrap:wrap;">
                <label for="photos">Add Photos (min. 3, max. 10)</label>
                <input multiple type="file" id="add-photo-input" accept="image/*" style="display: none;">
                <a id="add-photo-btn" class="button-primary button-sm" href="#"><i class="bi bi-upload"></i> Add Photo</a>
            </div>
            <input id="photos" name="photos" style="display: none;">
            <div class="photo-uploader">
                <span id="no-photo-text">Please add at least 3 photos</span>
                <!-- <div class="item">
                    <img src="./images/sample_property_0.webp">
                    <a class="remove-btn" href="#"><i class="bi bi-x-square-fill"></i></a>
                </div> -->
            </div>
            <span class="input-error" id="photos-error"></span>
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
            <span class="input-error" id="description-error"></span>
        </form>
        <div style="display: flex; gap: 16px; margin-top:16px; flex-wrap:wrap;">
            <input class="button-primary" type="submit" form="prop-form" value="Save">
            <div style="flex-grow: 1;"></div>
            <a class="button-primary" id="cancel-btn" href="<?php echo $returnUrl ?>">Cancel</a>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script src="./add_property.js"></script>
</body>

</html>