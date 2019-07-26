<?php

    // Used for the main page heading and to
    // set a realm for authentication.
    //
    $cfg['site_name']   = "SDMS";
    $cfg['version']		= "1.2";

    // Details for the mysql server.
    //
    $cfg['server']		= "localhost";
    $cfg['user']		= "root";
    $cfg['pass']		= "";
    $cfg['db']			= "documents";

    // Stuff below is used for setting colours.
 // /*   //
    $cfg['page_bg']		= "#FFFFFF";
    $cfg['text']   		= "#000000";
    $cfg['link_colour']		= "#CC0000";
    $cfg['active_link_colour']	= "#FFFF00";
    $cfg['visited_link_colour']	= "#888888";

    $cfg['table_bg']		= "#000000";
    $cfg['table_text']		= "#FFFFFF";

    $cfg['header_bg']		= "#000000";
    $cfg['header_text']		= "#FFFFFF";

    // Show some very rudimantary debug stuff.
    //
    $cfg['debug']			= 0;
// */
    // Global vars for debugging.
    //
    $debug_time_start		= 0;
    $debug_time_end		= 0;

    // Mysql password function. Default to using SHA1()
    // For old SDMS instalaltions, this should be OLD_PASSWORD
    // stay compatible with old sdms installs.
    $cfg['pwfunc']		= 'SHA1';

    require_once('lib/sql.inc.php');
    session_start();
?>
