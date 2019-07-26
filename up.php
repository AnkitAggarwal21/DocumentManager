<!DOCTYPE html>
<html lang="en">
<head>
  <title>Update Document</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

 
</head>
<body>

<?php

    require('lib/config.inc.php');
    require('lib/auth.inc.php');
    require('lib/classes.inc.php');
    require('lib/functions.inc.php');

    $user = new user($_SESSION['login']);

    if(! $user->god) {
        print_header("Access Dennied!");
        exit;
    }
    $order = mysql_real_escape_string(rawurldecode(isset($_REQUEST['order']) ? ($_REQUEST['order']) : ""));
    $page = intval(isset($_REQUEST['page']) ? ($_REQUEST['page']) : "");
    $type = isset($_REQUEST['type']) ? ($_REQUEST['type']) : "";
    $query = mysql_real_escape_string(isset($_REQUEST['query']) ? ($_REQUEST['query']) : "");


    $docid = intval(isset($_REQUEST['docid']) ? ($_REQUEST['docid']) : "");
    print_header("Update a document");

    echo "<form action=\"update\" method=\"post\" enctype=\"multipart/form-data\" id=\"upform\">\n";
    echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"16777216000\"><input type=\"hidden\" name=\"query\" value=\"$query\"><input type=\"hidden\" name=\"page\" value=\"$page\"><input type=\"hidden\" name=\"order\" value=\"$order\"><input type=\"hidden\" name=\"type\" value=\"$type\">
    \n";
    echo "

    <div class=\"container\">
      <h2>Update Document</h2>
      <div class=\"list-group\">
        <li class=\"list-group-item\">
          <h4 class=\"list-group-item-heading\">Document</h4>
          <p class=\"list-group-item-text\">Select file to update</p>
          <select class=\"selectpicker\" data-live-search=\"true\" name=\"doc_id\">
    ";


      
    

    

    /*
    table_start("center", 1, 0);

    echo "<tr>\n";
      echo "<td align=\"center\" colspan=\"2\"><font color=\"$cfg[table_text]\"><h3 align=\"center\">Update a document</h3></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
      echo "<td><b><font color=\"$cfg[table_text]\">Document:</font></b></td>\n";*/
      //echo "<td><select name=\"doc_id\">\n";
      if(isset($_REQUEST['docid']))
      {
      if($user->id == 1)
        $res = @mysql_query("SELECT d.id AS id,d.name AS name,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created, d.created AS cdate,u.name AS user FROM documents AS d LEFT JOIN users AS u on d.author=u.id where d.id = $docid ORDER BY cdate DESC");
      else
        $res = @mysql_query("SELECT d.id AS id,d.name AS name,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created, d.created AS cdate, a.level AS level FROM documents AS d LEFT JOIN ACL AS a ON a.document_id=d.id WHERE d.id = $docid AND a.user_id=$user->id AND (a.level='W' OR a.level='G') ORDER BY cdate DESC");
      }
      else
      {
      if($user->id == 1)
        $res = @mysql_query("SELECT d.id AS id,d.name AS name,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created, d.created AS cdate,u.name AS user FROM documents AS d LEFT JOIN users AS u on d.author=u.id ORDER BY cdate DESC");
      else
        $res = @mysql_query("SELECT d.id AS id,d.name AS name,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created, d.created AS cdate, a.level AS level FROM documents AS d LEFT JOIN ACL AS a ON a.document_id=d.id WHERE a.user_id=$user->id AND (a.level='W' OR a.level='G') ORDER BY cdate DESC");
      }
      if(!mysql_num_rows($res)) {
        echo "<option selected>You cannot update any documents</option>\n";
      } else {
        while( $row = @mysql_fetch_array($res)) {
          if($user->id == 1)
            echo "<option value=\"$row[id]\">$row[name] &nbsp;&nbsp; [$row[user]]</option>\n";
          else
            echo "<option value=\"$row[id]\">$row[name]</option>\n";
      } // while
    }
    echo "</select></li>";
    echo "

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Choose File</h4>
      <p class=\"list-group-item-text\">Click to browse for file. Select file with the same name to update document</p>
      <input type=\"file\" name=\"userfile\">
    </li>

    ";  

    /*
    echo "<font class=\"desc\">Select a document to update</font></td>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
      echo "<td><b><font color=\"$cfg[table_text]\">File:</font></b></td>\n";
      echo "<td><input type=\"file\" name=\"userfile\"><br><font class=\"desc\">Click browse to select a file</font></td>\n";
    echo "</tr>\n";*/

     echo "

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Pertains to:</h4>
      <p class=\"list-group-item-text\">Select concerned person for document</p>
      <select class=\"selectpicker\" multiple data-live-search=\"true\" name=\"For[]\">
        <option value=\"0\">None (Just for Storing)</option>
    

    ";  
    //echo "<tr>\n";
      //echo "<td><b><font color=\"$cfg[table_text]\">Pertains To:</font></b></td>\n";
      //echo "<td><select multiple=\"multiple\" name=\"For[]\">\n";
      //echo "<option value=\"X\">None (Just for Storing)</option>\n";

      $query = mysql_query(" SELECT * from users ");
      while($ros = mysql_fetch_array($query))
      {
        $name = $ros['name'];
        $pid = $ros['id'];
        if($pid==$user->id || $pid == 1)
          {
            continue;
          }
        else{
        echo "<option value=\"$pid\">$name</option>\n";}
      }

      echo "</select></li>";

    echo "
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Keywords</h4>
      <p class=\"list-group-item-text\">Separate keywords with a comma (,) or leave blank to keep originals</p> 
      <input type=\"text\" maxsize=\"512\" name=\"keywords\" class=\"form-control\">
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Info</h4>
      <p class=\"list-group-item-text\">Enter a short description of the document or leave blank to keep original</p>
      <textarea class=\"form-control\" name=\"info\" rows=\"3\"></textarea>
      
    </li>

    
    <li class=\"list-group-item\" align=\"center\"><input type=\"submit\" class=\"btn btn-primary\" value=\"Update this document\"></li>

    </div>
    
    ";


    /*
    echo "</tr>\n";
    echo "<tr>\n";
      echo "<td><b><font color=\"$cfg[table_text]\">Keywords:</font></b></td>\n";
      echo "<td><input type=\"text\" maxsize=\"512\" name=\"keywords\"><br><font class=\"desc\">Enter keywords delimited by spaces or commas or<br>leave blank to keep originals</font></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
      echo "<td valign=\"top\"><b><font color=\"$cfg[table_text]\">Info:</font></b></td>\n";
      echo "<td><textarea name=\"info\" rows=\"4\" cols=\"28\"></textarea><br><font class=\"desc\">Enter a short comment describing this document or<br>leave blank to keep original</font></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
      echo "<td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"Update this document\"></font></td>\n";
    echo "</tr>\n";

    table_end();*/

    echo "</form>";

    print_footer();

?>


</body>
</html>
