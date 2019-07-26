<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  /*
   * Basic input validation.
   */
  $user = new user($_SESSION['login']);

  $doc_id = intval(isset($_REQUEST['doc_id']) ? ($_REQUEST['doc_id']) : "");
  $info   = mysql_real_escape_string(isset($_REQUEST['info']) ? ($_REQUEST['info']) : "");
  $level   = mysql_real_escape_string(isset($_REQUEST['level']) ? ($_REQUEST['level']) : "");
  $keywords   = mysql_real_escape_string(isset($_REQUEST['keywords']) ? ($_REQUEST['keywords']) : "");
  $for_id = isset($_POST['For']) ? ($_POST['For']) : "";
  $date   = mysql_real_escape_string(isset($_REQUEST['date']) ? ($_REQUEST['date']) : "");
  $letter   = mysql_real_escape_string(isset($_REQUEST['letter']) ? ($_REQUEST['letter']) : "");
  $origin   = mysql_real_escape_string(isset($_REQUEST['origin']) ? ($_REQUEST['origin']) : "");


  //echo "$for_id";
  $items = array();
/*if(is_array($for_id)){
  foreach ($for_id as $selectedOption) {
    echo "$selectedOption";
    $items[] = $selectedOption;
  }
}*/
if(is_array($for_id)){
$ids = join("','",$for_id);
$query = mysql_query(" SELECT * from users where id IN ('$ids') ");
  while($ros = mysql_fetch_array($query))
  {

    $name = $ros['name'];
    $items[] = $name;
  }
}

$maintainer = implode(",", $items);

//echo "$maintainer";



  

  function upload_failed($message) {
    global $userfile;

    // Trash it.
    @unlink($userfile);

    echo "<h2 align=\"center\">Error: $message</h2>\n";
    print_footer();
    exit;
  }

  function add_standard_access($document_id,$level = "R") {
    global $user;
    // Owner access.
    @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($user->id,$document_id,'W')");

    // Others - set what was specified.
    switch($level) {
      case "X":
        break;
      case "W":
        $res = @mysql_query("SELECT id FROM users");
        while($row = @mysql_fetch_array($res))
          @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($row[id],$document_id,'$level')");
       break;
      default:
        break;
    }
    return;
  }

  print_header("Uploading Document");

  if(!isset($_FILES['userfile']))
    upload_failed("Document was not found");

  if(!file_exists($_FILES['userfile']['tmp_name']))
    upload_failed("Document was not uploaded");

  $fp = fopen($_FILES['userfile']['tmp_name'], "r");
  if(!$fp)
    upload_failed("Cannot open uploaded document File");
  

  //$content = fread($fp, $_FILES['userfile']['size']);
  //echo "$content";
  fclose($fp);
  //unlink($_FILES['userfile']['tmp_name']);

  $fileTmp = $_FILES['userfile']['tmp_name'];
  //echo "$fileTmp";

  @mysql_query("INSERT INTO documents(name,type,size,author,maintainer,revision,created,origin,dateletter,letter) VALUES('".mysql_real_escape_string($_FILES['userfile']['name'])."','".mysql_real_escape_string($_FILES['userfile']['type'])."',".intval($_FILES['userfile']['size']).",$user->id,'".mysql_real_escape_string($maintainer)."',1,NOW(),'$origin','$date','$letter')") or die ('Error updating database: '.mysql_error());

  $fileError = $_FILES['userfile']['error'];
  $fileName = $_FILES['userfile']['name'];

  switch(mysql_error()) {
    case 0:
      $doc_id = mysql_insert_id();
      if(is_array($for_id)){
      foreach ($for_id as $selectedOption) {

      @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($selectedOption,$doc_id,'W')") or die ('Error updating database: '.mysql_error()); }
      }
      $destination = "./Upload/".time().','.$fileName;
      move_uploaded_file($fileTmp, $destination);
      @mysql_query("INSERT INTO documents_content(id,content) VALUES($doc_id,'". $destination ."')") or die ('Error updating database: '.mysql_error());
        //if(mysql_errno()) {
           // $error = mysql_error();
            //mysql_query("DELETE FROM documents WHERE id=$doc_id") or die ('Error updating database: '.mysql_error());
            //upload_failed( "Index ($doc_id) succeeded, but content failed<br>Error: $error" );
        //} else {
      if($info) {
        @mysql_query("INSERT INTO documents_info(id,info) VALUES($doc_id,'". $info ."')");
          if(mysql_errno() ) {
            $error = mysql_error();
            @mysql_query("DELETE FROM documents WHERE id=$doc_id");
            //@mysql_query("DELETE FROM documents_content WHERE id=$doc_id");
            upload_failed( "Index ($doc_id) and content succeeded, but info failed<br>Error: $error" );
            }
          }
      add_standard_access($doc_id,$level);
      $keywords = ereg_replace(",", " ", $keywords);
      $keywords = ereg_replace("  ", " ", $keywords);
      $keywords = explode(" ", $keywords);
      $keyword = current($keywords);
      echo "<h2 align=\"center\">Uploaded ". htmlspecialchars(stripslashes($_FILES['userfile']['name'])) ." ({$_FILES['userfile']['size']} bytes) as Document ID $doc_id</h2>\n";
      echo "<h3 align=\"center\">Using keywords: \n";
      do {
          @mysql_query("INSERT INTO documents_keywords(id,keyword) VALUES($doc_id,'". mysql_real_escape_string($keyword) ."')");
          if(mysql_errno())
            echo "<br>Error, $keyword not saved\n";
          else
            echo "<br>$keyword\n";
        } while ($keyword = next($keywords));
      echo "</h3>\n";
      header("refresh:3; url=new");
      break;
    default:
      upload_failed( mysql_error() );
      break;
  }

  print_footer()

?>
