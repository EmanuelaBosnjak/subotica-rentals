<?php
$loginData = $_POST['loginData'] ?? null;
$registerData = $_POST['registerData'] ?? null;
if ($loginData != null || $registerData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    try {
        require_once 'db_config.php';
        if ($loginData != null) {
            $email = $loginData['email'];
            $password = $loginData['password'];
            $sql = 'SELECT * FROM user_accounts WHERE email=:email';
            $query = $dbh->prepare($sql);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            if (!empty($results)) {
                if (password_verify($password, $results[0]->password_hash)) {
                    $state = $results[0]->account_state;
                    $response['message'] = $state;
                    if ($state == 'ACTIVE' || $state == 'ADMIN') {
                        session_start();
                        $_SESSION['user_id'] = $results[0]->user_id;
                        $_SESSION['is_admin'] = ($state == 'ADMIN') ? true : false;
                        $_SESSION['login_time'] = time();
                        $response['status'] = 200;
                        $response['data'] = $results;
                    } else if ($state == 'NOT_VERIFIED' || $state == 'BLOCKED') {
                        $response['status'] = 403;
                    }
                } else {
                    $response['status'] = 403;
                    $response['message'] = 'wrong password';
                }
            } else {
                $response['status'] = 404;
                $response['message'] = 'email not found';
            }
        }
        if ($registerData != null) {
            $firstName = $registerData['firstName'];
            $lastName = $registerData['lastName'];
            $phone = $registerData['phone'];
            $email = $registerData['email'];
            $password = $registerData['password'];
            $sql = 'INSERT INTO user_accounts 
            (email, password_hash, first_name, last_name, phone_number) VALUES 
            (:email, :password_hash, :first_name, :last_name, :phone_number)';
            $query = $dbh->prepare($sql);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $pwHash = password_hash($password, PASSWORD_BCRYPT);
            $query->bindParam(':password_hash', $pwHash, PDO::PARAM_STR_CHAR);
            $query->bindParam(':first_name', $firstName, PDO::PARAM_STR);
            $query->bindParam(':last_name', $lastName, PDO::PARAM_STR);
            $query->bindParam(':phone_number', $phone, PDO::PARAM_INT);
            if ($query->execute()) {
                $response['status'] = 201;
                $response['message'] = 'registered';
                require_once 'send_mail.php';
                send_verify_email('nda@nda.stud.vts.su.ac.rs', "http://" . $_SERVER['HTTP_HOST'] . "/verify_account.php?key=$pwHash");
                // $response['verify_link'] = "http://" . $_SERVER['HTTP_HOST'] . "/verify_account.php?key=$pwHash";
            } else {
                $response['status'] = 400;
                $response['message'] = 'corrupt data';
            }
        }
    } finally {
        echo json_encode($response);
        die();
    }
}
?>

<div class="user-popup">
    <div class="popup-box">
        <div class="tab-bar">
            <div class="tab active" id="login-tab"><i class="bi bi-box-arrow-in-right"></i> Login</div>
            <div class="tab" id="register-tab"><i class="bi bi-person-plus"></i> Register</div>
            <span id="close-btn"><i class="bi bi-x-lg"></i></span>
        </div>
        <form id="login-form">
            <div class="inp-group">
                <label for="login-email">Email</label>
                <input id="login-email" name="email" type="text">
                <span class="input-error" id="login-email-error"></span>
            </div>
            <div class="inp-group">
                <label for="login-password">Password</label>
                <input id="login-password" name="password" type="password">
                <span class="input-error" id="login-password-error"></span>
            </div>
            <span class="result" id="login-result">Something happened</span>
            <div class="inp-group" style="display: flex; justify-content:end;">
                <a id="forgot-pw-btn" href="#">Forgot Password?</a>
            </div>
            <input type="submit" value="Login">
        </form>
        <form id="register-form" style="display: none;">
            <div class="inp-group">
                <label for="first-name">First Name</label>
                <input id="first-name" name="first-name" type="text">
                <span class="input-error" id="first-name-error"></span>
            </div>
            <div class="inp-group">
                <label for="last-name">Last Name</label>
                <input id="last-name" name="last-name" type="text">
                <span class="input-error" id="last-name-error"></span>
            </div>
            <div class="inp-group">
                <label for="phone">Phone Number</label>
                <div style="display: flex;">
                    <span style="padding: 12px 10px 12px 0;">+381</span>
                    <input id="phone" name="phone" type="number">
                </div>
                <span class="input-error" id="phone-error"></span>
            </div>
            <div class="inp-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="text">
                <span class="input-error" id="email-error"></span>
            </div>
            <div class="inp-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password">
                <span class="input-error" id="password-error"></span>
            </div>
            <div class="inp-group">
                <label for="cnf-password">Confirm Password</label>
                <input id="cnf-password" name="password" type="password">
                <span class="input-error" id="cnf-password-error"></span>
            </div>
            <span class="result" id="register-result">Something happened</span>
            <input type="submit" value="Register">
        </form>
    </div>
</div>
<script src="./login_popup.js"></script>