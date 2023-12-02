<?php 

    if ($page['path_lvl'] == 2) {
        require_once("../files/config.php");
    } else if ($page['path_lvl'] == 3) {
        require_once("../../files/config.php");
    }

    //ini_set('display_errors', 0);

    // Start a session
    session_start();

    if (
        $page['name'] != "login" &&
        $page['name'] != "register" &&
        $page['name'] != "forgot_pass"
    ) {
        if (isset($_SESSION['loggedin'])) {
            $loggedin = true;
        } else {
            header("Location: ".$path."admin/login.php");
        }
    } else if ($page['name'] == "login") {
        if (isset($_SESSION['loggedin'])) {
            $loggedin = true;
            header("Location: ".$path."admin/content/dashboard.php");
        } else {
            $loggedin = false;
        }
    } else if ($page['name'] == "register") {
        if (isset($_SESSION['loggedin'])) {
            $loggedin = true;
            header("Location: ".$path."admin/content/dashboard.php");
        } else {
            $loggedin = false;
        }
    } else if ($page['name'] == "forgot_pass") {
        if (isset($_SESSION['loggedin'])) {
            $loggedin = true;
            header("Location: ".$path."admin/content/dashboard.php");
        } else {
            $loggedin = false;
        }
    }

?>