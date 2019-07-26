<!DOCTYPE html>
<html lang="en">
<head>
  <title>Message Board</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   

  
</head>
<body>


<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  $user = new user($_SESSION['login']);



  function list_messages() {
    $result = @mysql_query("SELECT c.id AS id,u.name AS user,u.email AS email,c.subject AS subject,c.ref_id AS ref,DATE_FORMAT(c.date, \"%W, %e %M %Y\") AS date FROM chat AS c LEFT JOIN users AS u ON c.user=u.id ORDER BY id DESC LIMIT 50" );
    echo "<div class=\"container\">";
    



    if( ($num = @mysql_num_rows( $result )) == 1) {
        echo "<h2>Message Board: $num message</h2>\n";
    } else {
        echo "<h2>Message Board: $num messages</h2>\n";
    }

    echo "<a href=\"message?action=post\" class=\"btn btn-danger\">Post New Message</a>";


    echo "
    <div class=\"list-group\">
    ";
    

    
    

    while( $row = @mysql_fetch_array($result) ) {
        if($row['subject'])
            if($row['ref'])
                echo "<li class=\"list-group-item\">
                        <h4 class=\"list-group-item-heading\"><a href=\"message?action=read&mid=$row[id]\">Re: ". stripslashes($row['subject']) ."</a>
                        </h4>
                        <p class=\"list-group-item-text\"> by $row[user] on $row[date]</p>
                      </li>";
            else
                echo "<li class=\"list-group-item\">
                        <h4 class=\"list-group-item-heading\"><a href=\"message?action=read&mid=$row[id]\">". stripslashes($row['subject']) ."</a>
                        </h4>
                        <p class=\"list-group-item-text\"> by $row[user] on $row[date]</p>
                      </li>";
        else
            if($row['ref'])
                echo "<li class=\"list-group-item\">
                        <h4 class=\"list-group-item-heading\">
                          <a href=\"message?action=read&mid=$row[id]\">Re: No Subject</a>
                        </h4>
                        <p class=\"list-group-item-text\"> by $row[user] on $row[date]
                      </li>";
            else
                echo "<li class=\"list-group-item\">
                        <h4 class=\"list-group-item-heading\"><a href=\"message?action=read&mid=$row[id]\">No Subject</a>
                        </h4>
                        <p class=\"list-group-item-text\"> by $row[user] on $row[date]</p>
                      </li>";
    }
    return;
  }
  function show_message( $mid ) {

    $result = @mysql_query("SELECT c.id AS id,c.ref_id AS ref,u.name AS user,u.email AS email,c.subject AS subject,c.content AS content,DATE_FORMAT(c.date, \"%W, %e %M %Y\") AS date FROM chat AS c LEFT JOIN users AS u ON c.user=u.id WHERE c.id=$mid");
    $row = @mysql_fetch_array($result);

    echo "<div class=\"container\">";

    echo "<h2>Message by $row[user]<small> at $row[date]</small></h2>\n";

   

    
    echo "<br><h4><u>Subject: <b>". stripslashes( $row['subject'] ) ."</b></u>\n";

    if($row['ref']) {
        $result = @mysql_query("SELECT c.id AS id,u.name AS user,c.subject AS subject,DATE_FORMAT(c.date, \"%W, %e %M %Y\") AS date FROM chat AS c LEFT JOIN users AS u ON c.user=u.id WHERE c.id=$row[ref]");
        $ref = @mysql_fetch_array( $result );
        echo "<small> in reply to: <a href=\"message?action=read&mid=$ref[id]\">". stripslashes($ref['subject']) ."</a> by $ref[user] on $ref[date]\n</small></h4>";
    }

    echo "<blockquote>". nl2br( htmlentities( stripslashes($row['content']) )) ."</blockquote>\n";
    echo "<p><a href=\"message?action=reply&reply=$row[id]\" class=\"btn btn-primary\">Reply</a> <b></b> <a href=\"message?action=post\" class=\"btn btn-danger\">Post New</a>\n";

    $res = @mysql_query("SELECT id FROM chat WHERE id<$row[id]");
    if( mysql_num_rows($res) )
        echo " <b></b> <a href=\"message?action=read&mid=". ($row['id']-1) ."\" class=\"btn btn-info\">Previous</a>\n";

    $res = @mysql_query("SELECT id FROM chat WHERE id>$row[id]");
    if( mysql_num_rows($res) )
        echo " <b>|</b> <a href=\"message?action=read&mid=". ($row['id']+1) ."\" class=\"btn btn-info\">Next</a>\n";

    echo " <b></b> <a href=\"message\" class=\"btn btn-success\">Messages</a>\n";

    
    return;
  }

  function new_message( $mid ) {
    echo "<div class=\"container\">";
    echo "<div class=\"list-group\">";


    if( $mid > 0)
        echo "<h2>Reply to message #$mid</h2>\n";
    else
        echo "<h2>Enter a message</h2>\n";

    echo "<form action=\"message?action=save\" method=\"post\" id=\"messageform\">\n";

    echo "<input type=\"hidden\" value=\"$mid\" name=\"ref\">\n";
    
    if($mid) {
       $res = @mysql_query("SELECT subject FROM chat WHERE id=$mid");
       $row = @mysql_fetch_array($res);
       
       echo "<li class=\"list-group-item\">
       <h4 class=\"list-group-item-heading\">Subject: <b>Re: ". stripslashes($row['subject']) ."</b></h4>\n";
       echo "<input type=\"hidden\" name=\"subject\" value=\"". stripslashes($row['subject'])."\"></li>\n";
    } else {
        echo "<li class=\"list-group-item\">
       <h4 class=\"list-group-item-heading\">Subject </h4><input type=\"text\" size=\"32\" maxsize=\"128\" name=\"subject\"></li>\n";
    }
    echo "<li class=\"list-group-item\">
            <h4 class=\"list-group-item-heading\">Message</h4>
            <textarea class=\"form-control\" name=\"content\" rows=\"3\"></textarea>
          </li>";

          


    echo "<li class=\"list-group-item list-group-item-info\" align=\"center\"><a href=\"#\" id=\"messagebutton\" value=\"Post Message\">Post Message</a></li>";

    echo "</form></div></div>\n";

    return;
  }

  function save_message( $ref, $subject, $content ) {
    global $user;

    if($ref)
        $query = "INSERT INTO chat(ref_id,user,subject,content,date) values($ref,$user->id,'". addslashes($subject) ."','". addslashes($content) ."',NOW())";
    else
        $query = "INSERT INTO chat(user,subject,content,date) values($user->id,'". addslashes($subject) ."','". addslashes($content) ."',NOW())";
    $result = @mysql_query( $query );

    echo "<div class=\"container\">";  

    if($result != -1) {
        echo "<h2>Error ". mysql_errno() .": ". mysql_error() ."</h2>\n";
        echo "<p>$query\n";
    } else {
        $result = @mysql_query("SELECT LAST_INSERT_ID() FROM chat LIMIT 1");
        $row = @mysql_fetch_array($result);
        echo "<h2>Your message was posted successfully</h2>\n";
        echo "<p class=\"lead\"><small>You can <a href=\"message?action=read&mid=$row[0]\">read it</a> or view all <a href=\"message\">Messages\n";
    }
    return;
  }

  global $action, $mid, $reply, $ref, $subject, $content;

  /*
   * Basic input validation.
   */
  $action  = mysql_real_escape_string(isset($_REQUEST['action']) ? ($_REQUEST['action']) : "");
  $mid     = intval(isset($_REQUEST['mid']) ? ($_REQUEST['mid']) : "");
  $reply   = mysql_real_escape_string(isset($_REQUEST['reply']) ? ($_REQUEST['reply']) : "");
  $ref     = intval(isset($_REQUEST['ref']) ? ($_REQUEST['ref']) : "");
  $subject = mysql_real_escape_string(isset($_REQUEST['subject']) ? ($_REQUEST['subject']) : "");
  $content = mysql_real_escape_string(isset($_REQUEST['content']) ? ($_REQUEST['content']) : "");


  print_header("Message Board");

  

  switch($action) {

    case "read":
      show_message( intval($mid) );
      break;

    case "post":
      new_message( 0 );
      break;

    case "reply":
      new_message( $reply );
      break;

    case"save":
      save_message( $ref, $subject, $content );
      break;

    default:
      list_messages();
      break;
  }


  print_footer();

?>

<script>
  document.getElementById("messagebutton").onclick = function() {
    document.getElementById("messageform").submit();
}
</script>

</body>
</html>