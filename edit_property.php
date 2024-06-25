<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        require_once 'db_config.php';
        $sql = 
        "UPDATE properties 
        SET
            `name` = :name,
            type_id = :type_id, 
            location_id = :location_id, 
            street_address = :street_addr, 
            capacity = :capacity, 
            parking = :parking, 
            pets_allowed = :pets_allowed, 
            `description` = :description
        WHERE
            property_id = :prop_id;";
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $formData['propName'], PDO::PARAM_STR);
        $query->bindParam(':type_id', $formData['propType'], PDO::PARAM_INT);
        $query->bindParam(':location_id', $formData['locationId'], PDO::PARAM_INT);
        $query->bindParam(':street_addr', $formData['streetAddr'], PDO::PARAM_STR);
        $query->bindParam(':capacity', $formData['capacity'], PDO::PARAM_INT);
        $query->bindParam(':parking', $formData['parking'], PDO::PARAM_BOOL);
        $query->bindParam(':pets_allowed', $formData['pets'], PDO::PARAM_BOOL);
        $query->bindParam(':description', $formData['description'], PDO::PARAM_STR);
        $query->bindParam(':prop_id', $formData['propId'], PDO::PARAM_INT);
        echo "will try to execute query now ";
        if ($query->execute()) {
            $lastId = $formData['propId'];
            $photosJsonStr = json_encode($formData['photos']);
            file_put_contents("uploads/$lastId.json", $photosJsonStr);
            $response['status'] = 200;
            $response['message'] = 'updated';
        } else {
            $response['status'] = 400;
            $response['message'] = 'bad data';
        }
        echo "query executed ig ";
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
    echo "not logged in";
    // header('Location:index.php');
    die();
}
$userId = $_SESSION['user_id'];

$returnUrl = $_GET['return-url'] ?? "index.php";

require_once 'db_config.php';
$query = $dbh->prepare("SELECT * FROM property_types");
$query->execute();
$propertyTypes = $query->fetchAll(PDO::FETCH_OBJ);
$query = $dbh->prepare("SELECT * FROM locations ORDER BY location_name ASC");
$query->execute();
$locations = $query->fetchAll(PDO::FETCH_OBJ);

$propertyId = $_GET['property-id'] ?? null;
if ($propertyId == null) {
    // echo "property id null";
    // var_dump($_GET);
    header("Location:$returnUrl");
    die();
}
$query = $dbh->prepare(
    "SELECT * FROM properties 
    WHERE user_id = $userId AND property_id = $propertyId"
);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
if (empty($results)) {
    // echo "prop id not found in table";
    header("Location:$returnUrl");
    die();
}
$propertyData = $results[0];
?>

<!doctype html>
<html lang="en">

