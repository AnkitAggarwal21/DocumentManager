<?
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  global $cfg;

  /*
   * Basic input validation.
   */
  $doc_id = intval($_REQUEST['doc_id']);

  $user = new user($_SESSION['login']);
  $query = @mysql_query("SELECT * from documents_content where id = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  while($ros = mysql_fetch_array($query))
  {
    $content = $ros['content'];
    unlink($content);
  }
  
  @mysql_query("DELETE from documents_content where id = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  @mysql_query("DELETE from documents where id = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  @mysql_query("DELETE from documents_info where id = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  @mysql_query("DELETE from documents_keywords where id = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  @mysql_query("DELETE from documents_log where document = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  @mysql_query("DELETE from ACL where document_id = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  $query1 = @mysql_query("SELECT * from actions_content where docid = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  while($ros = mysql_fetch_array($query1))
  {
    $content = $ros['content'];
    $myArray = explode(',', $content);
    foreach ($myArray as $key) {
      # code...
    unlink($key);
  }
    
  }
  @mysql_query("DELETE from actions_content where docid = '$doc_id' ") or die ('Error updating database: '.mysql_error());
  
  if(mysql_error() == 0)
  {
    header("Location: list");
  }


?>
