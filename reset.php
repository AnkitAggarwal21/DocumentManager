<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>

<script type="text/javascript">
function validatePassword() {
var newPassword,confirmPassword,output = true;

newPassword = document.frmChange.newPassword;
confirmPassword = document.frmChange.confirmPassword;

if(!newPassword.value) {
    newPassword.focus();
    document.getElementById("newPassword").innerHTML = "required";
    output = false;
}
else if(!confirmPassword.value) {
    confirmPassword.focus();
    document.getElementById("confirmPassword").innerHTML = "required";
    output = false;
}
if(newPassword.value != confirmPassword.value) {
    newPassword.value="";
    confirmPassword.value="";
    newPassword.focus();
    document.getElementById("confirmPassword").innerHTML = "not same";
    output = false;
}   
return output;
}
</script>
</head>

<?php

    require('lib/config.inc.php');
    require('lib/auth.inc.php');
    require('lib/classes.inc.php');
    require('lib/functions.inc.php');

    $user = new user($_SESSION['login']);

    $ids = $_REQUEST['userid'];
    $row = mysql_fetch_array(mysql_query("SELECT * from users WHERE id='$ids'"));
    $user_name = $row['name'];

    if(!($user->id==1)) {
        print_header("Access Dennied!");
        exit;
    }
    
    print_header("Reset Password");

    if (count($_POST) > 0) {

   if (isset($_POST['newPassword'])&&isset($_POST['confirmPassword']))
   {        
    @mysql_query("UPDATE users set pass = '" . SHA1($_POST["newPassword"]) . "' WHERE id = '$ids' ") or die ('Error updating database: '.mysql_error());
        echo "Password Changed";
        header("refresh:2; url=reset?userid=$ids");
    }
       
    }

?>


<body>
    <form name="frmChange" method="post" action="" onSubmit="return validatePassword()">

        <div class="container">
            <div class="message"><?php if(isset($message)) { echo $message; } ?></div>
            <h2>Reset Password</h2>
            <div class="list-group">
                <li class="list-group-item">
                    <h4 class="list-group-item-heading">For <?php echo $user_name; ?></h4>
                </li>
                <li class="list-group-item">
                    <h4 class="list-group-item-heading">New Password</h4>
                    <input type="password" name="newPassword"
                        class="txtField" /><span id="newPassword"
                        class="required"></span>
                </li>
                <li class="list-group-item">
                    <h4 class="list-group-item-heading">Confirm Password</h4>
                    <input type="password" name="confirmPassword"
                    class="txtField" /><span id="confirmPassword"
                    class="required"></span>
                </li>
                <li class="list-group-item">
                    <input type="submit" name="submit"
                        value="Reset" class="btn btn-primary">
                </li>
            </div>
        </div>
    </form>
</body>
</html>








 