<?php
    session_start();
    if(!isset($_SESSION['log'])) //if log variable not set, go to the login.php
    {
        header('location: login.php');
        exit;
    }else{
        if((time()-$_SESSION['lastLoginTS'])>900)   //auto logout after 15 minutes (TS=timestamp)
        {
            header('location: logout.php');
            exit;
        }else{
            $_SESSION['lastLoginTS']=time();
        }
    }
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="style/fontello/css/fontello.css" type="text/css" />
        <link rel="icon" type="image/x-icon" href="style/img/favicon.ico">
        <title>Debts Navigation - Debt Register</title>
    </head>
    <body>
        <div id="container">
            <header>
                <img src="style/img/debt-register.png" alt="Debt Register">
            </header>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="icon-doc"></i>News</a></li>
                    <li>
                        <a href="debtorsNav.php"><i class="icon-meh"></i>Debtors</a>
                        <ul class="dropdown">
                            <li><a href="debtors.php"><i class="icon-address-book-o"></i>See your debtors</a></li>
                            <li><a href="manageDebtors.php"><i class="icon-user-plus"></i>Manage debtors</a></li>
                            <li><a href="debtorTop.php"><i class="icon-calendar"></i>Debtor of the month</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="debtsNav.php"><i class="icon-money"></i>Debts</a>
                        <ul class="dropdown">
                            <li><a href="debts.php"><i class="icon-money-1"></i>Browse debts</a></li>
                            <li><a href="manageDebts.php"><i class="icon-wallet"></i>Manage debts</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="settingsNav.php"><i class="icon-adult"></i>My account</a>
                        <ul class="dropdown">
                            <li><a href="settings.php"><i class="icon-cog"></i>Settings</a></li>
                            <li><a href="logout.php"><i class="icon-logout"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <section>
                <a href="debts.php" class="usualLink"><h1>Browse debts</h1></a>
                <a href="manageDebts.php" class="usualLink"><h1>Manage debts</h1></a>
            </section>
        </div>
    </body>
</html>