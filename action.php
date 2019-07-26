<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  /*
   * Basic input validation.
   */
  $doc_id = intval(isset($_REQUEST['doc_id']) ? ($_REQUEST['doc_id']) : "");
  $comment   = (isset($_REQUEST['comment']) ? ($_REQUEST['comment']) : "");
  $level   = mysql_real_escape_string(isset($_REQUEST['level']) ? ($_REQUEST['level']) : "");
 
  $user = new user($_SESSION['login']);

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
      default:
        $res = @mysql_query("SELECT id FROM users");
        while($row = @mysql_fetch_array($res))
          @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($row[id],$document_id,'$level')");
       break;
    }
    return;
  }


  print_header("Submitting Response");

//print_r($_FILES);

  $files = $_FILES['userfile'];
 
  if(empty($_FILES['userfile']['tmp_name'][0]) && empty($_FILES['userfile']['name'][1]) && ($comment==""))
  {
    upload_failed("No Response Recorded");
  }
  else if(empty($_FILES['userfile']['tmp_name'][0]) && empty($_FILES['userfile']['name'][1]))
    {
      $query = mysql_query("SELECT * from actions_content");
      while($ros = mysql_fetch_array($query))
      {
        $id = $ros['id'];
        $docid = $ros['docid'];
        if($user->id == $id && $doc_id == $docid)
        {
          @mysql_query("REPLACE INTO actions_content(id,docid,content,comments) VALUES($user->id,$doc_id,null,'". $comment ."')") or die ('Error updating database: '.mysql_error());
          echo "<h2 align=\"center\">Response Submitted</h2>\n";
          exit;

        }
        }
        
          @mysql_query("INSERT INTO actions_content(id,docid,content,comments) VALUES($user->id,$doc_id,null,'". $comment ."')") or die ('Error updating database: '.mysql_error());
          echo "<h2 align=\"center\">Response Submitted</h2>\n";
        
      }
  else
  {
    foreach ($_FILES['userfile']['error'] as $pos => $error) 
    {
    if($error !== 0)
    {
       upload_failed("Document was not uploaded");
    }
    }

    $items = array();
    $items1 = array();

   
    foreach ($_FILES['userfile']['name'] as $position => $fileName) 
    {
      # code...
    
     $fileTmp = $_FILES['userfile']['tmp_name'][$position];
     $name1 = basename($fileName);
     
     $destination = "./Actions/".time().$position.'|'.$fileName;move_uploaded_file($fileTmp, $destination);
     $items[] = $destination; 
     }
  
     /*if(count($_FILES['userfile']['name'])>1)
     {
     $zip = new ZipArchive;
     $zip_name = time().'.zip';
     if($zip->open('./Actions/'.$zip_name,ZipArchive::CREATE) === TRUE)
     {
      foreach ($items1 as $file) {
        $zip->addFile('./Actions/'.$file,$file);
      }
      $zip->close();
     }
     }*/

     $destinations = implode(",", $items);
     


     $query = mysql_query(" SELECT * from actions_content ");
     $flag = 0;
      while($ros = mysql_fetch_array($query))
      {
        $id = $ros['id'];
        $docid = $ros['docid'];
        $content = $ros['content'];
        if($user->id == $id && $doc_id == $docid)
        {  
          $myArray = explode(",", $content);
          foreach ($myArray as $key)
           {
             @unlink($key);
           }
  
          @mysql_query("REPLACE INTO actions_content(id,docid,content,comments) VALUES($user->id,$doc_id,'". $destinations ."','". $comment ."')") or die ('Error updating database: '.mysql_error());
          echo "<h2 align=\"center\">Response Submitted</h2>\n";
          $flag = 1;
          exit;
        }
      }
       if($flag == 0){
       @mysql_query("INSERT INTO actions_content(id,docid,content,comments) VALUES($user->id,$doc_id,'". $destinations ."','". $comment ."')") or die ('Error updating database: '.mysql_error());
          echo "<h2 align=\"center\">Response Submitted</h2>\n";
        } 

      }
      

 

 
  //unlink($_FILES['userfile']['tmp_name']);


 // @mysql_query("INSERT INTO documents(name,type,size,author,maintainer,revision,created) VALUES('".mysql_real_escape_string($_FILES['userfile']['name'])."','".mysql_real_escape_string($_FILES['userfile']['type'])."',".intval($_FILES['userfile']['size']).",$user->id,$for_id,1,NOW())") or die ('Error updating database: '.mysql_error());


  /*switch(mysql_error()) {
    case 0:
      $doc_id = mysql_insert_id();
      @mysql_query("INSERT INTO ACL(user_id,document_id,level) VALUES($for_id,$doc_id,'W')") or die ('Error updating database: '.mysql_error());
      $destination = "./Upload/".$fileName;
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
      break;
    default:
      upload_failed( mysql_error() );
      break;
  } */

  print_footer()

?>
