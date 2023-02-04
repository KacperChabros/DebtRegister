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
        $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
        if($connection!==false)
        {
            $day=date("d");
            $month=date("m");
            $previousMonth=$month-1;
            $year=date("Y");
            $fullDate=date("Y-m-d");
            $query="SELECT COUNT(*) FROM debtorOfTheMonth WHERE month='$previousMonth' AND year='$year'";  //selecting count of DotM records from previous month
            $result=mysqli_query($connection,$query);
            $record=mysqli_fetch_row($result);
            unset($query, $result);
            if($record[0]==0)   //if there is not any
            {
                $queryToPay="SELECT SUM(value), idDebtor FROM debts WHERE date<='$fullDate' GROUP BY idDebtor ORDER BY idDebtor ASC;";
                $resultToPay=mysqli_query($connection, $queryToPay);    //selecting id and amount that should have been paid by now (groupped by id)
                $toPay=array(); //amount of money that should have been paid
                $idToPay=array();   //debtors that should have paid
                $i=0;
                while($recordToPay=mysqli_fetch_row($resultToPay))
                {
                    $toPay[$i]=$recordToPay[0];     //rewriting data to arrays
                    $idToPay[$i]=$recordToPay[1];
                    $i++;
                }
                unset($queryToPay, $resultToPay, $recordToPay);

                $queryPaid="SELECT SUM(value), idDebtor FROM debts WHERE paid=1 AND date<='$fullDate' GROUP BY idDebtor ORDER BY idDebtor ASC;";
                $resultPaid=mysqli_query($connection, $queryPaid);  //Selecting id and amount that has been paid (groupped by id)
                $paid=array();  //amount of money that has been paid
                $idPaid=array();    //debtors that have paid
                $i=0;
                while($recordPaid=mysqli_fetch_row($resultPaid))
                {
                    $paid[$i]=$recordPaid[0];       //rewriting data to arrays
                    $idPaid[$i]=$recordPaid[1];
                    $i++;
                }
                unset($queryPaid, $resultPaid, $recordPaid, $i);

                $leftToPay=array();     //money that debtors should have paid but haven't yet
                $cleanSheetCount=0;     //count of debtors who have nothing left to pay
                for($i=0; $i<count($idToPay); $i++)
                {
                    for($j=0; $j<count($idPaid); $j++)  //searching debtors that have paid sth
                    { 
                        if($idToPay[$i]==$idPaid[$j])
                        {
                            $leftToPay[$i]=$toPay[$i]-$paid[$j];  //if found, his 'leftToPay' is subtracted by the money he has paid
                            break;
                        }else{
                            $leftToPay[$i]=$toPay[$i];  //if didn't, his 'leftToPay' equals whole amount
                        }
                    }
                    if($leftToPay[$i]==0)
                    {
                        $cleanSheetCount++; //increment count if one has nothing left to pay
                    }
                }
                unset($paid, $idPaid);
                if($cleanSheetCount==1) //if there is only one find him and add DotM to the DB
                {
                    for($i=0; $i<count($idToPay); $i++)
                    {
                        if($leftToPay[$i]==0)
                        {
                            $queryInsert="INSERT INTO debtorOfTheMonth(month, year, idDebtor) VALUES('$previousMonth', '$year', $idToPay[$i]);";
                            mysqli_query($connection, $queryInsert);
                            unset($queryInsert);
                            break;
                        }
                    }
                }else if($cleanSheetCount>1){ //if there is more
                    $inTimers=array();  //those who pay in time
                    $querySumPaid2="SELECT SUM(value), idDebtor FROM debts WHERE paid=1 AND idDebtor IN("; //query that needs ID's - used in line 182
                    for($i=0; $i<count($idToPay); $i++)
                    {
                        if($leftToPay[$i]==0)   //if one has nothing left to pay, add to inTimers and his ID to the query above
                        {
                            $inTimers[$i]=$idToPay[$i];
                            $querySumPaid2.=$inTimers[$i].", ";
                        }
                    }
                    unset($idToPay, $toPay, $leftToPay);
                    $queryPaidEarly="SELECT SUM(value), idDebtor FROM debts WHERE paid=1 AND date>'$fullDate' GROUP BY idDebtor ORDER BY idDebtor ASC;";    //those who pay upfront
                    $resultPaidEarly=mysqli_query($connection, $queryPaidEarly);
                    $idPaidEarly=array(); //debtors who pay upfront
                    $paidEarly=array(); //amount paid upfront by debtor
                    $x=0;
                    $querySumPaid1="SELECT SUM(value), idDebtor FROM debts WHERE paid=1 AND idDebtor IN("; //query that needs ID's - used in line 137
                    while($recordPaidEarly=mysqli_fetch_row($resultPaidEarly))
                    {
                        $idPaidEarly[$x]=$recordPaidEarly[1];   //rewriting data to arrays and ID to the query above
                        $paidEarly[$x]=$recordPaidEarly[0];
                        $querySumPaid1.=$idPaidEarly[$x].", ";
                        $x++;
                    }
                    unset($queryPaidEarly, $resultPaidEarly, $recordPaidEarly, $x);
                    if(count($idPaidEarly)>0) //if there are people who paid upfront
                    {
                        $maxEarly=$paidEarly[0];    //max sum paid upfront
                        $maxId=$idPaidEarly[0];     //id of this debtor
                        $flag=0;    //flag if there are 2 debtors who paid same amount upfront
                        for($i=1; $i<count($idPaidEarly); $i++)
                        {
                            if($maxEarly<$paidEarly[$i])
                            {
                                $maxEarly=$paidEarly[$i];   //rewriting if found greater amount
                                $maxId=$idPaidEarly[$i];
                            }else if($maxEarly==$paidEarly[$i])
                            {
                                $flag=1;
                                break;
                            }
                        }
                        unset($idPaidEarly, $paidEarly);
                        if($maxEarly>0 && !$flag)   //if amount is positive and there arent 2 same max amounts - insert DotM with max ID
                        {
                            $queryInsert="INSERT INTO debtorOfTheMonth(month, year, idDebtor) VALUES('$previousMonth', '$year', $maxId);";
                            mysqli_query($connection, $queryInsert); 
                            unset($queryInsert);
                        }else{ //if there are more than 1 with same max amount
                            $querySumPaid1=substr_replace($querySumPaid1, "", -2); //delete space and coma
                            $querySumPaid1.=") GROUP BY idDebtor ORDER BY idDebtor ASC;";
                            $resultSumPaid=mysqli_query($connection, $querySumPaid1);
                            $idSumPaid=array(); //id of debtors who paid upfront
                            $sumPaid=array();   //sum of all their paid debts
                            $x=0;
                            while($recordSumPaid=mysqli_fetch_row($resultSumPaid))
                            {
                                $sumPaid[$x]=$recordSumPaid[0]; //rewriting to arrays
                                $idSumPaid[$x]=$recordSumPaid[1];
                                $x++;
                            }
                            unset($querySumPaid1, $resultSumPaid, $recordSumPaid, $x);
                            $maxSumPaid=$sumPaid[0];        //max amount paid overall
                            $maxIdSumPaid=$idSumPaid[0];    //id of this debtor
                            $flag1=0;   //flag if there is more than 1 max amount
                            for($i=1; $i<count($idSumPaid); $i++)
                            {
                                if($maxSumPaid<$sumPaid[$i])
                                {
                                    $maxSumPaid=$sumPaid[$i];
                                    $maxIdSumPaid=$idSumPaid[$i];
                                }else if($maxSumPaid==$sumPaid[$i])
                                {
                                    $flag1=1; //if there are same max amounts, set flag
                                    break;
                                }
                            }
                            if($maxSumPaid>0 && !$flag1)    //if sum is positive and flag is not set, insert DotM with max ID
                            {
                                $queryInsert="INSERT INTO debtorOfTheMonth(month, year, idDebtor) VALUES('$previousMonth', '$year', $maxIdSumPaid);"; 
                                mysqli_query($connection, $queryInsert);
                                unset($queryInsert);
                            }else{  //else, insert more DotM
                                for($i=0; $i<count($idSumPaid); $i++)
                                {
                                    $queryInsert="INSERT INTO debtorOfTheMonth(month, year, idDebtor) VALUES('$previousMonth', '$year', $idSumPaid[$i]);";
                                    mysqli_query($connection, $queryInsert);
                                }
                                unset($queryInsert);
                            }
                            unset($idSumPaid, $sumPaid, $maxSumPaid, $maxIdSumPaid, $flag1);
                        }
                        unset($maxEarly, $maxId, $flag);
                    }else{
                        $querySumPaid2=substr_replace($querySumPaid2, "", -2); //subtracting space and coma
                        $querySumPaid2.=") GROUP BY idDebtor ORDER BY idDebtor ASC;";
                        $resultSumPaid=mysqli_query($connection, $querySumPaid2);
                        $idSumPaid=array(); //id of debtors who have clean sheet but didnt pay upfront
                        $sumPaid=array();   //sum of their paid debts
                        $x=0;
                        while($recordSumPaid=mysqli_fetch_row($resultSumPaid))
                        {
                            $sumPaid[$x]=$recordSumPaid[0]; //rewriting to arrays
                            $idSumPaid[$x]=$recordSumPaid[1];
                            $x++;
                        }
                        unset($querySumPaid2, $resultSumPaid, $recordSumPaid, $x);
                        $maxSumPaid=$sumPaid[0];    //max sum paid
                        $maxIdSumPaid=$idSumPaid[0];    //id of debtor
                        $flag1=0;   //flag if there are more with same max sum
                        for($i=1; $i<count($idSumPaid); $i++)
                        {
                            if($maxSumPaid<$sumPaid[$i])
                            {
                                $maxSumPaid=$sumPaid[$i];  
                                $maxIdSumPaid=$idSumPaid[$i];
                            }else if($maxSumPaid==$sumPaid[$i])
                            {
                                $flag1=1;   //if there is more with same max sum, set flag
                                break;
                            }
                        }
                        if($maxSumPaid>0 && !$flag1)    //if sum is positve and flag not set insert DotM with max ID
                        {
                            $queryInsert="INSERT INTO debtorOfTheMonth(month, year, idDebtor) VALUES('$previousMonth', '$year', '$maxIdSumPaid');"; 
                            mysqli_query($connection, $queryInsert);
                            unset($queryInsert);
                        }else{  //insert many DotMs
                            for($i=0; $i<count($idSumPaid); $i++)
                            {
                                $queryInsert="INSERT INTO debtorOfTheMonth(month, year, idDebtor) VALUES('$previousMonth', '$year', $idSumPaid[$i]);";
                                mysqli_query($connection, $queryInsert);
                            }
                            unset($queryInsert);
                        }
                        unset($idSumPaid, $sumPaid, $maxSumPaid, $maxIdSumPaid, $flag1, );
                    }
                }
                unset($day, $year, $month, $previousMonth, $fullDate, $cleanSheetCount);
            }
            unset($record);
        }
        mysqli_close($connection);
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
        <title>Home Page - Debt Register</title>
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
                <h1>Behold, the most punctual debtors of all time</h1>
                <?php
                    $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                    if($connection===false)
                    {
                        die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                    }else{
                        $query="SELECT * FROM leaderboard LIMIT 1";
                        $result=mysqli_query($connection,$query);
                        $record=mysqli_fetch_row($result);
                        if(!empty($record[0]))
                        {
                            echo<<<END
                                <article style="margin: 0; padding: 0;">
                                    <img src="style/img/uploads/$record[3]" alt="$record[0] $record[1]'s photo" class="winner">
                                    <p style="text-align: center;">The most punctual debtor is <b>$record[0] $record[1]</b><br> with an amazing amount of <b>$record[2]</b> Debtor of the Month
                            END;
                                if($record[2]==1)
                                {
                                    echo " title!</p>";
                                }else{
                                    echo " titles!</p>";
                                }
                            echo "</article>";
                            unset($query, $result, $record);

                            $query="SELECT * FROM leaderboard LIMIT 1 OFFSET 1";
                            $result=mysqli_query($connection,$query);
                            $record=mysqli_fetch_row($result);
                            if(!empty($record[0]))
                            {
                                echo<<<END
                                    <article style="width: 50%; margin: 0; padding: 0; display: inline-block; text-align: center; float: left;">
                                        <img src="style/img/uploads/$record[3]" alt="$record[0] $record[1]'s photo" class="secondPlace">
                                        <p style="text-align: center;">The second most punctual debtor is <b>$record[0] $record[1]</b><br>holding <b>$record[2]</b> Debtor of the Month
                                END;
                                if($record[2]==1)
                                {
                                    echo " title!</p>";
                                }else{
                                    echo " titles!</p>";
                                }
                                echo "</article>";
                                unset($query, $result, $record);

                                $query="SELECT * FROM leaderboard LIMIT 1 OFFSET 2";
                                $result=mysqli_query($connection,$query);
                                $record=mysqli_fetch_row($result);
                                if(!empty($record[0]))
                                {
                                    echo<<<END
                                        <article style="width: 50%; margin: 0; padding: 0; display: inline; text-align: center;">
                                            <img src="style/img/uploads/$record[3]" alt="$record[0] $record[1]'s photo" class="thirdPlace">
                                            <p style="text-align: center;">The third most punctual debtor is <b>$record[0] $record[1]</b><br> owning <b>$record[2]</b> Debtor of the Month
                                    END; 
                                    if($record[2]==1)
                                    {
                                        echo " title!</p>";
                                    }else{
                                        echo " titles!</p>";
                                    }
                                    echo "</article>";
                                }
                            }
                            unset($query, $result, $record);
                        }
                    }
                    mysqli_close($connection);
                ?>
            </section>
        </div>
    </body>
</html>