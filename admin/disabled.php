<?php

    $page['name'] = "disabled";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    $page['logo'] = "logo.svg";
    require_once("../files/components/account-setting.php");

    $email = $_SESSION['email'];

    $stmt = $link->prepare("SELECT id, disabled FROM `users` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $is_run = $stmt->get_result();
    $result = mysqli_fetch_assoc($is_run);

    $is_disabled = $result['disabled'];

    if ($is_disabled == 0) {
        $_SESSION['disabled'] == false;
    } else {
        $_SESSION['disabled'] == true;
    }
 
?>

<!DOCTYPE html>
<html lang="<?= $_COOKIE['site_lang'] ?>">

    <?php include($path."files/components/head.php") ?>
    
    <body class="<?=$page['name']?> page">
        <main class="login-page page--form">
            <div class="content">
                <a>
                    <div class="image-block">
                        <img src="<?= $path ?>files/logos/<?= $page['logo'] ?>"/>
                    </div>
                </a>
                <div class="form">
                    <form method="post">
                        <h2>Block</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                ACCOUNT BLOCKED
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Your account has been blocked!</h4>
                            <p>Your account has been blocked by our system. Please contact us for more information and to unblock your account.</p>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="logout.php">LOG OUT</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                    
                </div>
            </div>
        </main>

    </body>

</html>