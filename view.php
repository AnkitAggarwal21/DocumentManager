<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  function hilite($text) {
    global $query;
    $uquery = strtoupper($query);
    if($query)
        return ereg_replace("$query", "<b>$query</b>", ereg_replace("$uquery", "<b>$uquery</b>", $text));
    else
        return $text;
  }

  /*
   * Basic input validation.
   */
  $doc_id = intval($_REQUEST['doc_id']);

  $user = new user($_SESSION['login']);
  $document = new document($doc_id);
  $document->get_access($user->id);
  $author = $document->author;
  $maintainer = $document->maintainer;

  print_header("Response of Document");

  //neutral_table_start("center", 1, 0);

  echo "
  <div class=\"container\">
    <h2>Responses for $document->name</h2>
    <div class=\"list-group\">


      
  ";
  /*
  echo "<tr>\n";
    echo "<td align=\"center\">\n";
    echo "<h2 align=\"center\">Responses for $document->name</h2>\n";
    echo "</td>\n";
  echo "</tr>\n";*/
  $query = mysql_query(" SELECT * from actions_content where docid = $doc_id");
  while($ros = mysql_fetch_array($query))
      {
        $id = $ros['id'];
        $comment = $ros['comments'];
        $content = $ros['content'];
        $name = mysql_fetch_array(mysql_query("SELECT * FROM users where id = $id"));
        $username = $name['name'];

      echo "
      <li class=\"list-group-item\">
        <h4 class=\"list-group-item-heading\">User name: $username</h4>
        <p class=\"list-group-item-text\">Response:
        
      

      ";  
      
      /*
      neutral_table_start("left", 0, 1);
      echo "<td >User name:</td>\n";
      echo "<td colspan=\"2\" >$username</td>\n";
      echo "</tr>\n";
      echo "<tr>\n";
      echo "<td>Response:</td>\n";*/


      if(!empty($comment))
      {
      echo "$comment</p>\n";
      }
      else{
        echo "</p>\n";
      }
      $items1 = array();
      $myArray = explode(',', $content);
      
      if(!empty($content))
      {
        //echo "<td width=\"150\">";
        if(count($myArray)>1)
        {
          
         echo "<form action=\"downloadzip\" method=\"post\"><input type=\"hidden\" name=\"content\" value=\"$content\"><input type=\"hidden\" name=\"id\" value=\"$id\"><input type=\"submit\" value=\"Download ZIP\"></form>\n"; 
         }
        foreach($myArray as $keys => $value)
        {
          $ids = $keys + 1;
      echo "<form action=\"download1\" method=\"post\"><input type=\"hidden\" name=\"path\" value=\"$value\"><input type=\"hidden\" name=\"id\" value=\"$id\"><input type=\"submit\" value=\"Download File Attached $ids\"></form>\n";
        }
      echo "</p></li>";
      }
      else{
        echo "</p></li>\n";
      }

      //echo "</tr>\n";
      //table_end();
      }

  //table_end();

  print_footer()

?>
