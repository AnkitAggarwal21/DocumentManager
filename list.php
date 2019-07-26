<!DOCTYPE html>
<html lang="en">
<head>
  <title>Document List</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>



<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  /*
   * Basic input validation.
   */
  $order = mysql_real_escape_string(rawurldecode(isset($_REQUEST['order']) ? ($_REQUEST['order']) : "id DESC"));

  $user = new user($_SESSION['login']);

  $limit = 7;  
  if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };  
  $start_from = ($page-1) * $limit; 

  print_header("Document List");

  //neutral_table_start("center", 1, 1);


  echo "<table class=\"table table-hover table-striped\">\n";
  echo "<thead>";

  echo "<tr>\n";
    printf("<th><span class=\"glyphicon glyphicon-download-alt\"></span></th>");
    printf("  <th><a href=\"list?order=%s\">Filename</a></th>\n", ($order == "name DESC" ) ? "name%20ASC" : "name%20DESC" );
    
    printf("  <th><a href=\"list?order=%s\">Rev</a></th>\n", ($order == "revision DESC" ) ? "revision%20ASC" : "revision%20DESC" );
    printf("  <th><a href=\"list?order=%s\">Origin</a></th>\n", ($order == "origin DESC" ) ? "origin%20ASC" : "origin%20DESC" );
    printf("  <th><a href=\"list?order=%s\">Sent by</a></th>\n", ($order == "author DESC" ) ? "author%20ASC" : "author%20DESC" );
    printf("  <th><a href=\"list?order=%s\">Pertains To</a></th>\n", ($order == "maintainer DESC" ) ? "maintainer%20ASC" : "maintainer%20DESC" );
    printf("  <th><a href=\"list?order=%s\">Document Date</a></th>\n", ($order == "ddate DESC" ) ? "ddate%20ASC" : "ddate%20DESC" );
    printf("  <th><a href=\"list]?order=%s\">Document No.</a></th>\n", ($order == "letter DESC" ) ? "letter%20ASC" : "letter%20DESC" );

    printf("  <th><a href=\"list?order=%s\">Created</a></th>\n", ($order == "cdate DESC" ) ? "cdate%20ASC" : "cdate%20DESC" );

    
    printf("  <th align=\"center\"><a href=\"\">Status</a></th>\n");
    printf("  <th align=\"center\" colspan=\"2\"><a href=\"\">Response To The Document</a></th>\n");

  echo "</tr></thead>\n";

  if($user->god)
      if($order)
          $q = "SELECT d.id AS id,d.name AS name,d.type AS type,d.size AS size,u.name AS author,d.maintainer AS maintainer,d.revision AS revision,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%d-%m-%Y, %H:%i:%S') AS modified,d.origin AS origin,d.dateletter AS ddate, d.letter AS letter, d.created AS cdate,d.modified AS mdate FROM documents AS d LEFT JOIN users AS u ON u.id=d.author ORDER BY {$order} LIMIT $start_from, $limit";
      else
          $q = "SELECT d.id AS id,d.name AS name,d.type AS type,d.size AS size,u.name AS author,d.maintainer AS maintainer,d.revision AS revision,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%d-%m-%Y, %H:%i:%S') AS modified,d.origin AS origin, d.dateletter AS ddate, d.letter AS letter,d.created AS cdate,d.modified AS mdate FROM documents AS d LEFT JOIN users AS u ON u.id=d.author ORDER BY {$order} LIMIT $start_from, $limit";
  else
      if($order)
          $q = "SELECT d.id AS id,d.name AS name,d.type AS type,d.size AS size,u.name AS author,d.maintainer AS maintainer,d.revision AS revision,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%d-%m-%Y, %H:%i:%S') AS modified,d.origin AS origin, d.dateletter AS ddate, d.letter AS letter,d.created AS cdate,d.modified AS mdate,a.level AS level FROM documents AS d LEFT JOIN users AS u ON u.id=d.author LEFT JOIN ACL AS a ON a.document_id=d.id WHERE a.user_id=$user->id ORDER BY {$order} LIMIT $start_from, $limit";
      else
          $q = "SELECT d.id AS id,d.name AS name,d.type AS type,d.size AS size,u.name AS author,d.maintainer AS maintainer,d.revision AS revision,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%d-%m-%Y, %H:%i:%S') AS modified,d.origin AS origin, d.dateletter AS ddate, d.letter AS letter,d.created AS cdate,d.modified AS mdate,a.level AS level FROM documents AS d LEFT JOIN users AS u ON u.id=d.author LEFT JOIN ACL AS a ON a.document_id=d.id WHERE a.user_id=$user->id ORDER BY {$order} LIMIT $start_from, $limit";

  $res = mysql_query($q);
  if( ! ($count = @mysql_num_rows($res)) ) {
    echo "<tr>\n";
    echo "  <td align=\"center\" colspan=\"11\">No documents found</td>\n";
    echo "</tr>\n";
  } else {
    echo "<div class=\"container\" align=\"center\">
            <ul class=\"list-inline\">
              <li><h2>Listing $count document(s)</h2></li>";
          if($user->god && $user->id > 1)
          {            
            echo "<li align=\"right\"><a href=\"list1\" value=\"Show Documents for Me\" class=\"btn btn-success\">My Documents</a></li>";
          }


    echo "</ul>
          </div><tbody>";
    while($row = @mysql_fetch_array($res)) {
      echo "<tr>\n";
      echo "  <td><a href=\"download?doc_id=$row[id]\"><span class=\"glyphicon glyphicon-download-alt\"></span></a></td>\n";

      echo "  <td><form action=\"detail?doc_id=$row[id]\" method=\"post\"><input type=\"hidden\" name=\"type\" value=\"list\"><input type=\"hidden\" name=\"page\" value=\"$page\"><input type=\"hidden\" name=\"order\" value=\"$order\"><a><input type=\"submit\" value=\"$row[name]\" class=\"btn btn-link\"></a></form></td>\n";
//<a href=\"detail?doc_id=$row[id]\">$row[name]</a></td>\n";
      if($row['size'] < 0)
          continue;
      if( $row['size'] < 10240 ) {
          $size_str = sprintf("%d bytes", $row['size']);
      } else if( $row['size'] < 1048576 ) {
          $size_str = sprintf("%.1f Kb", ($row['size']/1024));
      } else {
          $size_str = sprintf("%.1f Mb", ($row['size'])/(1024*1024));
      }
      //echo "  <td align=\"right\">$size_str</td>\n";
      echo "  <td align=\"center\">$row[revision]</td>\n";
      echo "  <td>$row[origin]</td>\n";
      echo "  <td>$row[author]</td>\n";
      echo "  <td>$row[maintainer]</td>\n";
      echo "  <td>$row[ddate]</td>\n";
      echo "  <td>$row[letter]</td>\n";
      echo "  <td>$row[created]</td>\n";
    //printf("  <td%s</td>\n", ($row['modified'] == NULL) ? " align=\"center\">-" : ">$row[modified]" );
    

      $query = mysql_query("SELECT * from actions_content where docid = '$row[id]' ");
      $items = array();
      while($ros = mysql_fetch_array($query))
      {
        $id = $ros['id'];
        $name = mysql_fetch_array(mysql_query("SELECT name from users where id = '$id' "));
        $items[] = $name['name'];
      }
      $actiontaker = implode(",", $items);
    if($actiontaker)
    {
      echo "<td>Action taken by $actiontaker</td>\n";
    }
    else
    {
      echo "<td>No Action taken</td>\n";
    }

    $maintainer = $row['maintainer'];
    $flag = 1; 
    $myArray = explode(',', $maintainer);
    foreach($myArray as $keys)
    {
    $rs = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE name = '$keys'"));
    $id = $rs['id'];
    //echo "$id";
    if($user->id == $id)
    {
      echo "  <td align=\"center\"><a align=\"center\" href=\"act?doc_id=$row[id]\" class=\"btn btn-danger\" value=\"Take Action\">Take Action</a></td>\n";
      
      echo "  <td align=\"center\"><a align=\"center\" href=\"view?doc_id=$row[id]\" class=\"btn btn-warning\" value=\"View\">View</a></td>\n";

      $flag = 0;
      break;
    }
    }
    if($flag == 1)
    {
       echo "  <td align=\"center\" colspan=\"2\"><a align=\"center\" href=\"view?doc_id=$row[id]\" class=\"btn btn-warning\" value=\"View\">View</a></td>\n";

    //echo "  <td align=\"center\" colspan=\"2\"><a  href=\"view.php?doc_id=$row[id]\"><input type=\"submit\" value=\"View\"></a></td>\n";
    }

      # code...
    

    //echo "  <td align=\"center\"><a align=\"center\" href=\"act.php?doc_id=$row[id]\"><input type=\"submit\" value=\"Take Action\"></a></td>\n";
    
      echo "</tr>\n";
    }
  }
  table_end();

