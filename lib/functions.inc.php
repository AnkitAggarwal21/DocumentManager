<!DOCTYPE html>
<html lang="en">
<head>
  <title>Upload Document</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/i18n/defaults-*.min.js"></script>
  

</head>
<body>

<?php
  function print_login_header($title) {

    global $user, $cfg;
    global $debug_time_start;

    $debug_time_start = microtime();

    echo " 
<nav class=\"navbar navbar-inverse\">
  <div class=\"container-fluid\">

    <div class=\"navbar-header\">
    <a href=\"main\" class=\"navbar-brand\">DMRC Document Manager</a><img src=\"pix/dmrc.png\" height=\"35\" width=\"40\" vspace=\"7.5\" style=\"padding:0px 5px 0px 0px;\">
      
      <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#myNavbar\">
        <span class=\"icon-bar\"></span>
        <span class=\"icon-bar\"></span>
        <span class=\"icon-bar\"></span>                        
      </button>
    </div>
   <div class=\"collapse navbar-collapse\" id=\"myNavbar\">

    
    
     
    
    <ul class=\"nav navbar-nav navbar-right\">
      <li><a href=\"index\" target=\"_top\"><span class=\"glyphicon glyphicon-user\"></span> Log In to continue </a></li>
    </ul>
    </div>
  </div>
</nav>"; 
  }

  function print_header($title) {

    global $user, $cfg;
    global $debug_time_start;

    $debug_time_start = microtime();

    echo " 
<nav class=\"navbar navbar-inverse\">
  <div class=\"container-fluid\">

    <div class=\"navbar-header\">
    <a href=\"main\" class=\"navbar-brand\">DMRC<a href=\"#\"><img src=\"pix/dmrc.png\" height=\"35\" width=\"40\" vspace=\"7.5\" style=\"padding:0px 5px 0px 0px;\"></a></a>
      
      <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#myNavbar\">
        <span class=\"icon-bar\"></span>
        <span class=\"icon-bar\"></span>
        <span class=\"icon-bar\"></span>                        
      </button>
    </div>
    <div class=\"collapse navbar-collapse\" id=\"myNavbar\">

    
    <ul class=\"nav navbar-nav\">
      <li><a href=\"list\">Document List</a></li>
      <li><a href=\"message\">Messages</a></li>
         
      <li><a href=\"contacts\">Contacts</a></li>";

      if($user->god)
      {
        if($user->id == 1)
        {
          echo "<li><a href=\"users\">Users</a></li>";
          echo "<li><a href=\"Logs\">Logs</a></li>";
        }
        echo "<li><a href=\"up\">Update</a></li>"   ;
        
        echo "<a href=\"new\" class=\"btn btn-primary navbar-btn\" role=\"button\">Upload</a>";
        
      }
      echo "
    </ul>
     
     
    
    
    <ul class=\"nav navbar-nav navbar-right\">
      <li class=\"dropdown\"><a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\" target=\"_top\"><span class=\"glyphicon glyphicon-user\"></span> Logged in as: $user->name
      <span class=\"caret\"></span>
      </a>
        <ul class=\"dropdown-menu\">
          <li><a href=\"pass\">Change Password</a></li>
        </ul>

      </li>
      <li><a href=\"logout\" target=\"_top\"><span class=\"glyphicon glyphicon-log-out\"></span> Log Out</a></li>
    </ul>
    </div>
    </div>
  </div>
</nav>"; 

  }

  function print_login_footer() {
    
    /*global $cfg;

    if($cfg['debug']) {
        global $debug_time_start;
        $debug_time_end = microtime();

        $start = explode(" ", $debug_time_start);
        $end = explode(" ", $debug_time_end);

        $elapsed = ($end[0] - $start[0]) + ($end[1] - $start[1]);
    }

    
    
    if( $cfg['debug'] )
      echo "<br>debug: Generated in ". number_format( $elapsed, 3, ".", ",") ." seconds.\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</body>\n";
    echo "</html>\n";
  }*/
    echo "<table border=\"0\" width=\"100%\">\n";
    echo "<tr>\n";
    echo "<hr size=\"2\"></hr>\n";
    echo "<p class=\"descb\" align=\"right\" style=\"margin-right:20px;\">Designed and Developed by: Ankit Aggarwal & Kartik Gill\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</body>\n";
    echo "</html>\n";
  }

  function print_footer() {
    global $cfg;

    
    echo "<table border=\"0\" width=\"100%\">\n";
    //echo "<tr><td>\n</td></tr>\n";
    echo "<tr>\n";
    echo "<hr size=\"2\"></hr>\n";
    //echo "<td align=\"right\">\n";
    echo "<form action=\"search\" method=\"post\">\n";
    
     echo "<td><input type=\"submit\" value=\"Find!\" class=\"btn btn-success\"></td>\n";
    echo "<td><input type=\"text\" size=\"140\" name=\"query\" class=\"form-control\"></td>\n";
   
    echo "</form>\n";
    
    //echo "\n";
    echo "</tr>\n";
    echo "</table>\n";
    /*echo "</body>\n";
    echo "</html>\n";*/
  }

  function neutral_table_start($align, $outer = 0, $inner = 0) {
    echo "<div align=\"$align\">\n";
    echo "<table border=\"$outer\" cellpadding=\"0\" cellspacing=\"0\">\n";
    echo "<tr>\n<td>\n";
    echo "<table border=\"$inner\" cellpadding=\"4\" cellspacing=\"0\">\n";
  }

  function table_start($align, $outer = 0, $inner = 0) {
    global $cfg;
    echo "<div align=\"$align\">\n";
    echo "<table border=\"$outer\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$cfg[table_bg]\">\n";
    echo "<tr>\n<td>\n";
    echo "<table border=\"$inner\" cellpadding=\"4\" cellspacing=\"0\">\n";
  }

  function table_end() {
    echo "</td>\n";
    echo "</tr>\n";
    echo "</tbody></table>\n";
    //echo "</div>\n";
  }

  function get_extension($filename) {
    $tmp = explode(".", $filename);
    if( sizeof($tmp) > 1)
        return strtolower($tmp[sizeof($tmp)-1]);
    else
        return "none";
  }

  function is_god($user_id) {
    $res = @mysql_query("SELECT user FROM gods WHERE user=$user_id");
    if(mysql_errno())
      return false;
    if(mysql_num_rows($res) == 1)
      return true;
    return false;
  }

  function may_read($user_id, $document_id) {
    if( is_god($user_id) )
      return true;
    $res = @mysql_query("SELECT level FROM ACL WHERE user_id=$user_id AND document_id=$document_id");
    if(mysql_errno())
      return false;
    if(mysql_num_rows($res) != 1)
      return false;
    return true;
  }

  function may_write($user_id, $document_id) {
    if( is_god($user_id) )
      return true;
    $res = @mysql_query("SELECT level FROM ACL WHERE user_id=$user_id AND document_id=$document_id");
    if(mysql_errno())
      return false;
    if(mysql_num_rows($res) != 1)
      return false;
    $row = @mysql_fetch_array($res);
    if($row[level] != "R")
      return true;
    return false;
  } 

  function may_god($user_id, $document_id) {
    if( is_god($user_id) )
      return true;
    $res = @mysql_query("SELECT level FROM ACL WHERE user_id=$user_id AND document_id=$document_id");   
    if(mysql_errno())
      return false;
    if(mysql_num_rows($res) != 1)
      return false;
    $row = @mysql_fetch_array($res);
    if($row['level'] == "G")
      return true;
    return false;
  }

  function access_string($level) {
    switch($level) {
      case "R":
        return "Read-Only";
        break;
      case "W":
        return "Read/Write";
        break;
      case "G":
        return "God Mode";
        break;
      case "X":
        return "No Access";
        break;
      default:
        return "Unknown";
        break;
    }
  }

  function get_access($user_id,$document_id) {
    if( may_god($user_id,$document_id))
      return "G";
    if( may_write($user_id,$document_id))
      return "W";
    if( may_read($user_id,$document_id))
      return "R";
    return "X";
  }
?>


</body>
</html>