<?php
    session_start();
    if(isset($_SESSION['log'])) //checking if log var is set -  1=go to website  0=check credentials
    {
        header('location: index.php');
        exit();
    }else if(isset($_POST['username']) && isset($_POST['pass']))
    {
        $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
        if($connection===false)
        {
            die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
        }else
        {
            $query="SELECT login, password FROM dbUsers;";
            $result=mysqli_query($connection, $query);
            while($record=mysqli_fetch_array($result))
            {
                if($_POST['username']==$record[0] && sha1($_POST['pass'])==$record[1]) //check: positive = login,  negative=message
                {
                    $_SESSION['log']=$_POST['username'];
                    $_SESSION['lastLoginTS']=time();
                    mysqli_close($connection);
                    header('location: index.php');
                    exit();
                }
            }
            if(!isset($_SESSION['log']))
            {
                echo '<script>window.onload=(function(){document.getElementById("formHeader").innerHTML = "Invalid credentials. Please, try again";})</script>';
            }  
        }  
    }
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style/login.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="style/img/favicon.ico">
        <title>Debt Register - login</title>
    </head>
    <body>
        <header>
            <h1>I am pleased to welcome you on the Debt Register website</h1>
        </header>
        <section>
            
            <form action="login.php" method="POST">
                <h2 id="formHeader">Please, log in</h2>
                <input type="text" id="username" name="username" placeholder="Username"><br>
                <input type="password" id="pass" name="pass" placeholder="Password"><br>
                <input type="submit" value="Login">
            </form>
        </section>
        <footer>
            <p>This Register is classified. Should you feel that you ought to have access to this data please contact our administrator.</p>
        </footer>
    </body>
</html>