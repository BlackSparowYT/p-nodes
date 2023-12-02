<?php

    $page['name'] = "register";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    $page['logo'] = "logo.svg";
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
            $error = "Email is already in use.";
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
                        <h2>Register</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                REGISTER WITH EMAIL
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Email</h4>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div>
                            <h4>Name</h4>
                            <input type="text" name="name" required>
                        </div>
                        <div class="passBox">
                            <h4>Password</h4>
                            <input type="password" name="password" class="password" required>
                            <a class="showPass" onclick="showPass()"><i class="showPassBtn da-icon da-icon--eye"></i></a>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors"><?= $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <button class="btn btn--primary" type="submit" name="register">Register</button>
                        </div>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="index.php">LOGIN</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                    <script>
                        function showPass() {
                            const passwords = document.querySelectorAll(".passBox");
                            passwords.forEach(password => {
                                var myPass = password.querySelector(".password");
                                var showPass = password.querySelector(".showPass");
                                var showPassBtn = password.querySelector(".showPassBtn");
                                if (myPass.type === "password") {
                                    myPass.type = "text";
                                    showPassBtn.classList.remove("da-icon--eye");
                                    showPassBtn.classList.add("da-icon--eye-slash");
                                } else {
                                    myPass.type = "password";
                                    showPassBtn.classList.add("da-icon--eye");
                                    showPassBtn.classList.remove("da-icon--eye-slash");
                                }
                            });
                        }
                    </script>
                </div>
            </div>
        </main>

    </body>

</html>