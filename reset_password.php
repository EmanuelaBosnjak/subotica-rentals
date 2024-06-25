<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        $task = $formData['task'] ?? null;
        require_once 'db_config.php';
        if ($task == 'reset') {
            $user_id = $formData['userId'];
            $hash = $formData['hash'];
            $password = $formData['password'];
            $query = $dbh->prepare("SELECT password_hash AS pw_hash FROM user_accounts WHERE user_id = :u");
            $query->bindParam(':u', $user_id, PDO::PARAM_INT);
            $query->execute();
            $pw_hash = $query->fetch(PDO::FETCH_OBJ)->pw_hash;
            if (password_verify($pw_hash, $hash)) {
                $new_hash = password_hash($password, PASSWORD_BCRYPT);
                $query = $dbh->prepare("UPDATE user_accounts SET password_hash = :h WHERE user_id = :u");
                $query->bindParam(':h', $new_hash, PDO::PARAM_STR_CHAR);
                $query->bindParam(':u', $user_id, PDO::PARAM_INT);
                if ($query->execute()) {
                    $response['status'] = 200;
                    $response['message'] = 'reset';
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'bad data';
                }
            } else {
                $response['status'] = 403;
                $response['message'] = 'hash mismatch';
            }
        } else if ($task == 'send') {
            echo 'entered send';
            $email = $formData['email'];
            $query = $dbh->prepare("SELECT user_id, password_hash AS pw_hash FROM user_accounts WHERE email = :e;");
            $query->bindParam(':e', $email, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            $pw_hash = $result->pw_hash;
            $user_id = $result->user_id;
            require_once 'send_mail.php';
            $reset_url = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?key=$pw_hash&user-id=$user_id";
            // echo " reset_url = " . $reset_url;
            if (send_password_reset_email('nda@nda.stud.vts.su.ac.rs', $reset_url)) {
                $response['status'] = 200;
                $response['message'] = 'sent';
            }
        } else {
            $response['status'] = 400;
            $response['message'] = 'invalid data';
        }
    }catch (Exception $e) {
        echo $e;
    } finally {
        $response['echo'] = ob_get_contents();
        ob_end_clean();
        echo json_encode($response);
        die();
    }
}

$pw_hash = $_GET['key'] ?? null;
$user_id = $_GET['user-id'] ?? null;
if ($pw_hash == null || $user_id == null) {
    echo "<h1>Missing Data</h1>";
    die();
}

$hash = password_hash($pw_hash, PASSWORD_DEFAULT);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Reset Password - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main style="height:100vh; overflow-y: scroll;">
        <h1>Reset your Password</h1>
        <form id="pw-reset-form">
            <input id="user-id" value="<?php echo $user_id; ?>" style="display: none;">
            <input id="hash" value="<?php echo $hash; ?>" style="display: none;">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password">
            <span class="input-error" id="password-error"></span>
            <label for="cnf-password">Confirm Password</label>
            <input type="password" id="cnf-password" name="cnf-password">
            <span class="input-error" id="cnf-password-error"></span>
        </form>
        <div style="display: flex; gap: 16px; margin-top:16px; flex-wrap:wrap;">
            <input class="button-primary" type="submit" form="pw-reset-form" value="Reset Password">
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script>
        var isValid = true;

        $('#pw-reset-form').submit(function(e) {
            e.preventDefault();
            let formData = {
                userId: $('#user-id').val(),
                password: $('#password').val(),
                cnfPassword: $('#cnf-password').val(),
                hash: $('#hash').val(),
                task: 'reset',
            };
            console.log(formData);
            isValid = true;
            if (formData.password.length < 8) {
                isValid = false;
                $('#password-error').text("Password must be at least 8 characters long.");
            }
            if (formData.cnfPassword != formData.password) {
                isValid = false;
                $('#cnf-password-error').text("Passwords do not match.");
            }
            if (isValid) {
                $.ajax({
                    type: "POST",
                    url: "reset_password.php",
                    data: {
                        formData: formData
                    },
                    dataType: 'json',
                }).done(function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        alert('Password reset successfully!');
                        window.location.href = "index.php";
                    } else if (response.status == 400) {
                        alert('Invalid data entered, please check and try again!');
                    } else {
                        alert(`Unknown error occured, please try again after some time. ${response.message}`);
                    }
                });
            }
        });

        $('#password').on('input change', function(event) {
            if (!isValid && event.target.value.length < 8) {
                $('#password-error').text("Password must be at least 8 characters long.");
            } else {
                $('#password-error').text("");
            }
        });

        $('#cnf-password').on('input change', function(event) {
            if (!isValid && (event.target.value != $('#password').val())) {
                $('#cnf-password-error').text("Passwords do not match.");
            } else {
                $('#cnf-password-error').text("");
            }
        });
    </script>
</body>

</html>