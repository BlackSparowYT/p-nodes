<?php

    $page['name'] = "verify";
    $page['category'] = "account";
    $page['path_lvl'] = 2;
    require_once("../files/components/account-setting.php");

    require($path."files/mail-config.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../files/mailing/PHPMailer/src/Exception.php';
    require '../files/mailing/PHPMailer/src/PHPMailer.php';
    require '../files/mailing/PHPMailer/src/SMTP.php';

    $stmt = $link->prepare("SELECT id, verify_token FROM `users` WHERE email = ? AND id = ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $is_run = $stmt->get_result();
    $result = mysqli_fetch_assoc($is_run);

    $verify_token = $result['verify_token'];

    if (!isset($_SESSION['send_verify_email'])) {
        $_SESSION['send_verify_email'] = false;
    }

    $mail = new PHPMailer(true);

    try { 
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $mail_host;                             //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $mail_Username;                         //SMTP username
        $mail->Password   = $mail_Password;                         //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = $mail_Port;                             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('testing@design-atlas.nl');
        $mail->addAddress($_SESSION['email']);

        //Content
        $mail->isHTML(false);                                        //Set email format to HTML
        $mail->Subject = "Account Verifieren!";
        $mail->Body    = "Om je account te verifiëren kunt u onderstaande link gebruiken.\nLog eerst in en klik dan op de volgende link:\n\n".$site['url']."/account/verify.php?verify_token=".$verify_token."\nWerkt deze link niet? Neem dan contact op met onze klanten service: \n".$site['url']."/contact.php";
    } catch (Exception $e) {
        // Log the error

        $file = fopen("../files/mail-errors.txt","a");
        $ip = $_SERVER['REMOTE_ADDR'];
        $date = date("Y/m/d H:i:s");
        $fdata = "\n\nDate & Time: ".$date.", \nError: {".$mail->ErrorInfo."};\nEmail: ".$email.", Subject: ".$mail->Subject.", Content:".$mail->Body.";";
        fwrite($file,$fdata);
        fclose($file);
    }





    if (isset($_GET['verify_token'])) {
        $verify_token = $_GET['verify_token'];
        
        $stmt = $link->prepare("SELECT verify_token FROM `users` WHERE email = ? AND verify_token = ?");
        $stmt->bind_param("ss", $email, $verify_token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($result->num_rows == 1) {
            // If there are no errors, update the username in the database
            $stmt = $link->prepare("UPDATE `users` SET verify_token = NULL, must_verify = 0 WHERE email = ?");
            $stmt->bind_param("s", $_SESSION['email']);
            $stmt->execute();

            // Set a success message and redirect to the dashboard
            $_SESSION['verify'] == false;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Geen geldige token gebruikt!";
        }
  
    } else if ($_SESSION['send_verify_email'] == false || isset($_POST['resend'])) {
        try {
            $mail->send();
            $_SESSION['send_verify_email'] = true;
        } catch (Exception $e) {
            // Log the error

            $file = fopen("../files/mail-errors.txt","a");
            $ip = $_SERVER['REMOTE_ADDR'];
            $date = date("Y/m/d H:i:s");
            $fdata = "\n\nDate & Time: ".$date.", \nError: {".$mail->ErrorInfo."};\nEmail: ".$email.", Subject: ".$mail->Subject.", Content:".$mail->Body.";";
            fwrite($file,$fdata);
            fclose($file);
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
                        <h2>Verifiëren</h2>
                        <div class="link">
                            <hr>
                            <h5>
                                VERIFIEER JE ACCOUNT
                            </h5>
                            <hr>
                        </div>
                        <div>
                            <?php echo '<h4>Hallo '.$_SESSION['name'].',</h4>'; ?>
                            <p>Je moet je account verifiëren! Ga naar je inbox en klik op de link in de email.</p>
                            <br>
                            <p>Geen link gekregen? Klik op herzend email hieronder.</p>
                        </div>
                        <div class="link">
                            <button type="submit" name="resend"><p>Herzend Email</p></button>
                        </div>
                        <div class="link">
                            <a href="reset-mail.php" class="link-button"><p>Verander Email</p></a>
                        </div>
                        <?php if (isset($error)) : ?>
                            <div>
                                <p class="errors" style="color: darkred;"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="link">
                            <hr>
                            <h5>
                                <a href="logout.php">LOG UIT</a>
                            </h5>
                            <hr>
                        </div>
                    </form>
                    
                </div>
            </div>
        </main>

    </body>

</html>