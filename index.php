<?php
error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', 1);
$debug = false; //true;

session_start();
include_once ("guide_installation.php");

/* 
   this oddity is to eliminate some conflict between GuideInstallation and scripts.php
   if the system is fully install, it sets a cookie and forwards to itself to move on to the regular business
   until then, it runs the guided installation checker
*/

if (! array_key_exists('install_confirmed',$_SESSION)) {
    if (GuideInstallation()) { 
        $_SESSION['install_confirmed'] = true; 
        header('Location: index.php');
    }
    else {
        GuideInstallation(true); //run in verbose mode if it's not all set to go
    }
}

else { // run script
    include_once ("config.php");
    include_once ("header.php");
    include_once ("pdo_connect.php"); //return $db
    include_once ("scripts.php"); 
    
    if ($_REQUEST['delete_session_vars']) { session_unset(); }
    HandleCookies(); // sets $min and $post_class as defined or as default
    ThinkAbout($_SESSION);
    print "<body>\n";
  
    PrintPageTop();


  /* Add titles to DB if submitted */
  if ($_REQUEST[bulk_submit]) { BulkSubmit(); }
  elseif ($_REQUEST[add_title]) { AddTitle(); }
  
  
  
  /* Show options menu (call number classes, minimum pcircs, etc) */
  $menu = OptionsMenu();
  print "<div id=\"top_menu\">$menu</div>\n";
  

  /* Show appropriate titles: all known or just by call number */
  
  if($_SESSION[view] == "show_all") { ShowAllKnownTitles($_SESSION[sort]); } // show all having min circ
  elseif ($_REQUEST[add_title]) { //if just one added, go back to last screen
    $count = ShowBooksByQuery($_SESSION[last_query]);
  }
  else { $count = ShowBooksByClass (); } 
  
  print '<hr><p><a href="pcirc_holdings.php"><b>Add '.$Local_Catalog_Name.' holdings info for known titles</a></b></p>'.PHP_EOL;
  if ($allow_uploads == true) { print '<p><a href="upload_data.php"><b>Upload new InnReach Data</a></b></p>'.PHP_EOL; }
  print '<p><a href="matrix.php"><b>Matrix of Recent PCIRCs and Recent Publications</b></a></p>'.PHP_EOL;
  if ($count >9)
    print '<hr><center><div id="bottom_menu">'.$menu.'</div></center>';
} //end else run script if config in place

include ("license.php"); ?>

<p>For more information, please see the <strong><a href="readme.php">Documentation</a></strong>.</p>
