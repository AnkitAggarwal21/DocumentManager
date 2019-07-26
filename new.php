<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Include jQuery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

<!-- Include Date Range Picker -->


<?php

    require('lib/config.inc.php');
    require('lib/auth.inc.php');
    require('lib/classes.inc.php');
    require('lib/functions.inc.php');

    $user = new user($_SESSION['login']);

    if(! $user->god) {
        print_header("Access Dennied!");
        exit;
    }

    print_header("Upload a new document");

     echo "<form action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\" id=\"upform\">\n";
    echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"16777216000\">\n";

    echo "
<div class=\"container\">
  <h2>Upload New Document</h2>
  <div class=\"list-group\">
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Choose File</h4>
      <p class=\"list-group-item-text\">Click to browse for file</p>
      <input type=\"file\" name=\"userfile\">
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Select the default access</h4>
      <form>
        <label class=\"radio-inline\">
          <input type=\"radio\" name=\"acessradio\" value=\"X\">
            Selective Access
          </input>
        </label>
        <label class=\"radio-inline\">
          <input type=\"radio\" name=\"acessradio\" value=\"W\">
            Everybody Access
          </input>
        </label>";
        
        echo "


        
    </form>  
    </li>
    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Select people to give access to</h4>
      <p class=\"list-group-item-text\">Press button to expand list</p>
      <select class=\"selectpicker\" multiple data-live-search=\"true\" name=\"For[ ]\">
      ";


      $query = mysql_query(" SELECT * from users ");
      while($ros = mysql_fetch_array($query))
      {
        $name = $ros['name'];
        $pid = $ros['id'];
        if($pid==$user->id || $pid == 1)
          {
            continue;
          }
        else{
        echo "<option value=\"$pid\">$name</option>\n";}
      }
      echo "</select></li>";

    


      echo"
      </li>
      <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Origin of the Document</h4>
      <p class=\"list-group-item-text\">Department Name</p>
      <input type=\"text\" maxsize=\"512\" name=\"origin\" class=\"form-control\">
    </li>

    <li class=\"list-group-item\">
    <h4 class=\"list-group-item-heading\">Date of the Document</h4>
      <p class=\"list-group-item-text\">Date as appears on the document</p>
    <input class=\"form-control\" id=\"date\" name=\"date\" placeholder=\"DD/MM/YYYY\" type=\"text\" style=\"margin-top:8px;\">
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Letter Number</h4>
      <p class=\"list-group-item-text\">Letter number as appears on document</p>
      <input type=\"text\" maxsize=\"512\" name=\"letter\" class=\"form-control\">
    </li>


    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Keywords</h4>
      <p class=\"list-group-item-text\">Separate keywords with a comma (,)</p>
      <input type=\"text\" maxsize=\"512\" name=\"keywords\" class=\"form-control\">
    </li>

    <li class=\"list-group-item\">
      <h4 class=\"list-group-item-heading\">Info</h4>
      <p class=\"list-group-item-text\">Enter a short description of the document</p>
      <textarea class=\"form-control\" name=\"info\" rows=\"3\"></textarea>
      
    
    
    <li class=\"list-group-item\" align=\"center\"><input type=\"submit\" class=\"btn btn-primary\" value=\"Upload this document\"></li>

  </div>


";

          echo "</form>\n";

    print_footer();

?>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

<script>
    $(document).ready(function(){
        var date_input=$('input[name="date"]'); //our date input has the name "date"
        var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
        date_input.datepicker({
            format: 'dd/mm/yyyy',
            container: container,
            todayHighlight: true,
            autoclose: true,
        })
    })
</script>




</head>
</html>

