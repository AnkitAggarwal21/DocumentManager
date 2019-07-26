<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  function upload_failed($message) {
    // Trash it.
    @unlink($_FILES['userfile']['tmp_name']);

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
      default:
        $res = @mysql_query("SELECT id FROM users");
        while($row = @mysql_fetch_array($res))
          @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($row[id],$document_id,'$level')");
       break;
    }
    return;
  }
    
  /*
   * Basic input validation.
   */
  $order = mysql_real_escape_string(rawurldecode(isset($_REQUEST['order']) ? ($_REQUEST['order']) : ""));
  $page = intval(isset($_REQUEST['page']) ? ($_REQUEST['page']) : "");
  $type = mysql_real_escape_string(isset($_REQUEST['type']) ? ($_REQUEST['type']) : "");
  $query = isset($_REQUEST['query']) ? ($_REQUEST['query']) : "";

  $doc_id = intval($_REQUEST['doc_id']);
  $info   = mysql_real_escape_string(isset($_REQUEST['info']) ? ($_REQUEST['info']) : "");
  $keywords   = mysql_real_escape_string(isset($_REQUEST['keywords']) ? ($_REQUEST['keywords']) : "");
  $for_id = isset($_POST['For']) ? ($_POST['For']) : "";

  $user = new user($_SESSION['login']);
  $document = new document($doc_id);

  $items = array();
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


  print_header("Updating Document #$document->id");

  if((empty($_FILES['userfile']['tmp_name'])) && (!isset($_POST['For'])))
    upload_failed("Cannot Update Anything in Document");

  //if($_FILES['userfile']['name'] != $document->name )
    //upload_failed("Document should be called $document->name");

