<!DOCTYPE html>
<html lang="en">
<head>
  <title>DMRC DOC</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

 
</head>

<body>

<body class="text-center">
  

<?php
    require('lib/config.inc.php');
    require('lib/classes.inc.php');
    require('lib/functions.inc.php');

    // $user = new user($login);
    $errorMssg = (isset($_REQUEST['errorMssg']) ? ($_REQUEST['errorMssg']) : "");

    print_login_header("Login");

    echo "<div align=\'center\'>\n";
    echo "<div class=\"card card-container\" align=\"center\">
            
            <img src=\"pix/dmrc.png\" height=\"100\" width=\"100\">
            <h3 id=\"profile-name\" class=\"profile-name-card\">Delhi Metro Document Manager</h3><br>";

    echo "<h2>Log In</h2>";
	echo "<form action=\"login\" method=\"post\">";
	echo "<div class=\"container\" align=\"center\">";
    echo "$errorMssg";
    echo "<input type=\"text\" name=\"login\" class=\"form-control mb-2 mr-sm-2\" placeholder=\"Username\" style=\"width:350px;\">";
    echo "<input type=\"password\" name=\"pass\" class=\"form-control mb-2 mr-sm-2\" placeholder=\"Password\" style=\"width:350px;\"><br>";   
	echo "<input type=\"Submit\" class=\"btn btn-primary mb-2\" value=\"Login\">\n";
	echo "</form>";
	echo "</div>";



    print_login_footer();
?>

</body>
</html>