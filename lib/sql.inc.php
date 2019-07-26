<?php
  @mysql_connect($cfg['server'], $cfg['user'], $cfg['pass']) or die("Unable to connect to SQL Server.");
  @mysql_select_db($cfg['db']) or die("Unable to select database {$cfg['db']}");
?>
