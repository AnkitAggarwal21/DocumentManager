<?php
  require('lib/config.inc.php');
  require('lib/sql.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  global $cfg;

  $tmp = explode("/", $_SERVER['REQUEST_URI']);
  $doc_id = intval( $tmp[sizeof($tmp)-2] );

  $user = new user($_SESSION['login']);

  if( may_read($user->id,$doc_id) ) {
    $res = @mysql_query("SELECT d.id AS id,d.name AS name,d.type AS type,d.size AS size,c.content AS content FROM documents AS d LEFT JOIN documents_content AS c ON d.id=c.id WHERE d.id=$doc_id");
    $row = @mysql_fetch_array($res);
    Header("Content-Type: $row[type]");
    echo "". base64_decode($row['content']) ."";
    exit;
  }

  print_header("Permission Denied");
  echo "<h2 align=\"center\">Permission denied</h2>\n";
  print_footer();
  
 
?>
