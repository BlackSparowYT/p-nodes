<?php

    $page['name'] = "forgot_pass";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    $page['logo'] = "logo.svg";
    require_once("../files/components/account-setting.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require $path."files/components/PHPMailer/src/Exception.php";
    require $path."files/components/PHPMailer/src/PHPMailer.php";
    require $path."files/components/PHPMailer/src/SMTP.php";

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        $action = null;
    }



    // Check if the user has submitted the form
    if (isset($_POST['reset_password'])) {
        // Get the email, password and reset token from the form
        $email = $_POST['email'];
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        if (isset($_GET['token'])) {
            $reset_token = $_GET['token'];
        } else {
            $errors[] = "No token recieved.";
        }

        // Validate the inputs
        $errors = array();
        if ($email === "") {
            $errors[] = "Please enter your email.";
        }
        if ($new_password === "") {
            $errors[] = "Please enter your new password.";
        }
        if ($confirm_password === "") {
            $errors[] = "Please confirm your new password.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        // Check if the reset token is valid for the email
        $stmt = $link->prepare("SELECT * FROM `users` WHERE email = ? AND reset_token = ?");
        $stmt->bind_param("ss", $email, $reset_token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $errors[] = "Invalid token.";
        } else {
            $token_valid = '<p>Your token is invalid!</p>
            <p><a href="forgot-password.php?action=email">Request a new link</a></p>';
        }

        if (count($errors) == 0) {

            $new_token = substr(bin2hex(random_bytes(16)), 0, 32);

            // If there are no errors, update the password in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $link->prepare("UPDATE `users` SET password = ?, reset_token = ? WHERE email = ?");
            $stmt->bind_param("sss", $hashed_password, $new_token, $email);
            $stmt->execute();

            // Set a success message and redirect to the login page
            header("Location: login.php");
            exit();
        }
    } else if (isset($_POST['reset-link'])) {

        $email = $_POST['email'];

        $mail1 = new PHPMailer(true);

        $stmt = $link->prepare("SELECT reset_token FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $is_run = $stmt->get_result();
        $result = mysqli_fetch_assoc($is_run);
        $reset_token = $result['reset_token'];

        $emaillink = $site['url']."/admin/forgot-pass.php?action=reset&token=".$reset_token;
        $onderwerp = "Forgot password";
        $inhoud = "Use this link to reset your password ".$emaillink."\nDid you not request a password reset? Get in conact with our help desk! ".$site['url']."/contact.php\n\nRegards,\n".$site['name'];

        try {
            //Server settings
            $mail1->isSMTP();                                            //Send using SMTP
            $mail1->Host       = $mail_host;                             //Set the SMTP server to send through
            $mail1->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail1->Username   = $mail_Username;                         //SMTP username
            $mail1->Password   = $mail_Password;                         //SMTP password
            $mail1->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail1->Port       = $mail_Port;                             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail1->setFrom('testing@design-atlas.nl');
            $mail1->addAddress('support@design-atlas.nl');
            $mail1->addAddress($email);

            //Content
            $mail1->isHTML(false);                                        //Set email format to HTML
            $mail1->Subject = $onderwerp;
            $mail1->Body    = $inhoud;

            $mail1->send();

        } catch (Exception $e) {

            echo $mail1->ErrorInfo;

            // Log the error

            $file = fopen($path."files/errors/mail.txt","a");
            $ip = $_SERVER['REMOTE_ADDR'];
            $date = date("Y/m/d H:i:s");
            $fdata = "\n\nDate & Time: ".$date.", \nError: {".$mail1->ErrorInfo."};\nEmail: ".$email.", Subject: ".$onderwerp.", Content:".$inhoud.";";
            fwrite($file,$fdata);
            fclose($file);

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
                        <h2>Forgot Password</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                FORGOT PASSWORD
                            </h5>
                            <hr>
                        </div>

                        <?php 
                        
                            if($action=="reset") {
                                echo '
                                <div>
                                    <h4>Email</h4>
                                    <input type="email" name="email" required>
                                </div>
                                <div class="passBox">
                                    <h4>New password</h4>
                                    <input type="password" name="password" class="password" required>
                                    <a class="showPass" onclick="showPass()"><i class="showPassBtn da-icon da-icon--eye"></i></a>
                                </div>
                                <div class="passBox">
                                    <h4>Confirm password</h4>
                                    <input type="password" name="confirm_password" class="password" required>
                                    <a class="showPass" onclick="showPass()"><i class="showPassBtn da-icon da-icon--eye"></i></a>
                                </div>';
                                if (isset($error)) {
                                    echo '
                                    <div>
                                        <p class="errors"><?php echo $error; ?></p>
                                    </div>';
                                }
                                echo '
                                <div class="link">
                                    <button class="btn btn--primary" type="submit" name="reset_password">Change password</button>
                                </div>
                                <div class="link">
                                    <hr>
                                    <h5>
                                        <a href="login.php">BACK</a>
                                    </h5>
                                    <hr>
                                </div>';
                            } elseif ($action=="email") {
                                echo '
                                <div>
                                    <h4>Email</h4>
                                    <input type="email" name="email" required>
                                </div>';
                                if (isset($error)) {
                                    echo '
                                    <div>
                                        <p class="errors"><?php echo $error; ?></p>
                                    </div>';
                                }
                                echo '
                                <div class="link">
                                    <button class="btn btn--primary" type="submit" name="reset-link">Send link</button>
                                </div>
                                <div class="link">
                                    <hr>
                                    <h5>
                                        <a href="index.php">BACK</a>
                                    </h5>
                                    <hr>
                                </div>';
                            }
                        ?>
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