<!DOCTYPE html>
<html lang="en">
<head>
  <title>Upload Document</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   
</head>
<body>



<?php
    require('lib/config.inc.php');
    require('lib/auth.inc.php');
    require('lib/classes.inc.php');
    require('lib/functions.inc.php');
  

    $user = new user($_SESSION['login']);

    print_header("Main");

    echo "<div align=\"center\">\n";
    echo "<img src=\"pix/train.jpg\" height=\"292\" width=\"500\" alt=\"[ {$cfg['site_name']} Document Management ]\">\n";
    echo "</div>\n";

    print_footer();
?>

</body>
</html>