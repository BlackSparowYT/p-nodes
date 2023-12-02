<?php

    $page['name'] = "reset";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    $page['logo'] = "logo.svg";
    require_once("../files/components/account-setting.php");

    // Connect to the database
    require_once("../files/config.php");

    // Check if the user has submitted the password reset form
    if (isset($_POST['reset_password'])) {
        // Get the user's email and old password from the form
        $email = $_SESSION['email'];
        $old_password = $_POST['old_password'];

        // Check if the old password is correct
        $stmt = $link->prepare("SELECT password FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $hashed_old_password = $row['password'];

        if (password_verify($old_password, $hashed_old_password)) {
            // If the old password is correct, get the new passwords from the form
            $new_password1 = $_POST['new_password1'];
            $new_password2 = $_POST['new_password2'];

            // Check if the new passwords match
            if ($new_password1 === $new_password2) {
                // If the new passwords match, hash the new password and update the user's password in the database
                $hashed_new_password = password_hash($new_password1, PASSWORD_DEFAULT);
                $stmt = $link->prepare("UPDATE `users` SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_new_password, $email);
                $stmt->execute();

                // Redirect to the dashboard page
                header("Location: dashboard.php");
                exit();
            } else {
                // If the new passwords don't match, show an error message
                $error = "New passwords don't match";
            }
        } else {
            // If the old password is incorrect, show an error message
            $error = "Invalid password";
        }
    }
?>

<!DOCTYPE html>
<html lang="<?= $_COOKIE['site_lang'] ?>">

    <?php include($path."files/components/head.php") ?>
    
    <body class="<?=$page['name']?> page">
        <main class="register-page page--form">
            <div class="content">
                <a>
                    <div class="image-block">
                        <img src="<?= $path ?>files/logos/<?= $page['logo'] ?>"/>
                    </div>
                </a>
                <div class="form">
                    <form method="post">
                        <h2>Verander Wachtwoord</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                VERANDER WACHTWOORD
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Huidige Wachtwoord</h4>
                            <input type="password" name="old_password" id="password1" required>
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
                        <div>
                            <h4>Nieuw Wachtwoord</h4>
                            <input type="password" name="new_password1" id="password2" required>
                            <?php
                            echo '
                                <a id="showPass2" onclick="showPass2()"><img id="showPassBtn2" src="'.$path.'files/icons/pass-vis.png"></a>
                                <script>
                                    function showPass2() {
                                        var myPass = document.getElementById("password2");
                                        var showPass = document.getElementById("showPass2");
                                        var showPassBtn = document.getElementById("showPassBtn2");
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
                        <div>
                            <h4>Bevestig Nieuw Wachtwoord</h4>
                            <input type="password" name="new_password2" id="password3" required>
                            <?php
                            echo '
                                <a id="showPass3" onclick="showPass3()"><img id="showPassBtn3" src="'.$path.'files/icons/pass-vis.png"></a>
                                <script>
                                    function showPass3() {
                                        var myPass = document.getElementById("password3");
                                        var showPass = document.getElementById("showPass3");
                                        var showPassBtn = document.getElementById("showPassBtn3");
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
                            <button type="submit" name="reset_password">Verander Wachtwoord</button>
                        </div>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="index.php">TERUG</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>