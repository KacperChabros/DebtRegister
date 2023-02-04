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
        <title>Settings - Debt Register</title>
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
            <div>
                <?php
                    echo "<h1>Hello ".$_SESSION['log'].", what would you like to do?</h1>";
                ?>
            </div>
            <section>
                <ul>
                    <li>
                        Change the password
                        <form action="settings.php" method="POST">
                            <input type="password" id="currPass" name="currPass" placeholder="Current Password"><br>
                            <input type="password" id="pass1" name="pass1" placeholder="New Password"><br>
                            <input type="password" id="pass2" name="pass2" placeholder="Repeat New Password"><br>
                            <input type="submit" value="Change">
                        </form>
                        <?php
                            //changing password
                            if(!empty($_POST['currPass']) && !empty($_POST['pass1']) && !empty($_POST['pass2']))
                            {
                                $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                                if($connection===false)
                                {
                                    die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                                }else{                                    
                                        $log=$_SESSION['log'];
                                        $query="SELECT password FROM dbUsers WHERE login='$log';";
                                        $result=mysqli_query($connection, $query);
                                        $record=mysqli_fetch_row($result);
                                        if(sha1($_POST['currPass'])==$record[0]) //check if current password is correct
                                        {
                                            if(sha1($_POST['pass1'])==sha1($_POST['pass2'])) //check if passwords are the same
                                            {
                                                $check=$_POST['pass1']; //check if password contains at least 1 lowercase, 1 uppercase, 1 digit, 1 special character and is longer than 8 characters
                                                $pattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,}$/";
                                                if(preg_match($pattern, $check))
                                                {
                                                    $pass=sha1($_POST['pass1']);    //Change the password
                                                    $query="UPDATE dbUsers SET password='$pass' WHERE login='$log';";
                                                    unset($pass);
                                                    mysqli_query($connection, $query);
                                                    echo "<p class='valid'>Password has been changed</p>";
                                                    
                                                }else{
                                                    echo "<p class='invalid'>Password should contain at least 1 lowercase, 1 uppercase, 1 digit, 1 special character and be longer than 8 characters</p>";
                                                }
                                                unset($check);
                                            }else{
                                                echo "<p class='invalid'>Passwords are not the same</p>";
                                            }
                                        }else{
                                            echo "<p class='invalid'>Invalid password</p>";
                                        }
                                    unset($log, $query, $result, $record);
                                    mysqli_close($connection);
                                }
                            }
                        ?>
                    </li>
                    <li>
                        Add another admin
                        <form action="settings.php" method="POST">
                            <input type="text" id="newLogin" name="newLogin" placeholder="Login"><br>
                            <input type="password" id="newPass1" name="newPass1" placeholder="Password"><br>
                            <input type="password" id="newPass2" name="newPass2" placeholder="Repeat Password"><br>
                            <input type="submit" value="Add User">
                        </form>
                        <?php
                            //adding new admin
                            if(!empty($_POST['newLogin']) && !empty($_POST['newPass1']) && !empty($_POST['newPass2'])) //check if fields are not empty
                            {
                                $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                                if($connection===false)
                                {
                                    die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                                }else{
                                    $login=$_POST['newLogin'];
                                    $query="SELECT login FROM dbUsers";     //checks if this user exists in DB
                                    $result=mysqli_query($connection, $query);  
                                    $bool=0;
                                    while($record=mysqli_fetch_row($result))
                                    {
                                        if($record[0]==$login)
                                        {
                                            echo "<p class='invalid'>User with this login already exists</p>";
                                            $bool=1;
                                            break;
                                        }
                                    }
                                    if(!$bool)
                                    {
                                        if(sha1($_POST['newPass1'])==sha1($_POST['newPass2'])) //check if passwords are the same
                                        {
                                            $check=$_POST['newPass1']; //check if password contains at least 1 lowercase, 1 uppercase, 1 digit, 1 special character and is longer than 8 characters
                                            $pattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,}$/";
                                            if(preg_match($pattern, $check))
                                            {
                                                $newPass=sha1($_POST['newPass1']);
                                                $query="INSERT INTO dbUsers(login, password) VALUES('$login', '$newPass');";   //adding User
                                                mysqli_query($connection, $query);
                                                unset($newPass);
                                                echo "<p class='valid'>$login has been added</p>";
                                                    
                                            }else{
                                                echo "<p class='invalid'>Password should contain at least 1 lowercase, 1 uppercase, 1 digit, 1 special character and be longer than 8 characters</p>";
                                            }
                                            unset($check);
                                        }else{
                                            echo "<p class='invalid'>Passwords are not the same</p>";
                                        }
                                    }
                                    unset($login, $query, $result, $record);
                                }
                                mysqli_close($connection);
                            }
                        ?>
                    </li>
                    <li>
                        Delete User
                        
                            <?php
                                $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                                if($connection===false)
                                {
                                    die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                                }else{      
                                    echo "<table>";                           
                                    $query="SELECT idDbUser, login FROM dbUsers;";  //show users
                                    $result=mysqli_query($connection,$query);   
                                    while($record=mysqli_fetch_row($result))
                                    {
                                        echo<<<END
                                            <tr>
                                                <td class="first"><label for="radio$record[0]"><h4><input type="radio" id="radio$record[0]" name="del" onchange="radioC($record[0])">$record[0]. $record[1]</h4></label></td>
                                                <td class="second">
                                                    <form action="settings.php" method="POST">
                                                        <input type="password" id="passs$record[0]" class="delPass" name="delPass$record[0]" placeholder="$record[1]'s password" disabled><br>
                                                        <input type="submit" id="sub$record[0]" class="delSub" value="Delete user" disabled>
                                                    </form>
                                                </td>
                                                <td class="third">
                                        END;    
                                                    if(!empty($_POST["delPass$record[0]"]))
                                                    {
                                                        $query1="SELECT password FROM dbUsers WHERE login='$record[1]';";
                                                        $result1=mysqli_query($connection, $query1);
                                                        $record1=mysqli_fetch_row($result1);
                                                        if(sha1($_POST["delPass$record[0]"])==$record1[0])
                                                        {
                                                            $query2="DELETE FROM dbUsers WHERE idDbUser='$record[0]'";
                                                            $result2=mysqli_query($connection, $query2);
                                                            echo "<p class='valid'>User $record[1] has been deleted</p>";
                                                            unset($result2, $query2);
                                                        }else{
                                                            echo "<p class='invalid'>Invalid credentials. Please, try again</p>";
                                                        }
                                                        unset($result1,$query1,$record1);
                                                    }
                                        echo<<<END
                                                </td>
                                            </tr>
                                        END;    
                                    }

                                    echo "</table>"; 
                                    unset($query, $result, $record); 
                                }
                                mysqli_close($connection);
                            ?>
                        </table>
                    </li>
                </ul>
            </section>
        </div>
        <script>
            function radioC(num){   //function that disables/enables inputs depending on radio status
                let pass="passs"+num;
                let sub="sub"+num;
                var arr=[];
                var arrRadio=[];    //stores radio elements
                var arrPass=[];     //stores inputs with password
                var arrSub=[];      //stores submit buttons
                var r=0;
                var p=0;
                var s=0;
                arr=document.body.getElementsByTagName("input");    //creating an array with every input
                for(var i=0; i<arr.length; i++) 
                {  
                    if(arr[i].getAttribute("type")=="radio")    //select radio inputs
                    {
                        arrRadio[r++]=arr[i];
                    }else if(arr[i].getAttribute("class")=="delPass"){ //select inputs password with class delPass (Passwords of users to delete)
                        arrPass[p++]=arr[i];
                    }else if(arr[i].getAttribute("class")=="delSub"){ //select buttons of users to delete
                        arrSub[s++]=arr[i];
                    }
                }
                for(var i=0; i<arrRadio.length; i++)    //if radio is checked then enable pass and button, disable others
                {
                    if(arrRadio[i].checked){
                        arrPass[i].disabled=false;
                        arrSub[i].disabled=false;
                    }else{
                        arrPass[i].disabled=true;
                        arrSub[i].disabled=true;
                    }
                }    
            }    
        </script>
    </body>
</html>