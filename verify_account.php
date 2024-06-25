<?php
$pw_hash = $_GET['key'] ?? null;
$verified = false;
if ($pw_hash != null) {
    require_once 'db_config.php';
    $sql = "SELECT * FROM user_accounts WHERE password_hash = :pw_hash";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pw_hash', $pw_hash, PDO::PARAM_STR_CHAR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if (!empty($results)) {
        $user_id = $results[0]->user_id;
        $sql = "UPDATE user_accounts SET account_state = 'ACTIVE' WHERE user_id = $user_id";
        $query = $dbh->prepare($sql);
        if ($query->execute()) {
            $verified = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification</title>
</head>

<body>
    <p>
        <?php echo ($verified) ? "Your account was verified successfully!" : "Verification unsuccessful"; ?>
    </p>
</body>

</html>