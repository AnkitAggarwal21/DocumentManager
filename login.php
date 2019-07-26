<?php
  require('lib/config.inc.php');
  require('lib/sql.inc.php');

  /*
   * Basic input validation
   */
  $login = mysql_real_escape_string($_REQUEST['login']);
  $pass  = mysql_real_escape_string($_REQUEST['pass']);

  $result = mysql_query("SELECT id FROM users WHERE user='$login'");
  if( mysql_num_rows($result) == 0 ) {
      header("Location: index?errorMssg=".urlencode("Invalid loginID"));
      exit;
  }

  $result = mysql_query("SELECT pass!={$cfg['pwfunc']}('$pass') FROM users WHERE user='$login'");
  $row = mysql_fetch_array($result);

  if( $row[0] != 0 ) {

      //header("Location: index");
      header("Location: index?errorMssg=".urlencode("Incorrect Password"));
      exit;
  }

  $result = mysql_query("SELECT id,user,name FROM users WHERE user='$login'");
  $row = mysql_fetch_array($result);

  $_SESSION['id']    = $row['id'];
  $_SESSION['name']  = $row['name'];
  $_SESSION['login'] = $row['user'];

  header("Location: main");
?>
