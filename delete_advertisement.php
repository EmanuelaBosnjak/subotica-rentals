<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        require_once 'db_config.php';
        $query = $dbh->prepare("SELECT * FROM user_accounts WHERE user_id = :u AND account_state = 'ADMIN'");
        $query->bindParam(':u', $formData['userId'], PDO::PARAM_INT);
        $query->execute();
        $isAdmin = ($query->rowCount() > 0);
        if (!$isAdmin) {
            $sql = 
            "DELETE FROM advertisements 
            WHERE user_id = :user_id AND ad_id = :ad_id;";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
            $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
        } else {
            $sql = 
            "DELETE FROM advertisements 
            WHERE ad_id = :ad_id;";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ad_id', $formData['adId'], PDO::PARAM_INT);
        }
        $query->execute();
        if ($query->rowCount() > 0) {
            $response['status'] = 200;
            $response['message'] = 'deleted';
        } else {
            $response['status'] = 400;
            $response['message'] = 'bad data';
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