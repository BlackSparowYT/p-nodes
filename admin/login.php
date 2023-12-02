<?php

    $page['name'] = "login";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
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
                $error = "Email of Wachtwoord is incorrect.";
            }
        } else {
            // If the password doesn't match, show an error message
            $error = "Email of Wachtwoord is incorrect.";
        }
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
                        <h2>Login</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                LOGIN MET EMAIL
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <h4>Email</h4>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div>
                            <h4>Wachtwoord <a href="forgot-pass.php?action=email">wachtwoord vergeten</a></h4>
                            <input type="password" name="password" id="password" required>
                            <a id="showPass" onclick="showPass()"><img id="showPassBtn" src="<?= $path ?>files/icons/pass-vis.png"></a>
                            <script>
                                function showPass() {
                                    var myPass = document.getElementById("password");
                                    var showPass = document.getElementById("showPass");
                                    var showPassBtn = document.getElementById("showPassBtn");
                                    if (myPass.type === "password") {
                                        myPass.type = "text";
                                        showPassBtn.style.backgroundColor = "#E9E9E9";
                                        showPassBtn.src="../template/save.png";
                                        showPassBtn.src = "<?= $path ?>files/icons/pass-invis.png";
                                    } else {
                                        myPass.type = "password";
                                        showPassBtn.style.backgroundColor = "#ADD5D0";
                                        showPassBtn.src = "<?= $path ?>files/icons/pass-vis.png";
                                    }
                                }
                            </script>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors" style="color: darkred;"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <button type="submit" name="login">Login</button>
                        </div>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="register.php">REGISTREER</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                    
                </div>
            </div>
        </main>

    </body>

</html>