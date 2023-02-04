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
        <title>Manage Debts - Debt Register</title>
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
                <h1>Add new debt</h1>
                <form onsubmit="return validate(this.id)" action="manageDebts.php" method="POST" id="add">
                    <table class="add">
                        <tr>
                            <td>
                                <input type="text" id="addCause" name="addCause" class="addRequired" placeholder="Cause of debt" onblur="valHarm(this.id)">
                                <p id="maddCause" class="invalidSmall"></p>
                            </td>
                            <td>
                                <select id="addIdDebtor" name="addIdDebtor" class="customSelect" class="addRequired">
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
                        </tr>
                        <tr>
                            <td><abbr title="Due"><input type="date" id="addDate" name="addDate" class="addRequired"></abbr></td>
                            <td>
                                <input type="number" id="addValue" name="addValue" class="addRequired" placeholder="Amount of money (PLN)" min="0" step="0.01" onblur="valValue(this.id)">
                                <p class="invalidSmall" id="maddValue"></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="addPaid"><h3 class="addRequired">Has the debt been paid yet?</h3></label>
                                <select id="addPaid" name="addPaid" class="customSelect" class="addRequired">
                                    <option value="0" selected>No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </td>
                            <td>
                                <textarea id="addDescr" name="addDescr" maxlength="250" placeholder="Description (optional)(max 250 characters)" onblur="valHarm(this.id)"></textarea>
                                <p class="invalidSmall" id="maddDescr"></p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" name="submit1" value="Add new debt">
                </form>
                <div id="addMessage">
                    <?php
                        $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                        if($connection===false)
                        {
                            die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                        }else{
                            $queryCount="SELECT COUNT(*) FROM debtors;";
                            $resultCount=mysqli_query($connection, $queryCount);
                            $recordCount=mysqli_fetch_row($resultCount);
                            if($recordCount[0]!=0)
                            {
                                if(isset($_POST['submit1']))    //check if there are debtors in the DB, yes=add debt    no=message
                                {
                                    $cause=$_POST['addCause'];
                                    $idDebtor=$_POST['addIdDebtor'];
                                    $date=$_POST['addDate'];
                                    $value=$_POST['addValue'];
                                    $paid=$_POST['addPaid'];
                                    $desc=$_POST['addDescr'];
                                    $query="INSERT INTO debts(cause, idDebtor, date, value, paid, description) VALUES('$cause', $idDebtor, '$date', $value, $paid, NULLIF('$desc', ''));";
                                    mysqli_query($connection, $query);
                                    echo "<p class='valid'>New debt has been added</p>";
                                    unset($cause, $idDebtor, $date, $value, $paid, $desc, $query); 
                                }
                            }else{
                                echo "<p class='invalid'>new debt cannot be added due to lack of debtors. Add new debtor and come back</p>";
                            }
                            unset($queryCount, $resultCount, $recordCount);
                        }
                        mysqli_close($connection);
                    ?>
                </div>
            </section>
            <section>
                <h1>Change or delete debt</h1>
                <h3>Which debts would you like to see?</h3>
                <form onsubmit="return valDates('searchStartDate', 'searchEndDate', this.id);" id="search" action="manageDebts.php" method="POST">   <!-- no need to validate whole form as its only for showing data on site -->
                    <table class="search">
                        <tr>
                            <th>Cause containing given word</th>
                            <th>debtor(s)</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Amount</th>
                            <th>Paid</th>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="searchCause" class="searchInput" name="searchCause" placeholder="Word to search for" onblur="valHarm(this.id)">
                                <p class="invalidSmall" id="msearchCause"></p>
                            </td>
                            <td>
                                <select id="searchId" name="searchId[]" class="customSelectTall" multiple size="5">
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
                            <td><input type="date" id="searchStartDate" name="searchStartDate" class="searchInput"></td>
                            <td>
                                <input type="date" id="searchEndDate" name="searchEndDate" class="searchInput">
                                <p class="invalidSmall" id="msearchEndDate"></p>
                            </td>
                            <td>
                                <input type="number" id="searchValue" name="searchValue" class="searchInput" placeholder="Amount of money (PLN)" min="0" step="0.01" onblur="valValue(this.id)"><br>
                                <select id="greaterLess" name="greaterLess" class="customSelectTall">
                                    <option value="=">Equal</option>
                                    <option value=">">Greater than</option>
                                    <option value="<">Less than</option>
                                    <option value=">=">Greater than or equal</option>
                                    <option value="<=">Less than or equal</option>
                                </select>
                                <p class="invalidSmall" id="msearchValue"></p>
                            </td>
                            <td>
                                <select id="searchPaid" name="searchPaid" class="customSelectTall">
                                    <option value="3">Any</option>
                                    <option value="2">Yes</option>
                                    <option value="1">No</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" id="submit2" name="submit2" value="Search for debts">
                </form><br><br>
                <div id="searchMessage">
                </div>
                <div id="changeMessage">
                    <?php
                        if(isset($_POST['changeSub']))
                        {
                            $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                            if($connection===false)
                            {
                                die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                            }else{
                                $id=$_POST['changeDel'];        //update Data
                                $cCause=$_POST['changeCause'];
                                $cIdDebtor=$_POST['changeIdDebtor'];
                                $cDate=$_POST['changeDate'];
                                $cValue=$_POST['changeValue'];
                                $cPaid=$_POST['changePaid'];
                                $cDesc=$_POST['changeDescr'];
                                $query="UPDATE debts SET cause='$cCause', idDebtor=$cIdDebtor, date='$cDate', value=$cValue, paid=$cPaid, description=NULLIF('$cDesc', '') WHERE idDebt=$id;";
                                if(mysqli_query($connection,$query))
                                {
                                    echo "<p class='valid'>Data of $id. debt has been successfully updated</p>";
                                }else{
                                    echo "<p class='invalid'>A problem occurred updating the data</p>";
                                }
                                unset($id,$cCause,$cIdDebtor,$cDate,$cValue,$cPaid,$cDesc,$query);
                            }
                            mysqli_close($connection); 
                        }else if(isset($_POST['delSub']))
                        {
                            $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                            if($connection===false)
                            {
                                die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                            }else{
                                $id=$_POST['changeDel'];    //Delete selected debt
                                $query="DELETE FROM debts WHERE idDebt=$id;";
                                if(mysqli_query($connection,$query))
                                {
                                    echo "<p class='valid'>$id. debt has been deleted</p>";
                                }else{
                                    echo "<p class='invalid'>A problem occurred deleting the data</p>";
                                }
                                unset($id, $query);
                            }
                            mysqli_close($connection); 
                        } 
                    ?>
                </div>
                <?php
                    $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                    if($connection===false)
                    {
                        die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                    }else{
                        if(isset($_POST['submit2']))
                        {
                            $querySearch="SELECT * FROM debts";
                            $flag=0;
                            if(!empty($_POST['searchCause']) || !empty($_POST['searchId']) || !empty($_POST['searchStartDate']) || !empty($_POST['searchEndDate']) || !empty($_POST['searchValue']) || !empty($_POST['searchPaid']))
                            {
                                if(!empty($_POST['searchCause']))
                                {
                                    $cause=$_POST['searchCause'];
                                    $cond1="(cause LIKE '%$cause%')";
                                    $flag=1;
                                    unset($cause);
                                }
                                if(!empty($_POST['searchId']))
                                {
                                    $cond2="(idDebtor IN(";
                                    for($i=0; $i<count($_POST['searchId']); $i++)
                                    {
                                        $cond2.="'".$_POST['searchId'][$i]."', ";
                                    }
                                    $cond2=substr($cond2, 0, -2);
                                    $cond2.="))";
                                    $flag=1;
                                }
                                if(!empty($_POST['searchStartDate']))
                                {
                                    $startDate=$_POST['searchStartDate'];
                                    $cond3="(date>='$startDate')";
                                    unset($startDate);
                                    $flag=1;
                                }
                                if(!empty($_POST['searchEndDate']))
                                {
                                    $endDate=$_POST['searchEndDate'];
                                    $cond4="(date<='$endDate')";
                                    unset($endDate);
                                    $flag=1;
                                }
                                if(!empty($_POST['searchValue']))
                                {
                                    $amount=$_POST['searchValue'];
                                    $operation=$_POST['greaterLess'];
                                    $cond5="(value$operation$amount)";
                                    unset($operation, $amount);
                                    $flag=1;
                                }   
                                if(!empty($_POST['searchPaid']))
                                {
                                    switch($_POST['searchPaid'])
                                    {
                                        case 2:
                                            $cond6="(paid=1)";
                                            $flag=1;
                                            break;
                                        case 1:
                                            $cond6="(paid=0)"; 
                                            $flag=1;
                                            break;
                                    }
                                }
                            }
                            if($flag)
                            {
                                $querySearch.=" WHERE ";
                                if(isset($cond1))
                                {
                                    $querySearch.=$cond1;
                                }
                                if(isset($cond2))
                                {
                                    if(isset($cond1))
                                    {
                                        $querySearch.=" AND $cond2";
                                    }else{
                                        $querySearch.=$cond2;
                                    }
                                }
                                if(isset($cond3))
                                {
                                    if(isset($cond2) || isset($cond1))
                                    {
                                        $querySearch.=" AND $cond3";
                                    }else{
                                        $querySearch.=$cond3;
                                    }
                                }
                                if(isset($cond4))
                                {
                                    if(isset($cond1) || isset($cond2) || isset($cond3))
                                    {
                                        $querySearch.=" AND $cond4";
                                    }else{
                                        $querySearch.=$cond4;
                                    }
                                }
                                if(isset($cond5))
                                {
                                    if(isset($cond1) || isset($cond2) || isset($cond3) || isset($cond4))
                                    {
                                        $querySearch.=" AND $cond5";
                                    }else{
                                        $querySearch.=$cond5;
                                    }
                                }
                                if(isset($cond6))
                                {
                                    if(isset($cond1) || isset($cond2) || isset($cond3) || isset($cond4) || isset($cond5))
                                    {
                                        $querySearch.=" AND $cond6";
                                    }else{
                                        $querySearch.=$cond6;
                                    }
                                }
                            }
                            unset($cond1, $cond2, $cond3, $cond4, $cond5, $cond6, $flag);
                            //showing every debt (selected by query) with inputs to change data. 1 button to change, 1 to delete
                            $result=mysqli_query($connection,$querySearch);
                            echo<<<END
                                <table class="debtData">
                                <tr>
                                    <th>id</th>
                                    <th>Cause</th>
                                    <th>Debtor</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Description</th>
                                    <th>Change</th>
                                    <th>Delete</th>
                                </tr>
                            END;
                            while($record=mysqli_fetch_row($result))
                            {
                                echo<<<END
                                    <tr>
                                        
                                        <form onsubmit="return validate(this.id)" id="change$record[0]" action="manageDebts.php" method="POST"> 
                                            <td>
                                                <label for="radio$record[0]">$record[0].</label><input type="radio" id="radio$record[0]" name="changeDel" value="$record[0]" onchange="radioC(this.id);">
                                            </td>
                                            <td>
                                                <input type="text" id="change$record[0]Cause" name="changeCause" class="delInput" class="changeRequired" placeholder="Cause of debt" value="$record[1]" onblur="valHarm(this.id)" disabled>
                                                <p class="invalidSmall" id="mchange$record[0]Cause"></p>
                                            </td>
                                            <td>
                                                <select id="change$record[0]IdDebtor" name="changeIdDebtor" class="delInput" class="customSelect" class="changeRequired" disabled>
                                END;
                                                    $querySel="SELECT idDebtor, firstName, lastName FROM debtors;"; //show select with debtors
                                                    $resultSel=mysqli_query($connection,$querySel);
                                                    while($recordSel=mysqli_fetch_row($resultSel))
                                                    {
                                                        if($record[2]==$recordSel[0])
                                                        {echo "<option value='$recordSel[0]' selected>$recordSel[0]. $recordSel[1] $recordSel[2]</option>";}else{echo "<option value='$recordSel[0]'>$recordSel[0]. $recordSel[1] $recordSel[2]</option>";}
                                                    }
                                                    unset($querySel,$resultSel,$recordSel);
                                echo<<<END
                                                </select>
                                            </td>
                                            <td><abbr title="Due"><input type="date" id="change$record[0]Date" name="changeDate" class="delInput" class="changeRequired" value="$record[3]" disabled></abbr></td>
                                            <td>
                                                <input type="number" id="change$record[0]Value" name="changeValue" class="delInput" class="changeRequired" placeholder="Amount of money (PLN)" min="0" step="0.01" onblur="valValue(this.id)" value="$record[4]" disabled>
                                                <p class="invalidSmall" id="mchangeValue"></p>
                                            </td>
                                            <td>
                                                <select id="change$record[0]Paid" name="changePaid" class="delInput" class="customSelect" class="addRequired"  disabled>
                                END;
                                                if($record[5]==1)
                                                {
                                                    echo "<option value='0'>No</option>";
                                                    echo "<option value='1'selected>Yes</option>";
                                                }else{
                                                    echo "<option value='0' selected>No</option>";
                                                    echo "<option value='1'>Yes</option>";
                                                }
                                echo<<<END
                                                </select>
                                            </td>
                                            <td>
                                                <textarea id="change$record[0]Descr" name="changeDescr" maxlength="250" placeholder="Description (optional)(max 250 characters)" class="delInput" disabled onblur="valHarm(this.id)">$record[6]</textarea>
                                                <p class="invalidSmall" id="mchange$record[0]Descr"></p>
                                            </td>
                                            <td>
                                                <input type="submit"  id="changeSub" name="changeSub" value="Change" class="delInput" disabled>
                                            </td>
                                            <td>
                                                <input type="submit"  id="delSub" name="delSub" value="Delete" class="delInput" disabled>
                                            </td>
                                        </form>
                                    </tr>
                                END;
                            }
                            echo "</table>";
                        }
                    }
                    unset($querySearch, $result, $record);
                    mysqli_close($connection);
                ?>
            </section>
        </div>
    </body>
    <script>
        var vValue=/^\d{1,}([.]\d{2})?$/; //RegExp

        function valValue(id){  //function that check onblur if field matches regexp
            var mId="m"+id;
            if(document.getElementById(id).value<0)
            {
                document.getElementById(mId).innerHTML="Amount should be greater or equal 0";
            }else if(!document.getElementById(id).value.match(vValue))
            {document.getElementById(mId).innerHTML="Invalid amount (e.g. 16.50)";
            }else{
                document.getElementById(mId).innerHTML="";
            }
        }
        
        function valHarm(id){   //check if user doesnt want to DROP or DELETE sth
            var text=document.getElementById(id).value.toLowerCase();
            if(text.includes("delete") || text.includes("drop"))
            {
                document.getElementById("m"+id).innerHTML="Don't even try (Security Violation)";
                return false;
            }else{
                document.getElementById("m"+id).innerHTML="";
                return true;
            }
        }

        function valDates(idStart, idEnd, id){  //checks if end date is greater than start date
            if(document.getElementById(idStart).value>document.getElementById(idEnd).value)
            {
                document.getElementById("m"+idEnd).innerHTML="End Date should be greater than Start Date";
                return false;
            }else{
                document.getElementById("m"+idEnd).innerHTML="";
                return valHarm(id+"Cause");
            }
        }

        function validate(id){    //function that validates whole form, check if required fields are field and if they match regexp. If yes, submit
            var shortId=id; 
            //Ifs that deletes numbers that are at the end of id
            if(!isNaN(id.charAt(id.length-1)) && !isNaN(id.charAt(id.length-2)) && !isNaN(id.charAt(id.length-3)) && !isNaN(id.charAt(id.length-4)) && !isNaN(id.charAt(id.length-5)))      
            {
                shortId=id.slice(0,-5);
            }else if(!isNaN(id.charAt(id.length-1)) && !isNaN(id.charAt(id.length-2)) && !isNaN(id.charAt(id.length-3)) && !isNaN(id.charAt(id.length-4)))
            {
                shortId=id.slice(0,-4);
            }else if(!isNaN(id.charAt(id.length-1)) && !isNaN(id.charAt(id.length-2)) && !isNaN(id.charAt(id.length-3)))
            {
                shortId=id.slice(0,-3);
            }else if(!isNaN(id.charAt(id.length-1)) && !isNaN(id.charAt(id.length-2)))
            {
                shortId=id.slice(0,-2);
            }else if(!isNaN(id.charAt(id.length-1))){
                shortId=id.slice(0,-1);
            }
            var mId=shortId+"Message";
            if((document.getElementById(id+"Cause").value.length==0) || (document.getElementById(id+"IdDebtor").value.length==0) || (document.getElementById(id+"Date").value.length==0) || (document.getElementById(id+"Value").value.length==0) /*|| ((document.getElementById(id+"Yes").checked==false) && (document.getElementById(id+"No").checked==false))*/)
            {
                document.getElementById(mId).innerHTML="";
                document.getElementById(mId).innerHTML="<p class='invalid'>Type in required data</p>";
                var arrR=document.getElementsByClassName(shortId+"Required");
                /*if(arrR.length<=0)
                {
                    var arrR=document.getElementsByClassName("delInput");
                }*/
                //var arrR=document.querySelectorAll('.'+shortId+'Required');
                for(var i=0; i<arrR.length; i++)
                {
                    arrR[i].style.border="4px solid red";
                }
                if(shortId=="change")
                {
                    alert("Type in required data (cause, debtor, date, amount, paid)");
                }
                return false;
            }else if((document.getElementById(id+"Value").value.length<0) || !(document.getElementById(id+"Value").value.match(vValue)) || isNaN(document.getElementById(id+"Value").value)){
                document.getElementById(mId).innerHTML="";
                document.getElementById(mId).innerHTML="<p class='invalid'>Invalid data. Please, try again</p>";
                return false;
            }else if(!valHarm(id+"Cause")){
                document.getElementById(mId).innerHTML="<p class='invalid'>Security violation</p>";
                return false;
            }else{
                document.getElementById(mId).innerHTML="";
                return true;
            }
        }

        function radioC(id){   //function that disables/enables inputs depending on radio status      
                var arr=[];
                var arrRadio=[];                                                //stores radio elements
                var arrInp=[];                                                  //stores inputs from displayed table
                var arrSel=document.body.getElementsByTagName("select");        //stores select elements
                var arrTexta=document.body.getElementsByTagName("textarea");    //stores textarea elements
                var r=0;
                var rChecked=0;
                var inp=0;
                arr=document.body.getElementsByTagName("input");    //creating an array with every input
                for(var i=0; i<arr.length; i++)
                {
                    if(arr[i].getAttribute("type")=="radio")
                    {
                        arrRadio[r++]=arr[i];                       //selecting radio inputs
                    }else if(arr[i].getAttribute("class")=="delInput"){ //selecting other inputs from displayed table
                        arrInp[inp++]=arr[i];
                    }
                }
                for(var i=0; i<arrRadio.length; i++)    //if radio is checked then enable inputs and buttons, disable others
                {
                    if(arrRadio[i].id!=id)          //this if is here to uncheck radio buttons when other is checked - name attribute doesnt work
                    {
                        arrRadio[i].checked=false;
                    }
                    if(arrRadio[i].checked){
                        for(var j=i*5; j<i*5+5; j++)
                        {
                            arrInp[j].disabled=false;       //inputs
                        }
                        for(var j=i*2+5; j<i*2+7; j++)
                        {
                            arrSel[j].disabled=false;       //selects
                        }
                        arrTexta[i+1].disabled=false;       //textarea
                    }else{  
                        
                        for(var j=i*5; j<i*5+5; j++)
                        {
                            arrInp[j].disabled=true;        //inputs
                        }
                        for(var j=i*2+5; j<i*2+7; j++)
                        {
                            arrSel[j].disabled=true;        //selects
                        }
                        arrTexta[i+1].disabled=true;        //textarea
                    }
                } 
            }
    </script>
</html>