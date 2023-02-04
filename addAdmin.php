<!DOCTYPE html>
<?php
    if(!empty($_POST['login']) && !empty($_POST['pass'])) //checks if POST variables are empty, if not: add the User to the DB
    { 
        $login=$_POST['login'];
        $pass=sha1($_POST['pass']);
        $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
        if($connection===false)
        {
            unset($login);
            unset($pass);
            die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
        }else
        {
            $query="SELECT login FROM dbUsers";     //checks if this user exists in DB
            $result=mysqli_query($connection, $query);  
            $bool=0;
            while($record=mysqli_fetch_row($result))
            {
                if($record[0]==$login)
                {
                    echo "User with this login already exists";
                    $bool=1;
                    break;
                }
            }
            if(!$bool)
            {
                $query="INSERT INTO dbUsers(login, password) VALUES('$login', '$pass');";   //adding User
                mysqli_query($connection, $query);

                

                echo "User added";
            }
            mysqli_close($connection);
        }
        unset($bool);
        unset($login);
        unset($pass);
    }
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <form action="addAdmin.php" method="POST">
            Login: <input type="text" id="login" name="login"><br>
            Password: <input type="password" id="pass" name="pass"><br>
            <input type="submit">
        </form>
    </body>
</html>