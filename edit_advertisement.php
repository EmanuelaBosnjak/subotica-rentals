<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        $task = $formData['task'] ?? null;
        echo "task = $task\n";
        require_once 'db_config.php';
        $query = $dbh->prepare("SELECT * FROM user_accounts WHERE user_id = :user_id AND account_state = 'ADMIN'");
        $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_STR);
        $query->execute();
        $isAdmin = ($query->rowCount() > 0);
        $query = $dbh->prepare("SELECT `status` AS s FROM advertisements WHERE ad_id = :ad_id");
        $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
        $query->execute();
        $currentStatus = $query->fetch(PDO::FETCH_OBJ);
        if ($currentStatus->s == 'BLOCKED' && !$isAdmin) {
            $response['status'] = 403;
            $response['message'] = 'unauthorized';
        } else if ($task == 'publish' || $task == 'draft') {
            echo "entered if";
            $sql = "UPDATE advertisements
            SET
                property_id = :property_id, 
                status = :status, 
                rental_period = :rental_period, 
                rental_price = :rental_price, 
                description = :description
            WHERE
                ad_id = :ad_id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':property_id', $formData['propertyId'], PDO::PARAM_INT);
            $status = ($task == 'publish' ? 'PENDING' : 'DRAFT');
            if ($isAdmin) $status = 'PUBLIC';
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':rental_period', $formData['rentalPeriod'], PDO::PARAM_INT);
            $query->bindParam(':rental_price', $formData['rentalPrice'], PDO::PARAM_INT);
            $query->bindParam(':description', $formData['description'], PDO::PARAM_STR);
            $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
            echo "will try to execute query now";
            if ($query->execute()) {
                $response['status'] = 200;
                $response['message'] = 'updated';
            } else {
                $response['status'] = 400;
                $response['message'] = 'bad data';
            }
        } else if ($task == 'search_tenant') {
            echo "entered search tenant";
            $query = $dbh->prepare(
                "SELECT * FROM user_accounts
                WHERE email = :email"
            );
            $query->bindParam(':email', $formData['email'], PDO::PARAM_STR);
            $query->execute();
            if ($query->rowCount() > 0) {
                $response['status'] = 200;
                $response['message'] = 'found';
            } else {
                $response['status'] = 404;
                $response['message'] = 'email not found';
            }
        } else if ($task == 'mark_rented') {
            echo "entered mark rented";
            $query = $dbh->prepare(
                "SELECT user_id, first_name, last_name FROM user_accounts
                WHERE email = :email"
            );
            $query->bindParam(':email', $formData['tenantEmail'], PDO::PARAM_STR);
            $query->execute();
            if ($query->rowCount() <= 0) {
                $response['status'] = 404;
                $response['message'] = 'email not found';
            } else {
                $tenantResult = $query->fetch(PDO::FETCH_OBJ);
                $tenantUserId = $tenantResult->user_id;
                $recp_email = $formData['tenantEmail'];
                $recp_name = $tenantResult->first_name . ' ' . $tenantResult->last_name;
                $query = $dbh->prepare(
                    "SELECT 
                        p.name AS `name`,
                        a.property_id AS prop_id,
                        a.rental_price AS price,
                        a.rental_period AS `period`,
                        a.description AS ad_desc
                    FROM 
                        advertisements a
                    INNER JOIN properties p ON
                        p.property_id = a.property_id
                    WHERE ad_id = :ad_id"
                );
                $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
                $query->execute();
                $adData = $query->fetch(PDO::FETCH_OBJ);
                $query = $dbh->prepare(
                    "UPDATE advertisements SET
                    status = 'RENTED' 
                    WHERE ad_id = :ad_id"
                );
                $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
                $query->execute();
                $query = $dbh->prepare(
                    "INSERT INTO rented_by
                    (property_id, user_id, rental_period, rental_price)
                    VALUES (:prop_id, :tenant_id, :period, :price)"
                );
                $query->bindParam(':prop_id', $adData->prop_id, PDO::PARAM_INT);
                $query->bindParam(':tenant_id', $tenantUserId, PDO::PARAM_INT);
                $query->bindParam(':period', $adData->period, PDO::PARAM_INT);
                $query->bindParam(':price', $adData->price, PDO::PARAM_INT);
                if ($query->execute()) {
                    require_once 'send_mail.php';
                    $query = $dbh->prepare(
                        "SELECT email, first_name, last_name FROM user_accounts
                        WHERE user_id = :userId"
                    );
                    $query->bindParam(':userId', $formData['userId'], PDO::PARAM_INT);
                    $query->execute();
                    $senderResult = $query->fetch(PDO::FETCH_OBJ);
                    $sender_email = $senderResult->email;
                    $sender_name = $senderResult->first_name . ' ' . $senderResult->last_name;
                    $adInfo = "Property Name: $adData->name<br> Rental Price: $adData->price<br> Rental Period: $period<br> Description: $adData->ad_desc";
                    send_tenant_email($sender_email, $sender_name, 'nda@nda.stud.vts.su.ac.rs', $recp_name, $adInfo);
                    $response['status'] = 200;
                    $response['message'] = 'updated';
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'bad data';
                }
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

$sql = "SELECT * FROM advertisements
    WHERE ad_id = $adId AND user_id = $userId";
if ($_SESSION['is_admin']) {
    $sql = "SELECT * FROM advertisements
    WHERE ad_id = $adId";
}
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
if (empty($results)) {
    header("Location:$returnUrl");
    die();
}
$adData = $results[0];

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
        user_id = $adData->user_id"
);
$query->execute();
$properties = $query->fetchAll(PDO::FETCH_OBJ);

$isOwner = ($userId == $adData->user_id);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Edit Advertisement - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>

        <div class="user-popup">
            <div class="popup-box">
                <div class="tab-bar">
                    <span class="title">Search for Tenant Email</span>
                    <span id="close-btn"><i class="bi bi-x-lg"></i></span>
                </div>
                <form id="tenant-form">
                    <div class="inp-group">
                        <label for="search-email">Tenant Email</label>
                        <input id="search-email" type="search" name="search-email">
                        <span class="input-error" id="search-email-error"></span>
                    </div>
                    <input id="tenant-submit" type="submit" value="Search">
                </form>
            </div>
        </div>
    </header>

    <main style="height:100vh; overflow-y: scroll;">
        <h1>Edit Advertisement</h1>
        <form id="ad-edit-form">
            <input id="ad-id" value="<?php echo $adId; ?>" name="adId" style="display: none;">
            <input id="user-id" value="<?php echo $userId; ?>" name="userId" style="display: none;">
            <div style="display: flex; justify-content:space-between; flex-wrap:wrap;">
                <label for="property-id">Select Property to Advertise</label>
                <?php
                if ($isOwner) {
                    echo '<a class="button-primary button-sm" href="add_property.php?return-url=post_advert.php"><i class="bi bi-house-heart"></i> Add Property</a>';
                }
                ?>
            </div>
            <input id="property-id" value="<?php echo $adData->property_id; ?>" name="property-id" style="display: none;">
            <div class="property-selector">
                <?php
                if (empty($properties)) {
                    echo '<span id="no-prop-text">No Properties added yet!</span>';
                } else {
                    foreach ($properties as $prop) {
                        $allJson = file_get_contents("uploads/$prop->id.json");
                        $photo = json_decode($allJson)[0];
                        $s = ($prop->id == $adData->property_id ? ' selected' : '');
                        $query = $dbh->prepare("SELECT * FROM advertisements WHERE property_id = :prop_id;");
                        $query->bindParam(':prop_id', $prop->id, PDO::PARAM_INT);
                        $query->execute();
                        $posted = ($query->rowCount() > 0 ? ' posted' : '');
                        if ($s != '') $posted = '';
                        echo
                        "<div id='prop_$prop->id' class='item$s$posted'>
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
            <input title="Enter 0 for flexible period" value="<?php echo $adData->rental_period; ?>" id="rental-period" name="rental-period" type="number">
            <span class="input-error" id="rental-period-error"></span>
            <label for="rental-price">Rental Price (per month)</label>
            <input id="rental-price" value="<?php echo $adData->rental_price; ?>" name="rental-price" type="number">
            <span class="input-error" id="rental-price-error"></span>
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?php echo $adData->description; ?></textarea>
            <span class="input-error" id="description-error"></span>
        </form>
        <div style="display: flex; gap: 16px; margin-top:16px; flex-wrap:wrap;">
            <input class="button-primary" type="submit" form="ad-edit-form" value="Publish">
            <?php
            if (!$isAdmin) {
                echo '<a id="edit-draft-btn" class="button-primary" href="#">Save Draft</a>';
            }
            ?>
            <a id="mark-rented-btn" class="button-primary" href="#">Mark as Rented</a>
            <div style="flex-grow: 1;"></div>
            <a class="button-primary" id="cancel-btn" href="<?php echo $returnUrl ?>">Cancel</a>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script src="./post_advert.js"></script>
    <script>
        $('#close-btn').on('click', function(e) {
            root.style.setProperty('--modal-blur', '0px');
            root.style.setProperty('--user-popup-opactiy', '0');
            root.style.setProperty('--user-popup-events', 'none');
        });

        $('.user-popup').on('click', function(e) {
            if (e.target == e.currentTarget) {
                root.style.setProperty('--modal-blur', '0px');
                root.style.setProperty('--user-popup-opactiy', '0');
                root.style.setProperty('--user-popup-events', 'none');
            }
        });

        $('#mark-rented-btn').click(function(e) {
            e.preventDefault();
            root.style.setProperty('--modal-blur', '5px');
            root.style.setProperty('--user-popup-opactiy', '1');
            root.style.setProperty('--user-popup-events', 'unset');
        });

        var tenantFound = false;
        var searching = false;

        function searchTenant() {
            if (searching) return;
            let email = $('#search-email').val();
            if (!validateEmail(email)) {
                $('#search-email-error').text("Please enter a valid email.");
            } else {
                $('#search-email-error').text("");
                let formData = {
                    email: email,
                    task: 'search_tenant',
                };
                console.log(formData);
                $('#tenant-submit').val('Searching...');
                $('#tenant-submit').prop('disabled', true);
                $('#search-email').prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "edit_advertisement.php",
                    data: {
                        formData: formData
                    },
                    dataType: 'json',
                }).done(function(response) {
                    console.log(response);
                    searching = false;
                    $('#tenant-submit').prop('disabled', false);
                    $('#search-email').prop('disabled', false);
                    if (response.status == 200) {
                        tenantFound = true;
                        $('#search-email-error').css('color', 'green');
                        $('#search-email-error').text("Email found.");
                        $('#tenant-submit').val('Mark as Rented');
                    } else if (response.status == 404) {
                        $('#search-email-error').text("Email not found.");
                        $('#tenant-submit').val('Search');
                    } else {
                        alert(`Unknown error occurred, please try again after some time. ${response.message}`);
                    }
                });
            }
        }

        $('#search-email').on('input', function(e) {
            if (tenantFound) {
                $('#search-email-error').text("");
                $('#search-email-error').css('color', 'red');
                $('#tenant-submit').val('Search');
                tenantFound = false;
            }
        });

        $('#tenant-form').submit(function(e) {
            e.preventDefault();
            if (!tenantFound) {
                searchTenant();
                return;
            }
            let formData = {
                adId: $('#ad-id').val(),
                userId: $('#user-id').val(),
                tenantEmail: $('#search-email').val(),
                task: 'mark_rented',
            };
            console.log(formData);
            $.ajax({
                type: "POST",
                url: "edit_advertisement.php",
                data: {
                    formData: formData
                },
                dataType: 'json',
            }).done(function(response) {
                console.log(response);
                if (response.status == 200) {
                    alert('Advertisement successfully marked as Rented!');
                    $returnUrl = $('#cancel-btn').attr('href');
                    window.location.replace($returnUrl);
                } else if (response.status == 400) {
                    alert('Invalid data entered, please check and try again!');
                } else {
                    alert(`Unknown error occurred, please try again after some time. ${response.message}`);
                }
            });
        });

        $('#ad-edit-form').submit(function(e) {
            e.preventDefault();
            let formData = {
                adId: $('#ad-id').val(),
                userId: $('#user-id').val(),
                propertyId: $('#property-id').val(),
                task: 'publish',
                rentalPeriod: $('#rental-period').val(),
                rentalPrice: $('#rental-price').val(),
                description: $('#description').val(),
            };
            console.log(formData);
            isValid = true;
            if (formData.propertyId == -1) {
                isValid = false;
                $('#property-id-error').text("Please select a property to advertise!");
            }
            if (formData.rentalPeriod < 0) {
                isValid = false;
                $('#rental-period-error').text("The period should be 0 or more.");
            }
            if (formData.rentalPrice <= 0) {
                isValid = false;
                $('#rental-price-error').text("The price should be positive.");
            }
            if (formData.description.length < 50) {
                isValid = false;
                $('#description-error').text("The description must be at least 50 chars long.");
            }
            if (isValid) {
                console.log("form is valid");
                $.ajax({
                    type: "POST",
                    url: "edit_advertisement.php",
                    data: {
                        formData: formData
                    },
                    dataType: 'json',
                }).done(function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        alert('Advertisement modified successfully!');
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

        $('#edit-draft-btn').click(function(e) {
            e.preventDefault();
            let formData = {
                adId: $('#ad-id').val(),
                userId: $('#user-id').val(),
                propertyId: $('#property-id').val(),
                task: 'draft',
                rentalPeriod: $('#rental-period').val(),
                rentalPrice: $('#rental-price').val(),
                description: $('#description').val(),
            };
            console.log(formData);
            isValid = true;
            if (formData.propertyId == -1) {
                isValid = false;
                $('#property-id-error').text("Please select a property to advertise!");
            }
            if (formData.rentalPeriod < 0) {
                isValid = false;
                $('#rental-period-error').text("The period should be 0 or more.");
            }
            if (formData.rentalPrice <= 0) {
                isValid = false;
                $('#rental-price-error').text("The price should be positive.");
            }
            if (formData.description.length < 50) {
                isValid = false;
                $('#description-error').text("The description must be at least 50 chars long.");
            }
            if (isValid) {
                console.log("form is valid");
                $.ajax({
                    type: "POST",
                    url: "edit_advertisement.php",
                    data: {
                        formData: formData
                    },
                    dataType: 'json',
                }).done(function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        alert('Advertisement draft modified successfully!');
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