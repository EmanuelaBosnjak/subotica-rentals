<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        echo "entered if";
        require_once 'db_config.php';
        $query = $dbh->prepare(
            "SELECT
                a.user_id AS recp_user_id,
                a.rental_price AS price,
                a.rental_period AS `period`,
                a.description AS ad_desc,
                p.name AS `name`
            FROM
                advertisements a
            INNER JOIN properties p ON
                a.property_id = p.property_id
            WHERE
                ad_id = :ad_id"
        );
        $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
        $query->execute();
        $adData = $query->fetchAll(PDO::FETCH_OBJ)[0];
        $period = ($adData->period == 0 ? 'Flexible' : "$adData->period Months");
        $adInfo = "Property Name: $adData->name<br> Rental Price: $adData->price<br> Rental Period: $period<br> Description: $adData->ad_desc";
        $query = $dbh->prepare(
            "SELECT email, first_name, last_name FROM user_accounts WHERE user_id = :user_id"
        );
        $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
        $query->execute();
        $sender = $query->fetch(PDO::FETCH_OBJ);
        $sender_email = $sender->email;
        $sender_name = $sender->first_name . ' ' . $sender->last_name;
        $query = $dbh->prepare(
            "SELECT email, first_name, last_name FROM user_accounts WHERE user_id = :user_id"
        );
        $query->bindParam(':user_id', $adData->recp_user_id, PDO::PARAM_INT);
        $query->execute();
        $recp = $query->fetch(PDO::FETCH_OBJ);
        $recp_email = $recp->email;
        $recp_name = $recp->first_name . ' ' . $recp->last_name;
        require_once 'send_mail.php';
        echo json_encode([
            'sender_email' => $sender_email,
            'sender_name' => $sender_name,
            'recp_email' => $recp_email,
            'recp_name' => $recp_name,
            'ad_info' => $adInfo,
            'message' => $formData['message']
        ]);
        if (send_advert_contact_email($sender_email, $sender_name, 'nda@nda.stud.vts.su.ac.rs', $recp_name, $adInfo, $formData['message'])) {
            $response['status'] = 200;
            $response['message'] = 'sent';
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
    header('Location:index.php');
    die();
}

$query = $dbh->prepare(
    "SELECT
        p.name AS `name`
    FROM
        advertisements a
    INNER JOIN properties p ON
        a.property_id = p.property_id
    WHERE
        ad_id = $adId"
);
$query->execute();
$adData = $query->fetch(PDO::FETCH_OBJ);
$sampleMsg = "Hi, I'm interested in renting your property, $adData->name according to the advertisement you posted on Subotica Rentals.
Here are some details about me...";
?>

<!doctype html>
<html lang="en">

<head>
    <title>Send Contact Email - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main style="height:100vh; overflow-y: scroll;">
        <h1>Send Contact Email</h1>
        <form id="email-form">
            <input id="ad-id" value="<?php echo $adId; ?>" name="adId" style="display: none;">
            <input id="user-id" value="<?php echo $userId; ?>" name="userId" style="display: none;">
            <label for="message">Enter a Message</label>
            <textarea id="message" name="message" rows="3"><?php echo $sampleMsg; ?></textarea>
        </form>
        <div style="display: flex; gap: 16px; margin-top:16px; flex-wrap:wrap;">
            <input class="button-primary" type="submit" form="email-form" value="Send">
            <div style="flex-grow: 1;"></div>
            <a class="button-primary" id="cancel-btn" href="<?php echo $returnUrl ?>">Cancel</a>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script>
        $('#email-form').submit(function(e) {
            e.preventDefault();
            let formData = {
                adId: $('#ad-id').val(),
                userId: $('#user-id').val(),
                message: $('#message').val(),
            };
            console.log(formData);
            $.ajax({
                type: "POST",
                url: "advert_contact.php",
                data: {
                    formData: formData
                },
                dataType: 'json',
            }).done(function(response) {
                console.log(response);
                if (response.status == 200) {
                    alert('Email sent successfully!');
                    $returnUrl = $('#cancel-btn').attr('href');
                    // window.location.replace($returnUrl);
                } else if (response.status == 400) {
                    alert('Invalid data entered, please check and try again!');
                } else {
                    alert(`Unknown error occured, please try again after some time. ${response.message}`);
                }
            });
        })
    </script>
</body>

</html>