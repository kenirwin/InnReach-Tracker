<?php

/* define local catalogs using only subdomain.domain.tld */
$Local_Institution_Name = ""; // ex: Wittenberg University
$Local_Institution_Short_Name = ""; // ex: Witt
$Local_Catalog_Name= ""; // ex: EZRA
$Local_Catalog_URL = ""; // ex: ezra.wittenberg.edu
$InnReach_Catalog_URL = "olc1.ohiolink.edu";
$InnReach_Catalog_Name = "OhioLINK";
$Upload_Folder = ""; //path with no trailing slash; outside the webserver space, e.g. /var/upload/innreach

$MySQL_Host = "localhost";   /* if "localhost" doesn't work, try "127.0.0.1" */
$MySQL_Database = ""; //ex: "innreach_data"
$MySQL_User = ""; 
$MySQL_Password = "";

//convert database variables to constants
$constants = array ('Upload_Folder','MySQL_Host','MySQL_Database','MySQL_User','MySQL_Password');

foreach ($constants as $v) {
    define(strtoupper($v),$$v);
}

$ptypes = array();

/* 
   The "ptypes" array defines the patron types that may initiate INNreach
   requests. These should be listed in the same order that they appear in 
   the web management reports from your catalog.

   Starting with version 1.0.2, $ptypes is an associative array 

   Example: 
       $ptypes = array("students" => "Students",
		"faculty" => "Faculty",
		"staff" => "Staff",
		"sce" => "School of Community Ed.",
		"hs" => "High School Scholars");
*/

$allow_uploads = true;

/*
  $allow_uploads is set to "true" by default.

  $allow_uploads sets whether or not the user may upload tab-delimited
  monthly innreach data files to the server through this web interface.
  (other options include using a program like phpMyAdmin or a direct
  MySQL interface, either of which would be password protected.)

  if uploads are enabled, you will need to be sure that your webserver
  has write permissions to the tmp/ directory. You may need to get your 
  system administrator to help with that.

*/


/* End date of the first month for which you have III title data.
   For most users, this may be the same month you started exporting
   data at all, in which case you can just leave this commented out.
   For those of us who started just with call number data, this variable 
   gets to be helpful.
*/
// $date_of_first_title_data = "2006-11-30"; 

/* default display settings */
$Default_Mininum_Pcirc_Display = 2; 
$Default_LC_Class = "A"; 

/* icon files */
$check_local_catalog_icon = "images/check_local_arrow.gif";
$check_innreach_catalog_icon = "images/check_innreach.png";
$check_google_icon = "images/google.ico";
$local_holdings_unknown_icon = "images/question.png";
$local_holdings_yes_icon = "images/have.png";
$local_holdings_no_icon = "images/nohave.png";

/* III web management reports link */
$III_manage = "http://" . $Local_Catalog_URL . "/manage";

/* icon link formatting */
/* you probably don't need to edit this section, but it's here if you do */

$question = "<img src=\"$local_holdings_unknown_icon\" title=\"$Local_Institution_Short_Name holdings unknown\">\n";
$have_icon = "<img src=\"$local_holdings_yes_icon\" title=\"Some version held at $Local_Institution_Short_Name\">\n";
$nohave_icon = "<img src=\"$local_holdings_no_icon\" title=\"No $Local_Institution_Short_Name holdings at last check\">\n";
$check_innreach_icon = "<img src=\"$check_innreach_catalog_icon\" height=20 title=\"Check $InnReach_Catalog_Name\">\n";
$check_local_icon = "<img src=\"$check_local_catalog_icon\" height=12 title=\"Check $Local_Catalog_Name\">\n";
$google_icon = "<img src=\"$check_google_icon\" title=\"Check Google\">\n";

?>
