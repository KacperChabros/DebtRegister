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
        <title>Manage Debtors - Debt Register</title>
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
                <h1>Add new debtor</h1>
                <form onsubmit="return validate(this.id)" id="add" action="manageDebtors.php" method="POST" enctype="multipart/form-data">
                    <table class="add">
                        <tr>
                            <td>
                                <input type="text" id="addFname" class="addRequired" name="addFname" placeholder="First Name" onblur="valFname(this.id)">
                                <p class="invalidSmall" id="maddFname"></p>
                            </td>
                            <td>
                                <input type="text" id="addLname" class="addRequired" name="addLname" placeholder="Last Name" onblur="valLname(this.id)">
                                <p class="invalidSmall" id="maddLname"></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <abbr title="Birthday"><input type="date" id="addBday" class="addRequired" name="addBday" max="<?php echo date("Y-m-d");?>"></abbr>
                            </td>
                            <td>
                                <input type="text" id="addOrigin" class="addRequired" name="addOrigin" placeholder="Town of origin" onblur="valOrigin(this.id)">
                                <p class="invalidSmall" id="maddOrigin"></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="addUni" name="addUni" placeholder="University" onblur="valUni(this.id)">
                                <p class="invalidSmall" id="maddUni"></p>
                            </td>
                            <td>
                                <input type="text" id="addOccupation" name="addOccupation" placeholder="Occupation" onblur="valOccupation(this.id)">
                                <p class="invalidSmall" id="maddOccupation"></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="addPhone" name="addPhone" placeholder="Phone Number" onblur="valPhone(this.id)">
                                <p class="invalidSmall" id="maddPhone"></p>
                            </td>
                            <td>
                                <abbr title="Upload picture"><input type="file" id="addPhoto" name="addPhoto" placeholder="Photo"></abbr>
                                <p class="invalidSmall" id="maddPhoto"></p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" name="submit1" value="Add Debtor">
                </form>
                <div id="addMessage">
                    <?php
                        if(isset($_POST['submit1']))
                        {
                            $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                            if($connection===false)
                            {
                                die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                            }else{
                                $fname=$_POST['addFname'];
                                $lname=$_POST['addLname'];
                                $bday=$_POST['addBday'];
                                $origin=$_POST['addOrigin'];
                                $uni=$_POST['addUni'];                               
                                $occupation=$_POST['addOccupation'];                               
                                $phone=$_POST['addPhone'];
                                if($_FILES['addPhoto']['name']!="") //if file is set
                                {                          
                                    $targetDir="style/img/uploads/";
                                    $targetFile=$targetDir.basename($_FILES["addPhoto"]["name"]);
                                    $photo=$_FILES['addPhoto']['name'];
                                    $uploadOk=1;
                                    $imageFileType=strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
                                    $check=getimagesize($_FILES["addPhoto"]["tmp_name"]); //Check if image file is an actual image or fake image
                                    if($check !== false){
                                        $uploadOk = 1;
                                    }else{
                                        echo "<script>document.getElementById('maddPhoto').innerHTML='File is not an image.'</script>";
                                        $uploadOk = 0;
                                    }

                                    if (file_exists($targetFile)){ // Check if file already exists
                                        echo "<script>document.getElementById('maddPhoto').innerHTML='Sorry, file already exists.'</script>";
                                        $uploadOk = 0;
                                    }

                                    if ($_FILES["addPhoto"]["size"] > 25000000){ // Check file size
                                        echo "<script>document.getElementById('maddPhoto').innerHTML='Sorry, your file is too large.'</script>";
                                        $uploadOk = 0;
                                    }

                                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                                    && $imageFileType != "gif" ){ // Allow certain file formats
                                    echo "<script>document.getElementById('maddPhoto').innerHTML='Sorry, only JPG, JPEG, PNG & GIF files are allowed.'</script>";
                                    $uploadOk = 0;
                                    }
                                    if ($uploadOk == 0){ // Check if $uploadOk is set to 0 by an error
                                        echo '<p class="invalid">Sorry, your file was not uploaded and user was not added. Please, try again.</p>';
                                      
                                    }else{ // if everything is ok, try to upload file
                                        if (move_uploaded_file($_FILES["addPhoto"]["tmp_name"], $targetFile)) 
                                        {
                                            $query="INSERT INTO debtors(firstName, lastName, birthday, townOfOrigin, university, occupation, number, photo) VALUES('$fname', '$lname', '$bday', '$origin', NULLIF('$uni', ''), NULLIF('$occupation', ''), NULLIF('$phone', ''), NULLIF('$photo', ''));";
                                            mysqli_query($connection,$query);
                                            unset($query);
                                            echo "<p class='valid'>$fname $lname has been added</p>";
                                        }else{
                                          echo "<script>document.getElementById('maddPhoto').innerHTML='Sorry, there was an error uploading your file.'</script>";
                                        }
                                    }
                                }else{
                                    $query="INSERT INTO debtors(firstName, lastName, birthday, townOfOrigin, university, occupation, number) VALUES('$fname', '$lname', '$bday', '$origin', NULLIF('$uni', ''), NULLIF('$occupation', ''), NULLIF('$phone', ''));";
                                    echo "<p class='valid'>$fname $lname has been added</p>";
                                    mysqli_query($connection,$query);
                                    unset($query);
                                } 
                                unset($fname, $lname, $bday, $origin, $uni, $occupation, $phone, $photo);
                            }
                            mysqli_close($connection);
                        }
                    ?>
                </div>
            </section>
            <section>
                <h1>Change data</h1>
                <form action="manageDebtors.php" method="POST" id="formMargin">
                    <label for="idDebtor"><h3 class="selectHeader">Select your debtor: </h3></label>
                    <select id="idDebtor" name="idDebtor" class="customSelect">
                        <?php
                            $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                            if($connection===false)
                            {
                                die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                            }else{
                                $query="SELECT idDebtor, firstName, lastName FROM debtors;";
                                $result=mysqli_query($connection,$query); //show select with debtors
                                while($record=mysqli_fetch_row($result))
                                {
                                    echo "<option value='$record[0]'>$record[0]. $record[1] $record[2]</option>";
                                }
                                unset($query, $result, $record); 
                            }
                            mysqli_close($connection);
                        ?>
                    </select>
                    <input type="submit" name="submit2" value="Change row" class="smallerSubmit">
                </form>
                    <?php
                        $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                        if($connection===false)
                        {
                            die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                        }else{
                            $queryCount="SELECT COUNT(*) FROM debtors;";
                            $resultCount=mysqli_query($connection,$queryCount);
                            $recordCount=mysqli_fetch_row($resultCount);
                            if($recordCount[0]>0)
                            {
                                if(!empty($_POST['idDebtor']))
                                {
                                    $id=$_POST['idDebtor'];
                                }else if(isset($_SESSION['debtor'])){
                                    $id=$_SESSION['debtor'];
                                }else{
                                    $id=1;
                                }
                                $_SESSION['debtor']=$id;
                                $query="SELECT * FROM debtors WHERE idDebtor='$id';";
                                $result=mysqli_query($connection,$query);
                                $record=mysqli_fetch_row($result);
                                $today=date("Y-m-d");
                                echo<<<END
                                <table class="debtorData">
                                    <tr>
                                        <th>id</th>
                                        <th>First name</th>
                                        <th>Last name</th>
                                        <th>Birthday</th>
                                        <th>Town of origin</th>
                                        <th>University</th>
                                        <th>Occupation</th>
                                        <th>Phone number</th>
                                        <th>Photo</th>
                                    </tr>
                                    <tr>
                                        <th colspan="9">Current data</th>
                                    </tr>
                                    <tr>
                                        <td>$record[0]</td>
                                        <td>$record[1]</td>
                                        <td>$record[2]</td>
                                        <td>$record[3]</td>
                                        <td>$record[4]</td>
                                        <td>$record[5]</td>
                                        <td>$record[6]</td>
                                        <td>$record[7]</td>
                                        <td>$record[8]</td>
                                    </tr>
                                    <tr>
                                        <th colspan="9">Change data</th>
                                    </tr>
                                    <tr>
                                        <td>$record[0]</td>
                                        <form onsubmit="return validate(this.id)" id="change" action="manageDebtors.php" method="POST" enctype="multipart/form-data">
                                            <td><input type="text" id="changeFname" class="changeData" class="changeRequired" name="changeFname" value="$record[1]" onblur="valFname(this.id)"></td>
                                            <td><input type="text" id="changeLname" class="changeData" class="changeRequired" name="changeLname" value="$record[2]" onblur="valLname(this.id)"></td>
                                            <td><input type="date" id="changeBday" class="changeData" class="changeRequired" name="changeBday" value="$record[3]" max="$today"></td>
                                            <td><input type="text" id="changeOrigin" class="changeData" class="changeRequired" name="changeOrigin" value="$record[4]" onblur="valOrigin(this.id)"></td>
                                            <td><input type="text" id="changeUni" class="changeData" name="changeUni" value="$record[5]" onblur="valUni(this.id)"></td>
                                            <td><input type="text" id="changeOccupation" class="changeData" name="changeOccupation" value="$record[6]" onblur="valOccupation(this.id)"></td>
                                            <td><input type="text" id="changePhone" class="changeData" name="changePhone" value="$record[7]" onblur="valPhone(this.id)"></td>
                                            <td><abbr title="If new file is not selected, the old one will remain"><input type="file" id="changePhoto" class="changeData" name="changePhoto" value="$record[8]"></abbr></td>
                                    </tr>
                                    <tr>
                                        <th colspan="9">Alerts</th>
                                    </tr>
                                    <tr>
                                            <td><p class="invalidSmall" id="mchange"></p></td>
                                            <td><p class="invalidSmall" id="mchangeFname"></p></td>
                                            <td><p class="invalidSmall" id="mchangeLname"></p></td>
                                            <td><p class="invalidSmall" id="mchangeBday"></p></td>
                                            <td><p class="invalidSmall" id="mchangeOrigin"></p></td>
                                            <td><p class="invalidSmall" id="mchangeUni"></p></td>
                                            <td><p class="invalidSmall" id="mchangeOccupation"></p></td>
                                            <td><p class="invalidSmall" id="mchangePhone"></p></td>
                                            <td><p class="invalidSmall" id="mchangePhoto"></p></td>
                                    </tr>
                                    <tr>
                                            <td colspan="9"><div id="changeMessage"></div></td>
                                    </tr>
                                    <tr>
                                            <td colspan="9"><input type="submit" name="submit3" value="Change debtor's data" style="margin: 0;"></td>
                                        </form>
                                    </tr>
                                </table>       
                                END;
                                if(isset($_POST['submit3']))
                                {
                                    $fname=$_POST['changeFname'];
                                    $lname=$_POST['changeLname'];
                                    $bday=$_POST['changeBday'];
                                    $origin=$_POST['changeOrigin'];
                                    $uni=$_POST['changeUni'];                               
                                    $occupation=$_POST['changeOccupation'];                               
                                    $phone=$_POST['changePhone'];
                                    if($_FILES['changePhoto']['name']!="") //if file is set
                                    {                          
                                        $targetDir="style/img/uploads/";
                                        $targetFile=$targetDir.basename($_FILES["changePhoto"]["name"]);
                                        $photo=$_FILES['changePhoto']['name'];
                                        $uploadOk=1;
                                        $imageFileType=strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
                                        $check=getimagesize($_FILES["changePhoto"]["tmp_name"]); //Check if image file is an actual image or fake image
                                        if($check !== false){
                                            $uploadOk = 1;
                                        }else{
                                            echo "<script>document.getElementById('mchangePhoto').innerHTML='File is not an image.'</script>";
                                            $uploadOk = 0;
                                        }

                                        if (file_exists($targetFile)){ // Check if file already exists
                                            echo "<script>document.getElementById('mchangePhoto').innerHTML='Sorry, file already exists.'</script>";
                                            $uploadOk = 0;
                                        }

                                        if ($_FILES["changePhoto"]["size"] > 25000000){ // Check file size
                                            echo "<script>document.getElementById('mchangePhoto').innerHTML='Sorry, your file is too large.'</script>";
                                            $uploadOk = 0;
                                        }

                                        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                                        && $imageFileType != "gif" ){ // Allow certain file formats
                                        echo "<script>document.getElementById('mchangePhoto').innerHTML='Sorry, only JPG, JPEG, PNG & GIF files are allowed.'</script>";
                                        $uploadOk = 0;
                                        }
                                        if ($uploadOk == 0){ // Check if $uploadOk is set to 0 by an error
                                            echo '<p class="invalid">Sorry, your file was not uploaded and user was not changed. Please, try again.</p>';
                                        
                                        }else{ // if everything is ok, try to upload file
                                            if (move_uploaded_file($_FILES["changePhoto"]["tmp_name"], $targetFile)) 
                                            {
                                                $query1="UPDATE debtors SET firstName='$fname', lastName='$lname', birthday='$bday', townOfOrigin='$origin', university=NULLIF('$uni', ''), occupation=NULLIF('$occupation', ''), number=NULLIF('$phone', ''), photo=NULLIF('$photo', '') WHERE idDebtor=$id;";
                                                mysqli_query($connection,$query1);
                                                unset($query1);
                                                echo "<p class='valid'>Data of $id. user has been changed. Refresh to see the results</p>";
                                            }else{
                                            echo "<script>document.getElementById('mchangePhoto').innerHTML='Sorry, there was an error uploading your file.'</script>";
                                            }
                                        }
                                        unset($targetDir, $targetFile);
                                    }else{
                                        $query1="UPDATE debtors SET firstName='$fname', lastName='$lname', birthday='$bday', townOfOrigin='$origin', university=NULLIF('$uni', ''), occupation=NULLIF('$occupation', ''), number=NULLIF('$phone', '') WHERE idDebtor=$id;";
                                        echo "<p class='valid'>Data of $id. user has been changed. Refresh to see the results</p>";  
                                        mysqli_query($connection,$query1);
                                        unset($query1);
                                    } 
                                    unset($fname, $lname, $bday, $origin, $uni, $occupation, $phone, $photo);
                                }
                                unset($id, $query, $result, $record, $today);
                            }
                            unset($queryCount, $resultCount, $recordCount);
                        }
                        mysqli_close($connection);
                    ?>
            </section> 
            <section>
                <h1>Delete debtor</h1>
                <form action="manageDebtors.php" method="POST">
                    <label for="deleteDebtor"><h3 class="selectHeader">Select debtor to delete: </h3></label>
                    <select id="deleteDebtor" name="idDebtorDelete" class="customSelect">
                        <?php
                            $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                            if($connection===false)
                            {
                                die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                            }else{
                                $query="SELECT idDebtor, firstName, lastName FROM debtors;";    //show select with debtors
                                $result=mysqli_query($connection,$query);
                                while($record=mysqli_fetch_row($result))
                                {
                                    echo "<option value='$record[0]'>$record[0]. $record[1] $record[2]</option>";
                                }
                                unset($query, $result, $record);
                            }
                            mysqli_close($connection);
                        ?>
                    </select><br>
                    <input type="submit" name="submit4" value="Delete debtor">
                </form>
                <?php
                    $connection=@mysqli_connect("localhost", "root", "", "debtRegister");
                    if($connection===false)
                    {
                        die("ERROR: Couldn't connect to the DataBase. ".mysqli_connect_error());
                    }else{
                        if(!empty($_POST['submit4']))   //when previous form submitted, delete debtor with selected id
                        {
                            $delId=$_POST['idDebtorDelete'];    
                            $query="DELETE FROM debtors WHERE idDebtor=$delId";
                            if(!mysqli_query($connection, $query))
                            {
                                echo "<p class='valid'>$delId. debtor has been deleted</p>";
                            }else{
                                echo "<p class='invalid'>Operation did not succeed</p>";
                            }
                            unset($delId, $query);
                        }
                    }
                    mysqli_close($connection);
                ?>
            </section>     
        </div>
        <script>
                var vFname=/^[A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+([ ][A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+)?$/;  //RegExp for inputs
                var vLname=/^[A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+([-][A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+)?$/;
                var vTown=/^[A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+([ -][A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+)?$/;
                var vTown2=/^[A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+[ ][a-z]+[ ][A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+$/;
                var vUni=/^([A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+([ -](([A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+)|([i][m][\.])|([w])|([w][e])|([o][f])|([i][n])))+)?$/;
                var vOccupation=/^([A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+([ ][A-ZĆŁÓŚŻŹ][a-ząęćłńóśżź]+)*)?$/;
                var vPhone=/^(\d{9})?$/;

            function valFname(id){ //functions that check onblur if field matches regexp
                var mId="m"+id;
                if(!document.getElementById(id).value.match(vFname))
                {document.getElementById(mId).innerHTML="Invalid first name (e.g. John)";
                }else{
                    document.getElementById(mId).innerHTML="";
                }
            }
            function valLname(id){
                var mId="m"+id;
                if(!document.getElementById(id).value.match(vLname))
                {document.getElementById(mId).innerHTML="Invalid last name (e.g. Kowalski / Nowak-Górska)";
                }else{
                    document.getElementById(mId).innerHTML="";
                }
            }
            function valOrigin(id){
                var mId="m"+id;
                if(!document.getElementById(id).value.match(vTown) && !document.getElementById(id).value.match(vTown2))
                {document.getElementById(mId).innerHTML="Invalid town (e.g. Warszawa / Kazimierz Dolny)";
                }else{
                    document.getElementById(mId).innerHTML="";
                }
            }
            function valUni(id){
                var mId="m"+id;
                if(!document.getElementById(id).value.match(vUni))
                {document.getElementById(mId).innerHTML="Invalid University (e.g. Warsaw University of Technology / Uniwersytet im. Marii Skłodowskiej-Curie w Lublinie)";
                }else{
                    document.getElementById(mId).innerHTML="";
                }
            }
            function valOccupation(id){
                var mId="m"+id;
                if(!document.getElementById(id).value.match(vOccupation))
                {document.getElementById(mId).innerHTML="Invalid Occupation (e.g. Junior Developer)";
                }else{
                    document.getElementById(mId).innerHTML="";
                }
            }
            function valPhone(id){
                var mId="m"+id;
                if(!document.getElementById(id).value.match(vPhone))
                {document.getElementById(mId).innerHTML="Invalid Phone Number (e.g. 123456789)";
                }else{
                    document.getElementById(mId).innerHTML="";
                }
            }
            
                //function that validates whole form, check if required fields are field and if they match regexp. If yes, submit
                function validate(id){
                var mId=id+"Message";
                if(document.getElementById(id+"Fname").value.length==0 || document.getElementById(id+"Lname").value.length==0 || document.getElementById(id+"Bday").value.length==0 ||document.getElementById(id+"Origin").value.length==0)
                {
                    document.getElementById(mId).innerHTML="";
                    document.getElementById(mId).innerHTML="<p class='invalid'>Type in required data</p>";
                    var arrR=document.getElementsByClassName(id+"Required");
                    for(var i=0; i<arrR.length; i++)
                    {
                        arrR[i].style.border="4px solid red";
                    }
                    return false;
                }else if(!document.getElementById(id+"Fname").value.match(vFname) || !document.getElementById(id+"Lname").value.match(vLname) || (!document.getElementById(id+"Origin").value.match(vTown) && !document.getElementById(id+"Origin").value.match(vTown2)) || !document.getElementById(id+"Uni").value.match(vUni) || !document.getElementById(id+"Occupation").value.match(vOccupation) || !document.getElementById(id+"Phone").value.match(vPhone)){
                    document.getElementById(mId).innerHTML="";
                    document.getElementById(mId).innerHTML="<p class='invalid'>Invalid data. Please, try again</p>";
                    return false;
                }else{
                    document.getElementById(mId).innerHTML="";
                    return true;
                }
            }
        </script>
    </body>
</html>