<head>
    <title>Edit Property - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main style="height:100vh; overflow-y: scroll;">
        <h1>Edit Property</h1>
        <form id="prop-edit-form">
            <input id="user-id" value="<?php echo $_SESSION['user_id']; ?>" name="userId" style="display: none;">
            <input id="property-id" value="<?php echo $propertyId; ?>" name="propertyId" style="display: none;">
            <label for="prop-name">Name of Property</label>
            <input value="<?php echo $propertyData->name; ?>" id="prop-name" name="prop-name">
            <span class="input-error" id="prop-name-error"></span>
            <label for="prop-type">Type of Property</label>
            <select name="prop-type" id="prop-type">
                <option value="" hidden>Select a Type</option>
                <?php
                foreach ($propertyTypes as $type) {
                    $s = ($type->type_id == $propertyData->type_id ? 'selected' : '');
                    echo "<option value='$type->type_id' title='$type->description' $s>
                        $type->type_name
                        </option>";
                }
                ?>
            </select>
            <span class="input-error" id="prop-type-error"></span>
            <label for="location-id">Location in Subotica</label>
            <input value="<?php echo $propertyData->location_id; ?>" id="location-id" name="location-id" style="display: none;">
            <div class="location-selector">
                <?php
                if (empty($locations)) {
                    echo '<span id="no-loc-text">No Locations Available!</span>';
                } else {
                    foreach ($locations as $loc) {
                        $s = ($loc->location_id == $propertyData->location_id ? ' selected' : '');
                        echo "<div class='item$s' id='loc_$loc->location_id'>
                            $loc->iframe
                            </div>";
                    }
                }
                ?>
            </div>
            <span class="input-error" id="location-id-error"></span>
            <label for="street-addr">Street Address</label>
            <textarea id="street-addr" name="street-addr" rows="3"><?php echo $propertyData->street_address; ?></textarea>
            <span class="input-error" id="street-addr-error"></span>
            <label for="capacity">Capacity (Approx. No. of Residents)</label>
            <input id="capacity" value="<?php echo $propertyData->capacity; ?>" name="capacity" type="number">
            <span class="input-error" id="capacity-error"></span>
            <div class="checkbox-group">
                <input <?php echo ($propertyData->parking == 1 ? 'checked' : ''); ?> type="checkbox" id="parking" name="parking">
                <label for="parking">Parking Available?</label>
            </div>
            <div class="checkbox-group">
                <input <?php echo ($propertyData->pets_allowed == 1 ? 'checked' : ''); ?> type="checkbox" id="pets" name="pets">
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
            <textarea id="description" name="description" rows="3"><?php echo $propertyData->description; ?></textarea>
            <span class="input-error" id="description-error"></span>
        </form>
        <div style="display: flex; gap: 16px; margin-top:16px; flex-wrap:wrap;">
            <input class="button-primary" type="submit" form="prop-edit-form" value="Save">
            <div style="flex-grow: 1;"></div>
            <a class="button-primary" id="cancel-btn" href="<?php echo $returnUrl ?>">Cancel</a>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script src="./add_property.js"></script>
    <script>
        $('#no-photo-text').css('display', 'none');
        <?php
        $photos = json_decode(file_get_contents("uploads/$propertyId.json"));
        foreach ($photos as $photo) {
            $key = md5(rand());
            echo "uploadedPhotos.set('$key', '$photo');";
            echo "addImagePreview('$key', '$photo');";
        }
        ?>
        $('#prop-edit-form').submit(function(e) {
            e.preventDefault();
            let formData = {
                propId: $('#property-id').val(),
                userId: $('#user-id').val(),
                propName: $('#prop-name').val(),
                propType: $('#prop-type').val(),
                locationId: $('#location-id').val(),
                streetAddr: $('#street-addr').val(),
                capacity: parseInt($('#capacity').val()),
                parking: $('#parking').is(":checked"),
                pets: $('#pets').is(":checked"),
                photos: Array.from(uploadedPhotos.values()),
                description: $('#description').val(),
            };
            console.log(formData);
            isValid = true;
            if (formData.propName.length == 0) {
                isValid = false;
                $('#prop-name-error').text("Please enter a valid name.");
            } else if (formData.propName.length < 5) {
                isValid = false;
                $('#prop-name-error').text("The name must be at least 5 chars long.");
            }
            if (formData.propType == "") {
                isValid = false;
                $('#prop-type-error').text("Please select a property type.");
            }
            if (formData.locationId == -1) {
                isValid = false;
                $('#location-id-error').text("Please select a location.");
            }
            if (formData.streetAddr.length == 0) {
                isValid = false;
                $('#street-addr-error').text("Please enter a street address.");
            } else if (formData.streetAddr.length < 20) {
                isValid = false;
                $('#street-addr-error').text("The address must be at least 20 chars long.");
            }
            if (formData.capacity < 1 || formData.capacity > 15) {
                isValid = false;
                $('#capacity-error').text("Please enter a capacity between 1 and 15.");
            }
            if (formData.photos.length < 3) {
                isValid = false;
                $('#photos-error').text("Please select at least 3 images.");
            }
            if (formData.description.length < 50) {
                isValid = false;
                $('#description-error').text("The description must be at least 50 chars long.");
            }
            if (isValid) {
                console.log('form is valid');
                $.ajax({
                    type: "POST",
                    url: "edit_property.php",
                    data: {
                        formData: formData
                    },
                    dataType: 'json',
                }).done(function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        alert('Property modified successfully!');
                        $returnUrl = $('#cancel-btn').attr('href');
                        window.location.replace($returnUrl);
                    } else if (response.status == 400) {
                        alert('Invalid data entered, please check and try again!');
                    } else {
                        alert(`Unknown error occured, please try again after some time. ${response.message}`);
                    }
                });
            }
        });
    </script>
</body>

</html>