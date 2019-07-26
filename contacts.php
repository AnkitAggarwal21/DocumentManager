<!DOCTYPE html>
<html lang="en">
<head>
  <title>Contacts</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
</head>

<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  $user = new user($_SESSION['login']);

  print_header("Contacts List");

  echo "<div class=\"container\" align=\"center\">\n";
  echo "<h2 align=\"left\">Contact List</h2>\n";

  //neutral_table_start("center", 1, 0);

  
  echo "<table class=\"table table-hover table-striped\">\n";
  echo "<thead>
      <tr>
        <th>Name</th>
        <th>Email</th>";
  if($user->id == 1)
   {
    echo "<th>Reset password</th>\n";
   }
  echo "
      </tr>
    </thead>";
 

  $res = mysql_query("SELECT id,user,name,email FROM users where id>1 ORDER BY id ASC");
  while( $row = mysql_fetch_array($res) )
  {
    echo "<tr>
            <td align=\"left\">
              <a href=\"userdetail?contact=$row[user]\">$row[name]</a>
            </td>
            <td align=\"left\">
              <p href=\"mailto:$row[email]\">$row[email]</p>
            </td>";
    if($user->id == 1)
    {
    echo "<td align=\"left\">
            <form action=\"reset?userid=$row[id]\" method=\"post\">
              <input type=\"hidden\" name=\"userid\" value=\"$row[id]\">
              <input type=\"submit\" class=\"btn btn-primary\" value=\"Reset Password\">
            </form>\n";
    }
  }


  echo "</tr>\n";

  table_end();


  print_footer()

?>

</html>