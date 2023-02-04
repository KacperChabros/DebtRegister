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
        <title>Debts - Debt Register</title>
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
            <section id="choice">
                <h1>Debts can be browsed here</h1>
                <h3>Please, select type of debts to browse, particular debtors as well as order type</h3>
                <h4>Hold down the Ctrl (windows) / Command (Mac) button to select multiple options</h4>
                <form action="debts.php" method="POST">
                    <table class="formTable">
                        <tr>
                            <th>Type of debt</th>
                            <th>Debtors</th>
                            <th>Order by 1</th>
                            <th>Order by 2</th>
                        </tr>
                        <tr>
                            <td>
                                <select id="debtTypes" name="debtTypes[]" class="customSelectTall" multiple size="5" required>
                                    <option value="1" selected>All debts</option>
                                    <option value="2">Not paid</option>
                                    <option value="3">Paid</option>
                                    <option value="4">Paid in advance</option>
                                    <option value="5">Upcoming debts</option>
                                </select>
                            </td>
                            <td>
                                <select id="debtorsId" name="debtorsId[]" class="customSelectTall" multiple size="5" required>
                                    <?php
                                        $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                                        if($connection===false)
                                        {
                                            die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                                        }else{
                                            $query="SELECT idDebtor, firstName, lastName FROM debtors;"; //show select with debtors
                                            $result=mysqli_query($connection,$query);
                                            while($record=mysqli_fetch_row($result))
                                            {
                                                echo "<option value='$record[0]'>$record[0]. $record[1] $record[2]</option>";
                                            }
                                            unset($query,$result,$record);
                                        }
                                        mysqli_close($connection);
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select id="order1" name="order1" class="customSelectTall" required>
                                    <option value="idDebt">Debt id</option>
                                    <option value="cause">Cause</option>
                                    <option value="idDebtor">Debtor's id</option>
                                    <option value="date">Date</option>
                                    <option value="value">Amount</option>
                                    <option value="paid">Paid</option>
                                    <option value="description">Description</option>
                                </select><br><br>
                                <select id="order1Asc" name="order1Asc" class="customSelectTall" required>
                                    <option value="DESC">Descending</option>
                                    <option value="ASC">Ascending</option>
                                </select>
                            </td>   
                            <td>
                                <select id="order2" name="order2" class="customSelectTall" required>
                                    <option value="idDebt">Debt id</option>
                                    <option value="cause">Cause</option>
                                    <option value="idDebtor">Debtor's id</option>
                                    <option value="date" selected>Date</option>
                                    <option value="value">Amount</option>
                                    <option value="paid">Paid</option>
                                    <option value="description">Description</option>
                                </select><br><br>
                                <select id="order2Asc" name="order2Asc" class="customSelectTall" required>
                                    <option value="DESC">Descending</option>
                                    <option value="ASC">Ascending</option>
                                </select>
                            </td>   
                        </tr>
                    </table>
                    <input type="submit" name="submit1" value="Show Debts">
                </form>  
            </section>
            <section id="showDebts">
                <?php
                    if(isset($_POST['submit1']))
                    {
                        if(!empty($_POST['debtTypes']) && !empty($_POST['debtorsId']) && !empty($_POST['order1']) && !empty($_POST['order1Asc']))
                        {
                            $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                            if($connection===false)
                            {
                                die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                            }else{
                                $types=array();
                                $debtors=array();
                                $today=date("Y-m-d");
                                $query="SELECT debts.idDebt, debts.cause, debtors.firstName, debtors.lastName, debts.date, debts.value, debts.paid, debts.description FROM debts JOIN debtors ON debts.idDebtor=debtors.idDebtor WHERE ";    //beginning of the query

                                $cond1="(debts.idDebtor IN(";
                                for($i=0; $i<count($_POST['debtorsId']); $i++)  //stating condition with selected debtors
                                {
                                    $debtors[$i]=$_POST['debtorsId'][$i];
                                    if($i==(count($_POST['debtorsId'])-1))
                                    {
                                        $cond1.="$debtors[$i]"."))";
                                    }else{
                                        $cond1.="$debtors[$i]".", ";
                                    }
                                }
                                if($_POST['debtTypes'][0]!=1)   //if type is not 1 - all debts
                                {
                                    $query.=$cond1." AND (";    //adding condition with debtors to query
                                    for($i=0; $i<count($_POST['debtTypes']); $i++)
                                    {
                                        $types[$i]=$_POST['debtTypes'][$i];
                                            if($i!=0)
                                            {
                                                $query.=" OR ";
                                            }
                                            switch($types[$i])  
                                            {                           //adding conditions to query based on selected types
                                                case 2:
                                                    $query.="(debts.paid=0 AND debts.date<='$today')";
                                                    break;
                                                case 3:
                                                    $query.="(debts.paid=1 AND debts.date<='$today')";
                                                    break;
                                                case 4:
                                                    $query.="(debts.paid=1 AND debts.date>'$today')";
                                                    break;
                                                case 5:
                                                    $query.="(debts.paid=0 AND debts.date>'$today')";
                                                    break;
                                            }
                                    }
                                    $query.=")";   
                                }else{
                                    $query.=$cond1;
                                }
                                //add order to query
                                $query.=" ORDER BY debts.".$_POST['order1']." ".$_POST['order1Asc'].", debts.".$_POST['order2']." ".$_POST['order2Asc'].";";
                                $result=mysqli_query($connection, $query);
                                echo<<<END
                                    <table class="showMoney">
                                        <tr>
                                            <th>id</th>
                                            <th>Cause</th>
                                            <th>Debtor</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Paid</th>
                                            <th>Description</th>
                                        </tr>
                                END;
                                $paid=0; $paidEarly=0; $notPaid=0; $upcoming=0;
                                while($record=mysqli_fetch_row($result))
                                {
                                    if($record[6]==1 && $record[4]<=$today) //adding different classes depending on date and if debt is paid
                                    {
                                        echo "<tr class='paid'>";
                                        $paid+=$record[5];
                                    }else if($record[6]==1 && $record[4]>$today)
                                    {
                                        echo "<tr class='paidEarly'>";
                                        $paidEarly+=$record[5];
                                    }else if($record[6]==0 && $record[4]<=$today)
                                    {
                                        echo "<tr class='notPaid'>";
                                        $notPaid+=$record[5];
                                    }else{
                                        echo "<tr class='upcoming'>";
                                        $upcoming+=$record[5];
                                    }
                                    //show data
                                    //echo<<<END  
                                    echo "<td>$record[0]</td>";
                                    echo "<td>$record[1]</td>";
                                    echo "<td>$record[2] $record[3]</td>";
                                    echo "<td>$record[4]</td>";
                                    echo "<td>$record[5]</td>";
                                    //END;
                                    if($record[6]==1){
                                        echo "<td>Yes</td>";
                                    }else{
                                        echo "<td>No</td>";
                                    }
                                    echo<<<END
                                            <td>$record[7]</td>
                                        </tr>
                                    END;
                                }
                                echo "</table>";
                                echo<<<END
                                    <table>
                                    <tr>
                                END;
                                //messages under table with summary of debts
                                if($notPaid!=0)
                                {
                                    echo<<<END
                                        <th>
                                            <h1 class="mNotPaid">Total of $notPaid PLN left to pay! <br>Please, settle this payment quickly!</h1>
                                        </th>
                                    END;
                                }
                                if($paid!=0)
                                {
                                    echo<<<END
                                        <th>
                                            <h3 class="mPaid">Total of $paid PLN of previous debts already paid, good job!</h3>
                                        </th>
                                    END;
                                }
                                if($paidEarly!=0)
                                {
                                    echo<<<END
                                        <th>
                                            <h3 class="mPaidEarly">Total of $paidEarly PLN of upcoming debts paid early, excellent!</h3>
                                        </th>
                                    END;
                                }
                                if($upcoming!=0)
                                {
                                    echo<<<END
                                        <th>
                                            <h1 class="mUpcoming">Total of $upcoming PLN is approaching!</h1>
                                        </th>
                                    END;
                                }
                                unset($types, $debtors, $today, $cond1, $query, $result, $record, $result, $paid, $paidEarly, $notPaid, $upcoming);
                            }
                           mysqli_close($connection); 
                        }else{
                            echo "<p class='invalid'>Selection of type, debtor and order by is required</p>";
                        }
                    }
                ?>
            </section>
        </div>
    </body>
</html>