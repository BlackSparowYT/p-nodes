<?php

    $page['name'] = "register";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    require_once("../files/components/account-setting.php");

    // Check if the user has submitted the registration form
    if (isset($_POST['register'])) {
        // Get the email, username, and password from the form
        $email = $_POST['email'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        

        // Generate a random reset token
        $reset_is_same = true;
        $reset_token = substr(bin2hex(random_bytes(16)), 0, 32);

        while ($reset_is_same == true) {

            $stmt = $link->prepare("SELECT * FROM `users` WHERE reset_token = ?");
            $stmt->bind_param("s", $reset_token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $reset_is_same = false;
            } else {
                $reset_token = substr(bin2hex(random_bytes(16)), 0, 32);
            }
        }

        // Check if the email already exists
        $stmt = $link->prepare("SELECT * FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // If the email doesn't exist, hash the password and insert the user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $link->prepare("INSERT INTO `users` (email, password, name, reset_token) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $hashed_password, $name, $reset_token);
            $stmt->execute();

            // Log the user in and redirect to the dashboard page
            header("Location: login.php");
            exit();
        } else {
            // If the email already exists, show an error message
            $error = "Email is al in gebruik.";
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
                        <h2>Registreer</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                REGISTREER MET EMAIL
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Email</h4>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div>
                            <h3>Naam</h3>
                            <input type="text" name="name" required>
                        </div>
                        <div>
                            <h4>Wachtwoord</h4>
                            <input type="password" name="password" id="password" required>
                            <?php
                            echo '
                                <a id="showPass" onclick="showPass()"><img id="showPassBtn" src="'.$path.'files/icons/pass-vis.png"></a>
                                <script>
                                    function showPass() {
                                        var myPass = document.getElementById("password");
                                        var showPass = document.getElementById("showPass");
                                        var showPassBtn = document.getElementById("showPassBtn");
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
                            <button type="submit" name="register">Registreer</button>
                        </div>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="login.php">LOGIN</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                </div>
            </div>
        </main>

    </body>

</html>