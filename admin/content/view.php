<?php

    $page['name'] = "view";
    $page['category'] = "account";
    $page['path_lvl'] = 3;
    require_once("../../files/components/account-setting.php");

    // Get the username from the session
    $username = $_SESSION['name'];
    if (isset($_GET['mode']) && $_GET['mode']) { $mode = $_GET['mode']; }
    else { header("Location: ".$_SERVER['HTTP_REFERER']); }

    if (isset($_GET['id']) && $_GET['id']) { $id = $_GET['id']; } 
    else if ($mode == "add") { $id = null; }
    else { header("Location: ".$_SERVER['HTTP_REFERER']); }





    if (isset($_POST['edit'])) {
        $name = $_POST['name'];
        $ip = $_POST['ip'];

        $stmt = $link->prepare("UPDATE `nodes` SET `name`=?, `ip`=? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $ip, $id);
        $stmt->execute();

        header("Location: nodes.php");
    } else if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $ip = $_POST['ip'];

        $stmt = $link->prepare("INSERT INTO `nodes` SET `name`=?, `ip`=?");
        $stmt->bind_param("ss", $name, $ip);
        $stmt->execute();

        header("Location: nodes.php");
    } else if (isset($_POST['delete'])) {
        $stmt = $link->prepare("DELETE FROM `nodes` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        header("Location: nodes.php");
    }

?>
<!DOCTYPE html>
<html lang="<?= $_COOKIE['site_lang'] ?>">

    <?php include($path."files/components/head.php") ?>
    
    <body class="<?=$page['name']?> page page--account">

        <?php include($path."files/components/account-sidebar.php") ?>

        <main class="content">
            <div class="btn-group">
                <a class="btn btn--primary btn--small" href="./nodes.php"><i class="da-icon da-icon--arrow-left da-icon--small"></i>Go back</a>
                <?php if($mode != "add") : ?>
                    <a class="btn btn--primary btn--small btn--danger" href="?mode=delete&id=<?=$id?>"><i class="da-icon da-icon--trash da-icon--small"></i>Delete</a>
                <?php endif; ?>
            </div>
            <form method="post" class="form">

                <?php if($mode == "edit") : ?>
                    <?php
                        $stmt = $link->prepare("SELECT * FROM `nodes` WHERE id = ?");
                        $stmt->bind_param("i", $id);
                        if ($stmt->execute()) {
                            $is_run = $stmt->get_result();
                            $result = mysqli_fetch_assoc ($is_run); ?>

                            <div class="form__box">
                                <h3>Node name</h3>
                                <input name="name" value="<?= $result['name'] ?>">
                            </div>
                            <div class="form__box">
                                <h3>Node ip/fqdn</h3>
                                <input name="ip" value="<?= $result['ip'] ?>">
                            </div>
                            <div class="form__box">
                                <input type="submit" name="edit" class="btn btn--primary btn--small" value="Save">
                            </div>
                        <?php } else { echo "Error in execution!"; }
                    ?>
                <?php elseif ($mode == "delete") : ?>
                    <div class="form__box">
                        <h3>Are you sure you want to delete this item?</h3>
                        <input type="submit" name="delete" class="btn btn--danger btn--small" value="Yes, delete it!">
                    </div>
                <?php elseif ($mode == "add") : ?>
                    <div class="form__box">
                        <h3>Nodename</h3>
                        <input name="name">
                    </div>
                    <div class="form__box">
                        <h3>Node ip/fqdn</h3>
                        <input name="ip">
                    </div>
                    <div class="form__box">
                        <input type="submit" name="add" class="btn btn--primary btn--small" value="Save">
                    </div>
                <?php endif; ?>

            </form>
        </main>

    </body>

</html>