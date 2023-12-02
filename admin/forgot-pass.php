<?php

    $page['name'] = "forgot_pass";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    require_once("../files/components/account-setting.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require $path."files/mailing/PHPMailer/src/Exception.php";
    require $path."files/mailing/PHPMailer/src/PHPMailer.php";
    require $path."files/mailing/PHPMailer/src/SMTP.php";

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
            $errors[] = "Geen token gekregen.";
        }

        // Validate the inputs
        $errors = array();
        if ($email === "") {
            $errors[] = "Vul aub je email in.";
        }
        if ($new_password === "") {
            $errors[] = "Vul aub je nieuwe wachtwoord in.";
        }
        if ($confirm_password === "") {
            $errors[] = "Vul aub je nieuwe wachtwoord nog een keer in";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "De twee wachtwoorden komen niet overheen.";
        }

        // Check if the reset token is valid for the email
        $stmt = $link->prepare("SELECT * FROM `users` WHERE email = ? AND reset_token = ?");
        $stmt->bind_param("ss", $email, $reset_token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $errors[] = "Geen geldige token.";
        } else {
            $token_valid = '<p>Je token is niet geldig!</p>
            <p><a href="forgot-password.php?action=email">Vraag een nieuwe link aan.</a></p>';
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

        $emaillink = $site['url']."account/forgot-pass.php?action=reset&token=".$reset_token;
        $onderwerp = "Wachtwoord Vergeten";
        $inhoud = "Gebruik deze link om je wachtwoord te reseten ".$emaillink."\nHeb jij dit niet aangevraagd neem dan direct contact op met onze help desk!".$site['url']."/contact.php\n\nGroeten,\nRemote Reizen";

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

            $file = fopen("./files/mail-errors.txt","a");
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
                        <h2>Verander Vergeten</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                WACHTWOORD VERGETEN
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
                                <div>
                                    <h4>Nieuw Wachtwoord</h4>
                                    <input type="password" name="password" id="password" required>
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
                                </div>
                                <div>
                                    <h4>Bevestig Nieuw Wachtwoord</h4>
                                    <input type="password" name="confirm_password" id="password2" required>
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
                                </div>';
                                if (isset($error)) {
                                    echo '
                                    <div>
                                        <p class="errors" style="color: darkred;"><?php echo $error; ?></p>
                                    </div>';
                                }
                                echo '
                                <div class="link">
                                    <button type="submit" name="reset_password">Verander Wachtwoord</button>
                                </div>
                                <div class="link">
                                    <hr>
                                    <h5>
                                        <a href="login.php">TERUG</a>
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
                                        <p class="errors" style="color: darkred;"><?php echo $error; ?></p>
                                    </div>';
                                }
                                echo '
                                <div class="link">
                                    <button type="submit" name="reset-link">Stuur link</button>
                                </div>
                                <div class="link">
                                    <hr>
                                    <h5>
                                        <a href="login.php">TERUG</a>
                                    </h5>
                                    <hr>
                                </div>';
                            }
                        ?>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>