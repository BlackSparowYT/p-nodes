<?php

    $page['name'] = "reset";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    $page['logo'] = "logo.svg";
    require_once("../files/components/account-setting.php");

    // Connect to the database
    require_once("../files/config.php");

    // Check if the user has submitted the form
    if (isset($_POST['change_email'])) {
        // Get the email and password from the form
        $new_email = $_POST['new_email'];
        $confirm_email = $_POST['confirm_email'];
        $password = $_POST['password'];

        // Validate the inputs
        $errors = array();
        if ($new_email === "") {
            $errors[] = "Please enter your new email.";
        }
        if ($confirm_email === "") {
            $errors[] = "Please confirm your new email.";
        }
        if ($new_email !== $confirm_email) {
            $errors[] = "The new email and confirmation do not match.";
        }
        if ($password === "") {
            $errors[] = "Please enter your password.";
        }

        // Check if the password is correct
        $stmt = $link->prepare("SELECT * FROM `users` WHERE name = ?");
        $stmt->bind_param("s", $_SESSION['name']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            $errors[] = "Invalid password.";
        }

        // Check if the new email already exists
        $stmt = $link->prepare("SELECT * FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $new_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "The new email is already taken.";
        }

        if (count($errors) == 0) {
            // If there are no errors, update the email in the database
            $stmt = $link->prepare("UPDATE `users` SET email = ? WHERE name = ?");
            $stmt->bind_param("ss", $new_email, $_SESSION['name']);
            $stmt->execute();

            // Set a success message and redirect to the dashboard
            $_SESSION['email'] = $new_email;
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
                        <h2>Change Email</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                CHANGE EMAIL
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>New Email</h4>
                            <input type="email" name="new_email" required>
                        </div>
                        <div>
                            <h4>Confirm Email</h4>
                            <input type="email" name="confirm_email" required>
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
                            <button class="btn btn--primary" type="submit" name="change_email">Change Email</button>
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