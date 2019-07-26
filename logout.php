<?php
    require('lib/config.inc.php');
    session_destroy();

    header("Location: index");
?>
