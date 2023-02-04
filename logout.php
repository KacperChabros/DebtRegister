<?php
    session_start();    //unset log variable, destroy session and go to the login.php
    unset($_SESSION['log']);
    unset($_SESSION['debtor']);
    $s=session_destroy();
    header('location: login.php')
?>