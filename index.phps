<?
session_start();
include_once ("config.php");
include_once ("header.php");
include_once ("guide_installation.php");

if (! GuideInstallation ()) {
  GuideInstallation(true); //run in verbose mode if it's not all set to go
}

else { // run script
  include ("mysql_connect.php");
  include ("scripts.php"); 

  
HandleCookies(); // sets $min and $post_class as defined or as default

print "<body>\n";

PrintPageTop();

// print_rr($_SESSION);


/* Add titles to DB if submitted */
if ($_REQUEST[bulk_submit]) { BulkSubmit(); }
elseif ($_REQUEST[add_title]) { AddTitle(); }

/* Show options menu (call number classes, minimum pcircs, etc) */
$menu = OptionsMenu();
print "<div id=\"top_menu\">$menu</div>\n";


/* Show appropriate titles: all known or just by call number */
 
 if($_REQUEST[show_all]) { ShowAllKnownTitles($_REQUEST[sort]); } // show all having min circ
 elseif ($_REQUEST[add_title]) { //if just one added, go back to last screen
   $count = ShowBooksByQuery($_SESSION[last_query]);
 }
 else { $count = ShowBooksByClass (); } 

 print "<hr><p><a href=\"pcirc_holdings.php\"><b>Add $Local_Catalog_Name holdings info for known titles</a></b></p>\n";
 if ($count >9)
   print "<hr><center><div id=\"bottom_menu\">$menu</div></center>";
} //end else run script if config in place

include ("version.php"); ?>
