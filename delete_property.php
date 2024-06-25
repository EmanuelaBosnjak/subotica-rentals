<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        require_once 'db_config.php';
        $sql = 
        "DELETE FROM properties 
        WHERE user_id = :user_id AND property_id = :prop_id;";
        $query = $dbh->prepare($sql);
        $query->bindParam(':prop_id', $formData['propId'], PDO::PARAM_INT);
        $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
        echo "will try to execute query now ";
        $query->execute();
        if ($query->rowCount() > 0) {
            unlink("uploads/$propId.json");
            $response['status'] = 200;
            $response['message'] = 'deleted';
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