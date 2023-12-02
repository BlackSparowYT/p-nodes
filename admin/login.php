<?php

    $page['name'] = "login";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    $page['logo'] = "logo.svg";
    require_once("../files/components/account-setting.php");


    // Check if the user has submitted the login form
    if (isset($_POST['login'])) {
        // Get the email and password from the form
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Query the database to check if the email exists
        $stmt = $link->prepare("SELECT password FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            // Use password_verify to check if the entered password matches the hashed password
            if (password_verify($password, $hashed_password)) {

                $_SESSION['email'] = $email;
                $_SESSION['loggedin'] = true;

                $stmt = $link->prepare("SELECT name, id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if user has admin access
                if ($row = mysqli_fetch_assoc($result)) {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['name'] = $row['name'];
                }

                header("Location: ./content/dashboard.php");
                exit();
            } else {
                // If the password doesn't match, show an error message
                $error = "Email or Password is incorrect.";
            }
        } else {
            // If the password doesn't match, show an error message
            $error = "Email or Password is incorrect.";
        }
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
                        <h2>Login</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                LOGIN WITH EMAIL
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Email</h4>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="passBox">
                            <h4>Password <a href="forgot-pass.php?action=email">forgot password</a></h4>
                            <input type="password" name="password" class="password" required>
                            <a class="showPass" onclick="showPass()"><i class="showPassBtn da-icon da-icon--eye"></i></a>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors"><?= $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <button class="btn btn--primary" type="submit" name="login">Login</button>
                        </div>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="register.php">REGISTER</a>
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