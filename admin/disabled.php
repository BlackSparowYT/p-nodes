<?php

    $page['name'] = "disabled";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    require_once("../files/components/account-setting.php");

    $email = $_SESSION['email'];

    $stmt = $link->prepare("SELECT id, is_disabled FROM `users` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $is_run = $stmt->get_result();
    $result = mysqli_fetch_assoc($is_run);

    $is_disabled = $result['is_disabled'];

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
        <main class="login-page account-page">
            <div class="content">
                <?php echo '
                <a href="'.$path.'index.php">
                    <div class="image-block">
                        <img src="'.$path.'files/images/logo-blank.png"/>
                    </div>
                </a>
                '; ?>
                <div class="form">
                    <form method="post">
                        <h2>Geblockeerd</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                GEBLOCKEERD ACCOUNT
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Je account is geblockeerd!</h4>
                            <p>Je account is door ons system geblockeerd, neem contact met ons op voor meer informatie en om je account te deblockeren.</p>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors" style="color: darkred;"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="logout.php">LOG UIT</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                    
                </div>
            </div>
        </main>

    </body>

</html>