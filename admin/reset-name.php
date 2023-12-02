<?php

    $page['name'] = "reset";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    $page['logo'] = "logo.svg";
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
        <main class="register-page page--form">
            <div class="content">
                <a>
                    <div class="image-block">
                        <img src="<?= $path ?>files/logos/<?= $page['logo'] ?>"/>
                    </div>
                </a>
                <div class="form">
                    <form method="post">
                        <h2>Change name</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                CHANGE NAME
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>New name</h4>
                            <input type="text" name="new_username" required>
                        </div>
                        <div>
                            <h4>Confirm name</h4>
                            <input type="text" name="confirm_username" required>
                        </div>
                        <div class="passBox">
                            <h4>Password</h4>
                            <input type="password" name="password" class="password" required>
                            <a class="showPass" onclick="showPass()"><i class="showPassBtn da-icon da-icon--eye"></i></a>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors" style="color: darkred;"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <button class="btn btn--primary" type="submit" name="change_username">Change name</button>
                        </div>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="index.php">BACK</a>
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