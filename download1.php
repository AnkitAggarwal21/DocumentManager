<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  global $cfg;

  /*
   * Basic input validation.
   */
  //$doc_id = intval($_POST['docid']);
  $id = intval($_POST['id']); 
  $path = $_POST['path'];

  $user = new user($_SESSION['login']);
  
 // $query = @mysql_query("SELECT * from actions_content where id = '$id' AND docid = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  //while($ros = mysql_fetch_array($query))
  //{
    //$path = $ros['content'];
    $rest = end(explode('/',$path));
    $rest1 = end(explode('|',$rest));
    header('content-Disposition: attachment; filename = "'.$rest1.'"');
    header('content-type:appliction/octent-strem');
    header('content-length='.filesize($path));
    readfile($path);
  //}


?>
