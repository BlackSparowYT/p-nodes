<?php

    $page['name'] = "reset";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    require_once("../files/components/account-setting.php");

    // Connect to the database
    require_once("../files/config.php");

    // Check if the user has submitted the form
    if (isset($_POST['change_username'])) {
        // Get the password from the form
        $new_username = $_POST['new_username'];
        $confirm_username = $_POST['confirm_username'];
        $password = $_POST['password'];

        // Validate the inputs
        $errors = array();
        if ($new_username === "") {
            $errors[] = "Please enter your new username.";
        }
        if ($confirm_username === "") {
            $errors[] = "Please confirm your new username.";
        }
        if ($new_username !== $confirm_username) {
            $errors[] = "The new username and confirmation do not match.";
        }
        if ($password === "") {
            $errors[] = "Please enter your password.";
        }

        // Check if the password is correct
        $stmt = $link->prepare("SELECT * FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            $errors[] = "Invalid password.";
        }

        // Check if the new username already exists
        $stmt = $link->prepare("SELECT * FROM `users` WHERE name = ?");
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "The new username is already taken.";
        }

        if (count($errors) == 0) {
            // If there are no errors, update the username in the database
            $stmt = $link->prepare("UPDATE `users` SET name = ? WHERE email = ?");
            $stmt->bind_param("ss", $new_username, $_SESSION['email']);
            $stmt->execute();

            // Set a success message and redirect to the dashboard
            $_SESSION['name'] = $new_username;
            header("Location: dashboard.php");
            exit();
        }
    }
?>


<!DOCTYPE html>
<html lang="<?= $_COOKIE['site_lang'] ?>">

    <?php include($path."files/components/head.php") ?>
    
    <body class="<?=$page['name']?> page">
        <main class="register-page account-page">
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
                        <h2>Verander Naam</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                VERANDER NAAM
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Nieuwe Naam</h4>
                            <input type="text" name="new_username" required>
                        </div>
                        <div>
                            <h4>Bevestig Nieuwe Naam</h4>
                            <input type="text" name="confirm_username" required>
                        </div>
                        <div>
                            <h4>Huidige Wachtwoord</h4>
                            <input type="password" name="password" id="password1" required>
                            <?php
                            echo '
                                <a id="showPass1" onclick="showPass1()"><img id="showPassBtn1" src="'.$path.'files/icons/pass-vis.png"></a>
                                <script>
                                    function showPass1() {
                                        var myPass = document.getElementById("password1");
                                        var showPass = document.getElementById("showPass1");
                                        var showPassBtn = document.getElementById("showPassBtn1");
                                        if (myPass.type === "password") {
                                            myPass.type = "text";
                                            showPassBtn.style.backgroundColor = "#E9E9E9";
                                            showPassBtn.src="../template/save.png";
                                            showPassBtn.src = "'.$path.'files/icons/pass-invis.png";
                                        } else {
                                            myPass.type = "password";
                                            showPassBtn.style.backgroundColor = "#ADD5D0";
                                            showPassBtn.src = "'.$path.'files/icons/pass-vis.png";
                                        }
                                    }
                                </script>
                            ';
                            ?>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors" style="color: darkred;"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <button type="submit" name="change_username">Verander Naam</button>
                        </div>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="dashboard.php">TERUG</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>