if($user->god)
   $q1 = "SELECT * FROM documents";

else
   $q1 = "SELECT d.id AS id,d.name AS name,d.type AS type,d.size AS size,u.name AS author,d.maintainer AS maintainer,d.revision AS revision,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%d-%m-%Y, %H:%i:%S') AS modified,d.origin AS origin, d.dateletter AS ddate, d.letter AS letter,d.created AS cdate,d.modified AS mdate,a.level AS level FROM documents AS d LEFT JOIN users AS u ON u.id=d.author LEFT JOIN ACL AS a ON a.document_id=d.id WHERE a.user_id=$user->id";
$res1 = mysql_query($q1);
$count = @mysql_num_rows($res1);
$totalPages = (ceil($count/7));
$i = 1;
    
echo "<div class=\"container\" align=\"center\">
  <ul class=\"pagination\">
  <li class=\"page-item disabled\"><a>Page No.</a></li>";
    for($i; $i<=$totalPages; $i++){
      if($page==$i)
      {
   echo "<li class=\"page-item active\"><a class=\"page-link\" href=\"list.php?page=$i&order=$order\">$i</a></li>";
      }
      else
      {
        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"list.php?page=$i&order=$order\">$i</a></li>";
      }

}
 echo  "</ul>
</div>";

/*for ($i; $i<=$totalPages; $i++){
   echo '<a href="list.php?page='.$i.'&order='.$order.'">['.$i.']</a>';
}*/


  
  
  print_footer()

?>

<style type="text/css">
  .table > tbody > tr > td {
     vertical-align: middle;
}
</style>

</body>
</html>
