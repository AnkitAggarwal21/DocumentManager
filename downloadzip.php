<?
  require('lib/config.inc.php');
  require('lib/auth.inc.php');
  require('lib/classes.inc.php');
  require('lib/functions.inc.php');

  global $cfg;

  /*
   * Basic input validation.
   */
  //$doc_id = intval($_POST['docid']);
  $id = intval($_POST['id']); 
  $content = $_POST['content'];

  $user = new user($_SESSION['login']);

  $myArray = explode(',', $content);
      foreach ($myArray as $key) {
        $names = end(explode('/',$key));
        $items1[] = $names;
      }

         $zip = new ZipArchive;
         $zip_name = time().'.zip';
         $zip_dir = './Actions/'.$zip_name;
         if($zip->open($zip_dir,ZipArchive::CREATE) === TRUE)
         {
         foreach ($items1 as $position => $file1) {
         $file = end(explode('|',$file1));

         $zip->addFile('./Actions/'.$file1,'('.$position.')'.$file);
         }
         $zip->close();
         }
   
    $rest = end(explode('/',$zip_dir));
    header('content-Disposition: attachment; filename = "'.$rest.'"');
    header('content-type:appliction/octent-strem');
    header('content-length='.filesize($zip_dir));
    readfile($zip_dir);
    unlink($zip_dir);
  //}

?>
