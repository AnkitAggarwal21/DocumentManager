<!DOCTYPE html>
<html lang="en">
<head>
  <title>Document Detail</title>
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<body>



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

  print_header("Document Information");

  $order = mysql_real_escape_string(rawurldecode(isset($_REQUEST['order']) ? ($_REQUEST['order']) : ""));
  $page = intval(isset($_REQUEST['page']) ? ($_REQUEST['page']) : "");
  $type = isset($_REQUEST['type']) ? ($_REQUEST['type']) : "";
  $query = mysql_real_escape_string(isset($_REQUEST['query']) ? ($_REQUEST['query']) : "");
  //neutral_table_start("center", 1, 0);

  echo "
  <div class=\"container\">
  <h2>Details for $document->name</h2>
  <div class=\"list-group\">
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Description</h4>";

      printf("<p>%s\n", ($document->info == NULL) ? "No information" : hilite(htmlspecialchars(stripslashes($document->info))) );
      printf("%s\n", (get_extension($document->name) != "exe") ? "" : "<p class=\"descb\">Note: This application has not been scanned for viruses!" );

    echo "</li>
     <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Keywords:</h4>
      
    

    ";



  /*echo "<tr>\n";
    echo "<td >\n";
    echo "</td>\n";
    echo "<td align=\"center\">\n";
    echo "<h2 align=\"center\">Details for $document->name</h2>\n";
    echo "</td>\n";*/
    
  /*echo "</tr>\n";
  echo "<tr>\n";
    echo "<td colspan=\"2\">\n";
    neutral_table_start("center", 1, 0);
    echo "<tr>\n<td width=\"400\">\n";
    printf("<p>%s\n", ($document->info == NULL) ? "No information" : hilite(htmlspecialchars(stripslashes($document->info))) );
    printf("%s\n", (get_extension($document->name) != "exe") ? "" : "<p class=\"descb\">Note: This application has not been scanned for viruses!" );
    echo "</td>\n</tr>\n";
    table_end();
    echo "</td>\n";
    echo "<td rowspan=\"12\" valign=\"top\">\n";
    neutral_table_start("center", 1, 1);
    echo "<tr>\n<td align=\"center\">\n";
    echo "Keywords:\n";
    echo "<ul>\n";*/
    if($document->keywords)
    {
    $kw = current($document->keywords);
    do {
      echo "". hilite($kw) .", " ;
    } while( $kw = next($document->keywords) );
    }
    else
      {echo ", ";}
    /*echo "</ul>\n";
    //echo "</td>\n</tr>\n";
    //table_end();
    echo "</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td colspan=\"2\"> </td>\n";
  echo "</tr>\n";
  echo "<tr>\n";*/
    echo "</li>";
    echo "
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Size</h4>
      <p class=\"list-group-item-text\">". number_format($document->size, 0, ".", ",") ." bytes</p>
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Author</h4>
      <p class=\"list-group-item-text\">$author->name</p>
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Maintainer</h4>
      <p class=\"list-group-item-text\">$maintainer</p>
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Uploaded</h4>
      <p class=\"list-group-item-text\">$document->created</p>
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Revision</h4>
      <p class=\"list-group-item-text\">$document->revision</p>
    </li>";

    echo "<li class=\"list-group-item\">
    <table>
    <tr><td><form action=\"download\" method=\"post\"><input type=\"hidden\" name=\"doc_id\" value=\"$document->id\"><input type=\"submit\" class=\"btn btn-info\" value=\"Download File\"></form></td>
    ";
        
      if($user->id == $author->id)
    {
    echo "<td><p>&nbsp;</p></td><td><form action=\"delete\" method=\"post\"><input type=\"hidden\" name=\"doc_id\" value=\"$document->id\"><input type=\"submit\" value=\"Delete Document\" class=\"btn btn-danger \"></form></td>\n";
    }   
    

    if($user->id == 1)
      echo "<td><p>&nbsp;</p></td><td><form action=\"up?docid=$document->id\" method=\"post\"><input type=\"hidden\" name=\"docid\" value=\"$document->id\"><input type=\"hidden\" name=\"query\" value=\"$query\"><input type=\"hidden\" name=\"page\" value=\"$page'\"><input type=\"hidden\" name=\"order\" value=\"$order\"><input type=\"hidden\" name=\"type\" value=\"$type\"><input type=\"submit\" value=\"Update Document\" class=\"btn btn-info \"></form></td></tr></table>\n";
    else
    {
      $query1 = mysql_query("SELECT document_id FROM acl where user_id = $user->id AND level = 'W' ");
      while ($ros = mysql_fetch_array($query1))
      {
        $docid = $ros['document_id'];
        if ($document->id == $docid)
        {
      echo "<td><p>&nbsp;</p></td><td><form action=\"up?docid=$document->id\" method=\"post\"><input type=\"hidden\" name=\"docid\" value=\"$document->id\"><input type=\"hidden\" name=\"query\" value=\"$query\"><input type=\"hidden\" name=\"page\" value=\"$page'\"><input type=\"hidden\" name=\"order\" value=\"$order\"><input type=\"hidden\" name=\"type\" value=\"$type\"><input type=\"submit\" value=\"Update Document\" class=\"btn btn-info \"></form></td></tr></table>\n";
        }
      }
    }



        //$res = @mysql_query("SELECT d.id AS id,d.name AS name,DATE_FORMAT(d.created, '%d-%m-%Y, %H:%i:%S') AS created, d.created AS cdate, a.level AS level FROM documents AS d LEFT JOIN ACL AS a ON a.document_id=d.id WHERE a.user_id=$user->id AND (a.level='W' OR a.level='G') ORDER BY cdate DESC");
    echo "</li></div>";
    

   /* echo "<td>File name:</td>\n";
    echo "<td>$document->name</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td>File size:</td>\n";
    echo "<td>". number_format($document->size, 0, ".", ",") ." bytes</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td colspan=\"2\"> </td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td>Author:</td>\n";
    echo "<td>$author->name &lt;<a href=\"mailto:$author->email\">$author->email</a>&gt;</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td>Maintainer:</td>\n";
    echo "<td>$maintainer</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td>Created:</td>\n";
    echo "<td>$document->created</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td>Revision:</td>\n";
    echo "<td>$document->revision</td>\n";
  echo "</tr>\n";

  echo "<td align=\"right\"><form action=\"download.php\" method=\"post\"><input type=\"hidden\" name=\"doc_id\" value=\"$document->id\"><input type=\"submit\" class=\"btn btn-info\" value=\"Download\"></form></td>\n";


  table_end();*/

  print_footer()

?>


</body>
</head>