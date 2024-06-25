<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        $task = $formData['task'] ?? null;
        echo "task = $task\n";
        require_once 'db_config.php';
        if ($task == 'password') {
            echo "entered pw";
            $query = $dbh->prepare("SELECT password_hash FROM user_accounts WHERE user_id = :user_id");
            $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
            $query->execute();
            $currentHash = $query->fetch(PDO::FETCH_OBJ)->password_hash;
            if (!password_verify($formData['oldPassword'], $currentHash)) {
                $response['status'] = 403;
                $response['message'] = 'wrong password';
            } else {
                $query = $dbh->prepare("UPDATE user_accounts SET password_hash = :hash WHERE user_id = :user_id");
                $hash = password_hash($formData['newPassword'], PASSWORD_BCRYPT);
                $query->bindParam(':hash', $hash, PDO::PARAM_STR_CHAR);
                $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
                if ($query->execute()) {
                    $response['status'] = 200;
                    $response['message'] = 'updated';
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'bad data';
                }
            }
        } else if ($task == 'others') {
            echo "entered others";
            $query = $dbh->prepare(
                "UPDATE user_accounts
                SET
                    first_name = :fname,
                    last_name = :lname,
                    phone_number = :phone
                WHERE
                    user_id = :user_id"
            );
            $query->bindParam(':fname', $formData['fname'], PDO::PARAM_STR);
            $query->bindParam(':lname', $formData['lname'], PDO::PARAM_STR);
            $query->bindParam(':phone', $formData['phone'], PDO::PARAM_INT);
            $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
            if ($query->execute()) {
                $response['status'] = 200;
                $response['message'] = 'updated';
            } else {
                $response['status'] = 400;
                $response['message'] = 'bad data';
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

require_once 'db_config.php';
$query = $dbh->prepare(
    "SELECT
        *
    FROM
        user_accounts
    WHERE
        user_id = $userId"
);
$query->execute();
$userData = $query->fetch(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <title>User Settings - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main style="height:100vh; overflow-y: scroll;">
        <h1>User Settings</h1>
        <form id="user-settings-form">
            <input id="user-id" value="<?php echo $_SESSION['user_id']; ?>" name="userId" style="display: none;">
            <label for="first-name">First Name</label>
            <input value="<?php echo $userData->first_name; ?>" id="first-name" name="first-name">
            <span class="input-error" id="first-name-error"></span>
            <label for="last-name">Last Name</label>
            <input value="<?php echo $userData->last_name; ?>" id="last-name" name="last-name">
            <span class="input-error" id="last-name-error"></span>
            <label for="phone">Phone Number</label>
            <span style="display: flex;">
                <span style="flex-shrink: 0; padding: 12px 5px;">+381</span>
                <input value="<?php echo $userData->phone_number; ?>" id="phone" name="phone">
            </span>
            <span class="input-error" id="phone-error"></span>
        </form>
        <div style="display: flex; gap: 16px; margin-top:16px; flex-wrap:wrap;">
            <input class="button-primary" type="submit" form="user-settings-form" value="Save">
            <a id="change-password-btn" class="button-primary" href="#">Change Password</a>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script>
        var isValid = true;

        $('#user-settings-form').submit(function(e) {
            e.preventDefault();
            let formData = {
                userId: $('#user-id').val(),
                fname: $('#first-name').val(),
                lname: $('#last-name').val(),
                phone: $('#phone').val(),
                task: 'others',
            };
            console.log(formData);
            isValid = true;
            if (formData.fname.length == 0) {
                isValid = false;
                $('#first-name-error').text("Please enter a valid first name.");
            }
            if (formData.lname.length == 0) {
                isValid = false;
                $('#last-name-error').text("Please enter a valid last name.");
            }
            if (formData.phone.toString().length != 9) {
                isValid = false;
                $('#phone-error').text('Please enter a valid phone number.');
            }
            if (isValid) {
                console.log('form is valid');
                $.ajax({
                    type: "POST",
                    url: "user_settings.php",
                    data: {
                        formData: formData
                    },
                    dataType: 'json',
                }).done(function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        alert('Details updated successfully!');
                    } else if (response.status == 400) {
                        alert('Invalid data entered, please check and try again!');
                    } else {
                        alert(`Unknown error occured, please try again after some time. ${response.message}`);
                    }
                });
            }
        });

        $('#first-name').on('input change', function(event) {
            if (!isValid && event.target.value.length == 0) {
                $('#first-name-error').text("Please enter a valid first name.");
            } else {
                $('#first-name-error').text("");
            }
        });

        $('#last-name').on('input change', function(event) {
            if (!isValid && event.target.value.length == 0) {
                $('#last-name-error').text("Please enter a valid last name.");
            } else {
                $('#last-name-error').text("");
            }
        });

        $('#phone').on('input change', function(event) {
            if (!isValid && event.target.value.length < 9) {
                $('#phone-error').text("Please enter a valid phone number.");
            } else {
                $('#phone-error').text("");
            }
        });

        $('#change-password-btn').click(function(e) {
            e.preventDefault();
            let oldPassword = prompt("Please enter your current password:");
            if (oldPassword == null || oldPassword == "") return;
            let newPassword = prompt("Please enter the new password:");
            if (newPassword == null || newPassword == "") return;
            let cnfPassword = prompt("Confirm new password:");
            if (cnfPassword == null || cnfPassword == "") return;
            if (newPassword !== cnfPassword) {
                alert('Passwords do not match, please try again.');
                return;
            }
            let formData = {
                userId: $('#user-id').val(),
                oldPassword: oldPassword,
                newPassword: newPassword,
                task: 'password',
            };
            $.ajax({
                type: "POST",
                url: "user_settings.php",
                data: {
                    formData: formData
                },
                dataType: 'json',
            }).done(function(response) {
                console.log(response);
                if (response.status == 200) {
                    alert('Password updated successfully!');
                } else if (response.status == 400) {
                    alert('Invalid data entered, please check and try again!');
                } else {
                    alert(`Unknown error occured, please try again after some time. ${response.message}`);
                }
            });
        });
    </script>
</body>

</html>