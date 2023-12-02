<?php

$page['name'] = "dashboard";
$page['category'] = "account";
$page['path_lvl'] = 3;
require_once("../../files/components/account-setting.php");

// Get the username from the session
$username = $_SESSION['name'];

?>
<!DOCTYPE html>
<html lang="<?= $_COOKIE['site_lang'] ?>">

<?php include($path . "files/components/head.php") ?>

<body class="<?= $page['name'] ?> page page--account">

    <?php include($path . "files/components/account-sidebar.php") ?>

    <main class="content">
        <section class="block posts">
            <div class="container">
                <div class="posts__grid posts__grid--3" id="serverList">
                    <?php

                        $stmt = $link->prepare("SELECT * FROM `nodes` ORDER BY name ASC");

                        if ($stmt->execute()) {
                            $is_run = $stmt->get_result();
                            while ($result = mysqli_fetch_assoc($is_run)) { ?>

                                <div data-url="<?= $result['ip'] ?>" class="card card--server unkown">
                                    <div class="text">
                                        <h3 class="title"><?= strtoupper($result['name']) ?></h3>
                                        <p class="ip">IP: <?= $result['ip'] ?></p>
                                        <p class="status">Status: checking...</p>
                                    </div>
                                </div>
                            <?php }
                        } else { echo "<h2>Er is iets fout gegaan! Probeer het later opnieuw.</h2>"; }
                    ?>
                </div>
            </div>
        </section>
        <script src="<?=$path?>files/js/status-checker.js"></script>
    </main>

</body>

</html>