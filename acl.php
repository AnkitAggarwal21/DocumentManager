<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  /*
   * Basic input validation.
   */
  $user_id = intval(isset($_POST['user_id']) ? ($_POST['user_id']) : "");
  $doc_id  = intval(isset($_POST['doc_id']) ? ($_POST['doc_id']) : "");
  $level   = mysql_real_escape_string(isset($_POST['level']) ? ($_POST['level']) : "");


  if( !may_god($user->id, $document->id) ) {
     print_header("Permission Denied");
     echo "<h2 align=\"center\">Permission denied</h2>\n";
     print_footer();
     exit;
  }

  print_header("Edit Access Control List");
  echo "<h2 align=\"center\">Edit ACL for $document->name</h2>\n";

  if(isset($_POST['button'])) {
    if( $level == "X" ) {
      @mysql_query("DELETE FROM ACL WHERE document_id=$document->id AND user_id=$user_id");
    } else {
      @mysql_query("INSERT INTO ACL(document_id,user_id,level) VALUES($document->id,$user_id,'$level')");
      if(mysql_errno() == 1062)
        @mysql_query("UPDATE  ACL SET level='$level' WHERE user_id=$user_id AND document_id=$document->id");
    }
    if(mysql_errno()) {
      echo "<h3 align=\"center\">Update failed<br>". mysql_error() ."</h3>\n";
    } else {
      echo "<h3 align=\"center\">Update succeeded; new level active</h3>\n";
    }
  }

  echo "<form action=\"acl\" method=\"post\">\n";
  echo "<input type=\"hidden\" name=\"doc_id\" value=\"$document->id\">\n";
  neutral_table_start("center", 1, 0);
  
  echo "<tr>\n";
    echo "<td>User:</td>\n";
    echo "<td><select name=\"user_id\">\n";
    $res = @mysql_query("SELECT id,name FROM users ORDER BY name ASC");
    while($row = @mysql_fetch_array($res))
      printf("<option value=\"%d\"%s>%s (%s)</option>\n"
          ,$row[id]
          ,($row[id] == $user_id) ? "selected" : "" 
          ,$row[name]
          ,access_string(get_access($row[id],$document->id))
    );
    echo "</select></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td valign=\"top\">New level:</td>\n";
    echo "<td>\n";
    echo "<img src=\"pix/X.gif\" height=\"15\" width=\"15\" alt=\"[ ". access_string("X") ."]\"><input type=\"radio\" name=\"level\" value=\"X\">No Access<br>\n";
    echo "<img src=\"pix/R.gif\" height=\"15\" width=\"15\" alt=\"[ ". access_string("R") ."]\"><input type=\"radio\" name=\"level\" value=\"R\">Read-Only<br>\n";
    echo "<img src=\"pix/W.gif\" height=\"15\" width=\"15\" alt=\"[ ". access_string("W") ."]\"><input type=\"radio\" name=\"level\" value=\"W\">Read/Write<br>\n";
    echo "<img src=\"pix/G.gif\" height=\"15\" width=\"15\" alt=\"[ ". access_string("G") ."]\"><input type=\"radio\" name=\"level\" value=\"G\">God Mode<br>\n";
    echo "</td>\n";
  echo "</tr>\n";

  echo "<tr>\n";
    echo "<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"button\" value=\"Update Access Level\"></td>\n";
  echo "</tr>\n";

  table_end();
  echo "</form>\n";


  print_footer()

?>
