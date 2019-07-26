<!DOCTYPE html>
<html>
<head>
  <title>Take Action</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>
<script type="text/javascript">
   $(document).ready(function(){
    $('.add_more').click(function(e){
        e.preventDefault();
        $(this).before("<p><input type=\"file\" name=\"userfile[]\"><a href=\"javascript:void(0);\" onclick=\"delfile(this);\" style=\"border-top-left-radius: 0px; border-botton-left-radius: 0px\"><button class=\"btn btn-danger\">Delete</button></a></p>");
    });
});

   function delfile(id)
   {
    $(id).parent().remove();
   }


</script>


<?php
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');


  /*
   * Basic input validation.
   */
  $doc_id = intval($_REQUEST['doc_id']);
  $user = new user($_SESSION['login']);
  $document = new document($doc_id);
  $document->get_access($user->id);
  $author = $document->author;
  $maintainer = $document->maintainer;


  print_header("Action to be taken");



  echo "<form action=\"action\" method=\"post\" enctype=\"multipart/form-data\">\n";
  echo "<input type=\"hidden\" name=\"doc_id\" value=\"$doc_id\">\n";
  echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"16777216000\">";

  //table_start("center", 1, 0);

  echo "
  <div class=\"container\">
  <h2>Reply/Action to document $document->name</h2>
  <div class=\"list-group\">
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Choose File</h4>
      <p class=\"list-group-item-text\">Select file if any</p>
      <input type=\"file\" name=\"userfile[]\">
    </li>
    <li class=\"list-group-item\">
      <button class=\"add_more btn btn-info\">Add More Files</button>
    </li>
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Info</h4>
      <p class=\"list-group-item-text\">Enter a short description of response to document</p>
      <textarea class=\"form-control\" name=\"comment\" rows=\"3\"></textarea>      
    </li>
    <li class=\"list-group-item\">
      <input type=\"submit\" value=\"Submit Response\" class=\"btn btn-primary\">
    </li>
    </div>
    




  ";

  /*echo "<tr>\n";
   echo "<td align=\"center\" colspan=\"2\"><font color=\"#000000\"><h3 align=\"center\">Reply/Action to document $document->name</h3></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
   echo "<td><font color=\"#000000\">Response File:</font></td>\n";
   echo "<td><input type=\"file\" name=\"userfile[]\"><br><font class=\"desc\">Select a file if any.</font></td>\n";
   
   //echo "<td><button DELETE</button></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td></td>\n";
  echo "<td><button class=\"add_more\">Add More Files</button></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td valign=\"top\"><b><font color=\"#000000\">Comments:</font></b></td>\n";
    echo "<td><textarea name=\"comment\" rows=\"4\" cols=\"28\"></textarea><br><font class=\"desc\">Enter a short comment in response to the document</font></td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
    echo "<td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"Submit Response\"></font></td>\n";
  echo "</tr>\n";

  table_end();*/
  echo "</form>\n";

  

  print_footer()

?>
</body>
</html>

