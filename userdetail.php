<!DOCTYPE html>
<html lang="en">
<head>
  <title>Contact Information</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  

</head>
<body>

<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  /*
   * Basic input validation.
   */
  $contact = mysql_real_escape_string($_REQUEST['contact']);

  $user = new user($_SESSION['login']);
  $contact = new user($contact);

  if( !$contact->id ) {
    print_header("No such user: {$contact}");
    echo "<p>User ".htmlspecialchars($_REQUEST['contact'])." does not exist.";
    print_footer();
    die();
  }


  $contact->load_address();

  print_header("Contact Information for $contact->name");

  echo "
  <div class=\"container\" align=\"center\" style=\"padding: 2rem;\">
    <div class=\"panel panel-default\" style=\"width: 30rem;\">
      <div class=\"panel-heading\">
        <h3>Contact Information</h3>  
      </div>
  
      <div class=\"panel-body\">
            
        <p class=\"card-text\" style=\"padding: 10px 0px 0px 0px;\"><small>Name: </small>$contact->name</p>
        <p class=\"card-text\"><small>Email: </small>$contact->email</p>
      </div>
      </div>";




  

  print_footer()

?>

</body>
</html>