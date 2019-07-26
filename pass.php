<!DOCTYPE html>
<html>
<head>
<title>Change Password</title>

<script type="text/javascript">
function validatePassword() {
var currentPassword,newPassword,confirmPassword,output = true;

currentPassword = document.frmChange.currentPassword;
newPassword = document.frmChange.newPassword;
confirmPassword = document.frmChange.confirmPassword;

if(!currentPassword.value) {
    currentPassword.focus();
    document.getElementById("currentPassword").innerHTML = "required";
    output = false;
}
else if(!newPassword.value) {
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

 print_header("Edit Password");


if (count($_POST) > 0) {
    $result = mysql_query("SELECT * from users WHERE id='$user->id'");
    $row = mysql_fetch_array($result);

    if (SHA1($_POST['currentPassword']) == $row['pass']) {
        mysql_query("UPDATE users set pass='" . SHA1($_POST["newPassword"]) . "' WHERE id='$user->id'");
        $message = "Password Changed";
        header("refresh:5; url=pass");
    } else
        $message = "Current Password is not correct";
}
?>
<body>
    <form name="frmChange" method="post" action="" onSubmit="return validatePassword()">
        <div class="container">
        <div class="message"><?php if(isset($message)) { echo $message; } ?></div>
            <h2>Change Password</h2>
            <div class="list-group">
                <li class="list-group-item">
                    <h4 class="list-group-item-heading">Current Password</h4>
                    <input type="password"
                        name="currentPassword" class="txtField" /><span
                        id="currentPassword" class="required"></span>
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
                        value="Change" class="btn btn-primary">
                </li>               
            </div>
            
        </div>
        
    </form>
</body>
</html>