if((empty($_FILES['userfile']['tmp_name'])) && (isset($_POST['For'])))
  {
    @mysql_query("DELETE FROM ACL WHERE document_id=$document->id");
    echo "<h2 align=\"center\">Updated ". htmlspecialchars(mysql_real_escape_string($_FILES['userfile']['name'])) ." ({$_FILES['userfile']['size']} bytes) to revision ". ($document->revision + 1) ."</h2>\n";

    @mysql_query("UPDATE documents SET revision = revision+1 WHERE id=$document->id");
    @mysql_query("UPDATE documents SET modified = NOW() WHERE id=$document->id");
    @mysql_query("UPDATE documents SET author = '$user->id', maintainer = '$maintainer' WHERE id=$document->id") or die ('Error updating database: '.mysql_error());
    @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($user->id,$doc_id,'W')");
        if(is_array($for_id)){
        foreach ($for_id as $selectedOption) {
        @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($selectedOption,$doc_id,'W')") or die ('Error updating database: '.mysql_error()); }
        }
        // New info?
        if($info != NULL && $info != "") {
            @mysql_query("REPLACE INTO documents_info(id,info) VALUES($document->id,'". $info ."')");
            if(mysql_errno()) {
                echo "<h3 align=\"center\">New info <u>not</u> saved<br>". mysql_error() ."</h3>\n";
            } else {
                echo "<h3 align=\"center\">New info saved</h3>\n";
            }
        }

        // New keywords?
        if($keywords != NULL && $keywords != "") {
            // Delete the old keywords.
            @mysql_query("DELETE FROM documents_keywords WHERE id=$document->id");
            $keywords = ereg_replace(",", " ", $keywords);
            $keywords = ereg_replace("  ", " ", $keywords);
            $keywords = explode(" ", $keywords);
            $keyword = current($keywords);
            echo "<h3 align=\"center\">Using keywords: \n";
            do {
                @mysql_query("INSERT INTO documents_keywords(id,keyword) VALUES($document->id,'". mysql_real_escape_string($keyword) ."')");
                if(mysql_errno())
                    echo "<br>Error, $keyword not saved\n";
                else
                    echo "<br>$keyword\n";
            } while ($keyword = next($keywords));
            echo "</h3>\n";

        }
        if($type=='search')
        {
          header("refresh:2; url=$type?query=$query");
        }
        else if(empty($type))
        {
          header("refresh:2; url=list");
        }
        else
        {
        header("refresh:2; url=$type?page=$page&order=$order");
        }
  }

  else{
    if($_FILES['userfile']['name'] != $document->name )
      upload_failed("Document should be called $document->name");
    if(!file_exists($_FILES['userfile']['tmp_name']))
    upload_failed("Document was not uploaded");

  $fp = fopen($_FILES['userfile']['tmp_name'], "r");
  if(!$fp)
    upload_failed("Cannot open uploaded document");
  //$content = fread($fp, $_FILES['userfile']['size']);
  fclose($fp);
  //unlink($_FILES['userfile']['tmp_name']);
  $query1 = @mysql_fetch_array(mysql_query("SELECT * FROM documents_content where id = $document->id"));
  $content1 = $query1['content'];
  @unlink($content1);

  $fileName = $_FILES['userfile']['name'];
  $fileTmp = $_FILES['userfile']['tmp_name'];
  $destination = "./Upload/".time().','.$fileName; 
  move_uploaded_file($fileTmp, $destination);
  @mysql_query("REPLACE INTO documents_content(id,content) VALUES($document->id,'". $destination ."')") or die ('Error updating database: '.mysql_error());
  @mysql_query("DELETE FROM ACL WHERE document_id=$document->id");
  switch( mysql_errno() ) {

    // Updated OK.
    case 0:
        echo "<h2 align=\"center\">Updated ". htmlspecialchars(mysql_real_escape_string($_FILES['userfile']['name'])) ." ({$_FILES['userfile']['size']} bytes) to revision ". ($document->revision + 1) ."</h2>\n";

        @mysql_query("UPDATE documents SET revision = revision+1 WHERE id=$document->id");
        @mysql_query("UPDATE documents SET modified = NOW() WHERE id=$document->id");
        @mysql_query("UPDATE documents SET author = '$user->id', maintainer = '$maintainer' WHERE id=$document->id") or die ('Error updating database: '.mysql_error());

        @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($user->id,$doc_id,'W')");
        if(is_array($for_id)){
        foreach ($for_id as $selectedOption) {
        @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($selectedOption,$doc_id,'W')") or die ('Error updating database: '.mysql_error()); }
        }
        // New info?
        if($info != NULL && $info != "") {
            @mysql_query("REPLACE INTO documents_info(id,info) VALUES($document->id,'". $info ."')");
            if(mysql_errno()) {
                echo "<h3 align=\"center\">New info <u>not</u> saved<br>". mysql_error() ."</h3>\n";
            } else {
                echo "<h3 align=\"center\">New info saved</h3>\n";
            }
        }

        // New keywords?
        if($keywords != NULL && $keywords != "") {
            // Delete the old keywords.
            @mysql_query("DELETE FROM documents_keywords WHERE id=$document->id");
            $keywords = ereg_replace(",", " ", $keywords);
            $keywords = ereg_replace("  ", " ", $keywords);
            $keywords = explode(" ", $keywords);
            $keyword = current($keywords);
            echo "<h3 align=\"center\">Using keywords: \n";
            do {
                @mysql_query("INSERT INTO documents_keywords(id,keyword) VALUES($document->id,'". mysql_real_escape_string($keyword) ."')");
                if(mysql_errno())
                    echo "<br>Error, $keyword not saved\n";
                else
                    echo "<br>$keyword\n";
            } while ($keyword = next($keywords));
            echo "</h3>\n";

        }
        if($type=='search')
        {
          header("refresh:2; url=$type?query=$query");
        }
        else
        {
        header("refresh:2; url=$type?page=$page&order=$order");
        }
        break;


    default:
        upload_failed( "Could not save updated content<br>Error: ". mysql_error() );
        break;
  }
}

  print_footer()

?>
