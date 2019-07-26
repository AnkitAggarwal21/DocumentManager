<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  /*
   * Basic input validation.
   */
  $victim = mysql_real_escape_string(isset($_POST['victim']) ? ($_POST['victim']) : "");
  $pass   = mysql_real_escape_string(isset($_POST['pass']) ? ($_POST['pass']) : "");
  $name   = mysql_real_escape_string(isset($_POST['name']) ? ($_POST['name']) : "");
  $email  = mysql_real_escape_string(isset($_POST['email']) ? ($_POST['email']) : "");
  $button = mysql_real_escape_string(isset($_POST['button']) ? ($_POST['button']) : "");

  $user = new user($_SESSION['login']);

  print_header("Edit Users");

  switch($button) {

      case "Yes, add user":
          echo "<h3 align=\"center\">Action: Add user $victim: \n";
          @mysql_query("INSERT INTO users(user,pass,name,email) VALUES('$victim', SHA1('$pass'),'$name','$email')");
//PASSWORD('$pass')
          if(mysql_errno())
              echo "Error<br>". mysql_error() ."</h3>";
          else 
              echo "OK</h3>";
          break;

      case "Add User":
          echo "<h2 align=\"center\">Add user $victim?</h2>\n";
          echo "<div align=\"center\">\n";
          echo "<form action=\"users\" method=\"post\">\n";
          echo "<input type=\"hidden\" name=\"victim\" value=\"$victim\">\n";
          echo "<input type=\"hidden\" name=\"pass\" value=\"$pass\">\n";
          echo "<input type=\"hidden\" name=\"name\" value=\"$name\">\n";
          echo "<input type=\"hidden\" name=\"email\" value=\"$email\">\n";
          echo "<input type=\"submit\" class=\"btn btn-success\" name=\"button\" value=\"Yes, add user\">\n";
          echo "<input type=\"submit\" name=\"button\" class=\"btn btn-warning\" value=\"Oh, never mind\">\n";
          echo "</form>\n";
          echo "</div>\n";
          print_footer();
          exit;
          break;

      case "Delete User":
          $tmp = explode(",", $victim);
          echo "<h2 align=\"center\">Delete user $tmp[1]?</h2>\n";
          echo "<div align=\"center\">\n";
          echo "<form action=\"users\" method=\"post\">\n";
          echo "<input type=\"hidden\" name=\"victim\" value=\"$victim\">\n";
          echo "<input type=\"submit\" name=\"button\" value=\"Yes, I am sure\">\n";
          echo "<input type=\"submit\" name=\"button\" value=\"Oh, never mind\">\n";
          echo "</form>\n";
          echo "</div>\n";
          print_footer();
          exit;
          break;

      case "Yes, I am sure":
          echo "<h3 align=\"center\">Action: Delete user $victim: \n";;
          $tmp = explode(",", $victim);
          @mysql_query("DELETE FROM users WHERE id=$tmp[0]");
          if(mysql_errno())
              echo "Error<br>". mysql_error() ."</h3>";
          else
              echo "OK</h3>";
          break;

      default:
          break;


  }

  echo "<form action=\"users\" method=\"post\">\n";
  //table_start("center", 1, 0);
  echo "
  <div class=\"container\">
  <h2>Add user</h2>
  <div class=\"list-group\">
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Username</h4>
      <p class=\"list-group-item-text\">Provide a unique username</p>
      <input type=\"text\" name=\"victim\" class=\"form-control\" size=\"16\" maxlength=\"16\" style=\"margin-top:7px; padding:5px;\">
    </li>
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Password</h4>
      
      <input type=\"password\" name=\"pass\" size=\"16\" class=\"form-control\" maxlength=\"8\" style=\"margin-top:7px; padding:5px;\">
    </li>
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Name</h4>
      <p class=\"list-group-item-text\">Name of the user</p>

      <input type=\"text\" name=\"name\" size=\"16\" maxlength=\"64\" class=\"form-control\" style=\"margin-top:7px; padding:5px;\">
    </li>
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\"Email</h4>
      <p class=\"list-group-item-text\">Email address of the user</p>
      <input type=\"text\" name=\"email\" size=\"16\" maxlength=\"64\" class=\"form-control\" style=\"margin-top:7px; padding:5px;\">
    </li>
    <li class=\"list-group-item\">
      <input type=\"submit\" name=\"button\" value=\"Add User\" class=\"btn btn-primary\">
    </li>
    </div>


  ";
  /*
  echo "<tr>\n";
  echo "<td colspan=\"2\"><h3 align=\"center\"><font color=\"#000000\">Add a user</font></h3></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td><b><font color=\"#000000\">Login:</font></td>\n";
  echo "<td><input type=\"text\" name=\"victim\" size=\"16\" maxlength=\"16\"></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td><b><font color=\"#000000\">Password:</font></td>\n";
  echo "<td><input type=\"password\" name=\"pass\" size=\"16\" maxlength=\"8\"></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td><b><font color=\"#000000\">Name:</font></td>\n";
  echo "<td><input type=\"text\" name=\"name\" size=\"16\" maxlength=\"64\"></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td><b><font color=\"#000000\">Email:</font></td>\n";
  echo "<td><input type=\"text\" name=\"email\" size=\"16\" maxlength=\"64\"></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"button\" value=\"Add User\"></td>\n";
  echo "</tr>\n";
  table_end();*/
  echo "</form>\n";

  echo "<form action=\"users\" method=\"post\">\n";
  //table_start("center", 1, 0);

  echo "

  <h2>Delete a user</h2>
  <div class=\"list-group\">
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Select user</h4>
      
      <select class=\"selectpicker\" data-live-search=\"true\" name=\"victim\">
        

  ";

/*
  echo "<tr>\n";
  echo "<td colspan=\"2\"><h3 align=\"center\"><font color=\"#000000\">Delete a user</font></h3></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td><b><font color=\"#000000\">User:</font></td>\n";
  echo "<td><select name=\"victim\">\n";*/
  $res = @mysql_query("SELECT id,user,name FROM users ORDER BY name ASC");
  while( $row = @mysql_fetch_array($res) ) {
    $tmp = new user($row[user]);
    if(!$tmp->god)
        printf("<option value=\"%d,%s\">%s</option>\n", $tmp->id, $tmp->name, $tmp->name );
  }
  echo "</select></li>";
  echo "
  <li class=\"list-group-item\" align=\"center\"><input type=\"submit\" class=\"btn btn-danger\" value=\"Delete User\" name=\"button\">
  </li>
  </div>
  ";

/*
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"button\" value=\"Delete User\"></td>\n";
  echo "</tr>\n";
  table_end();
  echo "</form>\n";*/


  print_footer()

?>
