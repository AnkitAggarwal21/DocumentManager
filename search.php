<!DOCTYPE html>
<html lang="en">
<head>
  <title>Document Search List</title>
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

   (isset($_REQUEST['query']) ? ($_REQUEST['query']) : "")
   */
  $query = mysql_real_escape_string($_REQUEST['query']);
  
  

  $order = mysql_real_escape_string(rawurldecode(isset($_REQUEST['order']) ? ($_REQUEST['order']) : ""));


  $user = new user($_SESSION['login']);
  $query = strtolower($query);


  print_header("Search Results");

  //neutral_table_start("center", 0, 1);

  echo "<table class=\"table table-hover table-striped\">\n";
  echo "<thead>";

  echo "<tr>\n";
    printf("<th><span class=\"glyphicon glyphicon-download-alt\"></span></th>");
    printf("  <th><a>Filename</a></th>\n");
    //printf("  <th><a href=\"list?order=%s\">Size</a></th>\n", ($order == "size DESC" ) ? "size%20ASC" : "size%20DESC" );
    printf("  <th><a>Rev</a></th>\n");
    printf("  <th><a>Origin</a></th>\n");
    printf("  <th><a>Sent By</a></th>\n");
    printf("  <th><a>Pertains To</a></th>\n");
    printf("  <th><a>Document Date</a></th>\n");
    printf("  <th><a>Document No.</a></th>\n");
    printf("  <th><a>Created</a></th>\n");
    
    printf("  <th align=\"center\"><a>Status</a></th>\n");
    printf("  <th align=\"center\" colspan=\"2\"><a>Response To The Document</a></th>\n");

  echo "</tr></thead><tbody>\n";

  if($user->god) {
    
      $res = @mysql_query(" SELECT DISTINCT k.id AS id,d.name AS name,d.type AS type,d.size AS size,u.name AS author,d.maintainer AS maintainer, d.revision AS revision,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%d-%m-%Y, %H:%i:%S') AS modified,d.origin AS origin,d.dateletter AS ddate, d.letter AS letter,d.created AS cdate,d.modified AS mdate FROM documents_keywords AS k LEFT JOIN documents AS d ON k.id=d.id LEFT JOIN users AS u ON u.id=d.author LEFT JOIN documents_info AS i on i.id=k.id WHERE k.keyword LIKE '$query%' OR d.name LIKE '$query%' OR i.info LIKE '$query%' OR d.origin LIKE '$query' OR d.letter LIKE '%$query%' ORDER BY id ASC") or die ('Error updating database: '.mysql_error());

    } else {
      $res = mysql_query("SELECT DISTINCT k.id AS id,d.name AS name,d.type AS type,d.size AS size,u.name AS author,d.maintainer AS maintainer, d.revision AS revision,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%d-%m-%Y, %H:%i:%S') AS modified,d.origin AS origin,d.dateletter AS ddate, d.letter AS letter,d.created AS cdate,d.modified AS mdate,a.level AS level FROM documents_keywords AS k LEFT JOIN documents AS d ON k.id=d.id LEFT JOIN users AS u ON u.id=d.author LEFT JOIN documents_info AS i on i.id=k.id LEFT JOIN ACL AS a ON a.document_id=k.id WHERE (a.user_id=$user->id) AND (k.keyword LIKE '$query%' OR d.name LIKE '$query%' OR i.info LIKE '$query%' OR d.origin LIKE '$query' OR d.letter LIKE '%$query%') ORDER BY id ASC");
  }
  $count = mysql_num_rows($res);
  if(!($count)) {
    echo "<tr>\n";
    echo "  <td align=\"center\" colspan=\"7\">No documents found</td>\n";
    echo "</tr>\n";
  } else {
    echo "<h2 align=\"center\">Found $count matching documents</h2>\n";
    while($row = @mysql_fetch_array($res)) {
      echo "<tr>\n";
      echo "  <td><a href=\"download?doc_id=$row[id]\"><span class=\"glyphicon glyphicon-download-alt\"></span></a></td>\n";
      //echo "  <td align=\"right\">$row[id]\n";
       echo "  <td><form action=\"detail?doc_id=$row[id]\" method=\"post\"><input type=\"hidden\" name=\"query\" value=\"$query\"><input type=\"hidden\" name=\"type\" value=\"search\"><input type=\"submit\" value=\"$row[name]\" class=\"btn btn-link\"></form></td>\n";
      /*
      if($row['size'] < 0)
          continue;
      if( $row['size'] < 10240 ) {
          $size_str = sprintf("%d bytes", $row['size']);
      } else if( $row['size'] < 1048576 ) {
          $size_str = sprintf("%.1f Kb", ($row['size']/1024));
      } else {
          $size_str = sprintf("%.1f Mb", ($row['size'])/(1024*1024));
      }*/
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
    $myArray = explode(',', $maintainer);
    foreach($myArray as $keys)
    {
    $rs = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE name = '$keys'"));
    $id = $rs['id'];
    if($user->id == $id)
    {
      echo "  <td align=\"center\"><a align=\"center\" href=\"act?doc_id=$row[id]\"><input type=\"submit\" value=\"Take Action\" class=\"btn btn-danger\"></a></td>\n";
      echo "  <td align=\"center\"><a  href=\"view?doc_id=$row[id]\"><input type=\"submit\" class=\"btn btn-warning\" value=\"View\" align=\"center\"></a></td>\n";
      break;
    }
    else 
    {
      echo "  <td align=\"center\" colspan=\"2\"><a  href=\"view?doc_id=$row[id]\"><input type=\"submit\" class=\"btn btn-warning\" value=\"View\" align=\"center\"></a></td>\n";
      break;
    }
    
    }

    
      echo "</tr>\n";
    }
  }

  table_end();

  print_footer()

?>

<style type="text/css">
  .table > tbody > tr > td {
     vertical-align: middle;
}
</style>

</body>
</html>