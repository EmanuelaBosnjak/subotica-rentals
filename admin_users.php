<?php
$formData = $_POST['formData'] ?? null;
if ($formData != null) {
    $response = ["status" => 500, "message" => 'server error'];
    ob_start();
    try {
        $task = $formData['task'];
        require_once 'db_config.php';
        if ($task == 'unblock' || $task == 'block') {
            $query = $dbh->prepare(
                "SELECT * FROM user_accounts 
                WHERE user_id = :user_id AND account_state = 'ADMIN'"
            );
            $query->bindParam(':user_id', $formData['userId'], PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                $status = ($task == 'unblock' ? 'ACTIVE' : 'BLOCKED');
                $query = $dbh->prepare(
                    "UPDATE user_accounts SET
                    account_state = '$status' WHERE user_id = :user_id 
                    AND account_state NOT IN ('ADMIN', 'NOT_VERIFIED')"
                );
                $query->bindParam(':user_id', $formData['modUserId'], PDO::PARAM_INT);
                $query->execute();
                if ($query->rowCount() > 0) {
                    $response['status'] = 200;
                    $response['message'] = 'updated';
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'bad data';
                }
            } else {
                $response['status'] = 403;
                $response['message'] = 'unauthorized';
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
if (!$loggedIn || !$_SESSION['is_admin']) {
    header('Location:index.php');
    die();
}

require_once 'db_config.php';
$userId = $_SESSION['user_id'];
$query = $dbh->prepare(
    "SELECT
        *
    FROM
        user_accounts
    WHERE
        user_id <> $userId"
);
$query->execute();
$users = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Users - Subotica Rentals</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php require_once "global_includes.php"; ?>

    <style>
        main {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-title {
            flex-shrink: 0;
            padding: 5px;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
            align-content: center;

            >.button-primary {
                margin: unset;
            }
        }

        .users-list {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            gap: 10px;
            scrollbar-width: thin;
            overflow-y: scroll;
            padding: 10px;
            border: 3px solid var(--primary-color);
            border-radius: 20px;
            background-color: black;

            #no-users-text {
                flex-grow: 1;
                text-align: center;
                color: white;
            }

            .item {
                display: flex;
                flex-direction: column;
                flex-shrink: 0;
                flex-basis: content;
                height: fit-content;
                align-items: stretch;
                background-color: azure;
                border: 0px solid white;
                border-radius: 10px;
                padding: 10px;

                @media only screen and (max-width: 768px) {
                    /* For mobile phones: */
                    flex-direction: column;
                    align-items: unset;
                }

                .btn-bar {
                    display: flex;
                    align-content: center;
                    gap: 0px 10px;
                    flex-wrap: wrap;
                    justify-content: end;
                }

                .account-state {
                    font-size: x-large;
                    font-weight: bold;
                }

                .NOT_VERIFIED {
                    color: rgb(161, 161, 0);
                }

                .NOT_VERIFIED::before {
                    content: "AWAITING EMAIL VERIFICATION";
                }

                .ACTIVE {
                    color: rgb(142, 211, 40);
                }

                .ACTIVE::before {
                    content: "ACCOUNT ACTIVE";
                }

                .ADMIN {
                    color: yellowgreen;
                }

                .ADMIN::before {
                    content: "ADMIN";
                }

                .BLOCKED {
                    color: rgb(187, 0, 0);
                }

                .BLOCKED::before {
                    content: "BLOCKED BY ADMIN";
                }
            }
        }
    </style>
</head>

<body>
    <header>
        <?php require_once('navbar.php'); ?>
    </header>

    <main>
        <div class="page-title">
            <h1>
                Users
            </h1>
        </div>
        <div class="users-list">
            <?php
            if (empty($users)) {
                echo '<span id="no-users-text">No other users registered!</span>';
            } else {
                foreach ($users as $u) {
                    $state = $u->account_state;
                    $actionButton = (($state == 'NOT_VERIFIED' || $state == 'ADMIN') ? ''
                        : ($state != 'ACTIVE'
                            ? "<a href='#' id='$u->user_id-active' data-mod-user-id='$u->user_id' class='button-primary button-sm'><i class='bi bi-check2-all'></i> Unblock</a>"
                            : "<a href='#' id='$u->user_id-block' data-mod-user-id='$u->user_id' class='button-primary button-sm button-delete'><i class='bi bi-ban'></i> Block</a>"));
                    echo
                    "<div class='item'>
                        <span class='userId'>User ID: $u->user_id</span>
                        <span class='name'>$u->first_name $u->last_name</span>
                        <a class='email' href='mailto:$u->email'>$u->email</a>
                        <a class='phone' href='tel:+381$u->phone_number'>+381 $u->phone_number</a>
                        <span class='btn-bar'>
                            <span class='account-state $u->account_state'></span>
                            $actionButton
                        </span>
                    </div>";
                }
            }
            ?>
        </div>
    </main>

    <footer>
        <!--  -->
    </footer>

    <script src="./script.js"></script>
    <script>
        var userId = <?php echo $userId; ?>;
    </script>
    <script src="./admin_users.js"></script>
</body>

</html>