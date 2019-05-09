<?php

function GuideInstallation ($verbose = false) {
  global $debug;
  if (! $debug) { ERROR_REPORTING(0); }
  include_once ("config.php");
  include_once ("pdo_connect.php");
  $print = "<h1>InnReach Tracker - Installation Progress</h1>\n";
  $config_vars = array ("Local_Institution_Name", "Local_Institution_Short_Name", "Local_Catalog_Name", "Local_Catalog_URL", "InnReach_Catalog_URL", "InnReach_Catalog_Name", "MySQL_Host", "MySQL_Database", "MySQL_User", "MySQL_Password");
  if (is_null($db)) {
    $print .= "<h2>First: create a new MySQL database</h2>\n";
    $print .= "<p>Call it whatever you want, ('innreach', or 'innreach_tracker' would be sensible.) Whatever you call it, that'll be the value you give to the MySQL_Database variable in config.php.</p>\n";
  } //end if not connected to a mysql database

  $print .= "<h2>Define Variables in config.php</h2><ul>\n";
 
  foreach ($config_vars as $var) {
    if (${$var}) { $print .= "<li class=good>$var defined</li>\n";}
    else { 
      $print .= "<li class=bad>Please define $var in config.php</li>\n"; 
      $incomplete = true;
    }
  }
  
  if ((sizeof($ptypes) > 0) && (is_assoc($ptypes))) {
    // note: is_assoc is defined below; not a standard PHP function
    $print .= "<li class=good>ptypes array defined</li>\n";
  } //end if $ptypes is a non-empty associative array
  else { 
    $print .= "<li class=bad>Please add values to ptypes array in config.php</li>\n"; 
    $incomplete = true;
  } //end else
$print .= "</ul>\n";

$print .= "<h2>Connect to MySQL Database</h2>\n";
/* only try to connect to mysql database if we're ok so far */
if (! $incomplete) {
$print .= "<ul>\n";
if (get_class($db) == 'PDO') {
$print .= "<li class=good>Connected to MySQL Database $MySQL_Database</li>\n";
}
else {
$print .= "<li class=bad>Unable to connect to MySQL Database $MySQL_Database with variables assigned in config.php</li>\n"; 
$incomplete = true;
}
$print .= "</ul>\n";

} //end if trying to connect to mysql / config variables are assigned
  
  else { $print .= "<p class=bad>We'll try that once your variable are established in config.php</p>"; }
  
  $print .= "<h2>Establishing necessary database tables & loading data</h2>\n";
  if (! $incomplete) {
    $print .= "<ul>\n";
    $required_tables = array("innreach_by_call", "innreach_by_title", "innreach_stats_by_ptype", "innreach_titles_by_call", "major_lc");
    $stmt = $db->query("SHOW TABLES");
    $extant_tables = array();
while ($myrow = $stmt->fetch(PDO::FETCH_NUM)) {
      array_push($extant_tables, $myrow[0]);
    }
    foreach ($required_tables as $table) {
      if (in_array($table, $extant_tables))
	$print .= "<li class=good>Table exists: $table</li>\n";
      else {
	$print .= "<li class=bad>Table doesn't exist: $table. Create table by running: <a href=\"create_mysql_tables.php\">create_mysql_tables.php</a>.</li>\n";
	$incomplete = true;
      } //end else if table is missing
    }//end foreach table
    $print .= "</ul>\n";
  } //end if all connected to mysql

  else { $print .= "<p class=bad>We'll try that once you're able to connect to MySQL</p>\n"; }

  if (! $incomplete) {
    $print .= "<ul>\n";
    $tables_with_preloaded_data = array ("major_lc", "innreach_titles_by_call");
    foreach ($tables_with_preloaded_data as $table) {
      $q = "SELECT count(*) FROM $table";
$stmt = $db->query($q);
$n = $stmt->fetch(PDO::FETCH_NUM);
      if ($n[0] > 200)
	$print .= "<li class=good>Table $table contains data</li>\n";
      else {
	$print .= "<li class=bad>Table $table is missing its data. There's no easy fix for that. See if you can execute the commands in the file $table".".sql to get that data loaded up.</li>\n";
	$incomplete = true;
      } //end else if data is missing from table
    } //end while checking for data in tables
    $print .= "</ul>\n";
  } //end if no errors creating tables


  
  $print .= "<h2>Load InnReach data from your III catalog</h2>\n";
  if (! $incomplete) {
    /* overwrite all the preceding info and move on to more important stuff */
    $print = "<h1 class=good>The InnreachTracker is successfully installed!</h1><p>Next we need to get data from your catalog into the database.</p>";
    $print .= "<ul>\n";
    $tables_with_user_loaded_data = array ("innreach_by_call", "innreach_by_title", "innreach_stats_by_ptype");
    foreach ($tables_with_user_loaded_data as $table) {
      $q = "SELECT count(*) FROM $table";
$stmt = $db->query($q);
$n = $stmt->fetch(PDO::FETCH_NUM);
      if ($n[0] > 1)
	$print .= "<li class=good>Table $table contains data</li>\n";
      else {
	if ($table == "innreach_stats_by_ptype") { 
	  $extra = "Run the <strong><a href=\"pcirc_sum_stats.php\">Stats Update</a></strong> after uploading Title & Call# data.";
	} //end if stats table
	else { 
	  $extra = "Use the <strong><a href=\"upload_data.php\">Upload Form</a></strong> or see below.\n";
	} //end else 
	$print .= "<li class=bad>Table $table doesn't contain any data yet. $extra</li>\n";
	$incomplete = true;
	$show_data_load_instructions = true;
      }//end else if data is missing from table
    } //end if ready to check for data in user-loaded tables
  $print .= "</ul>\n";
  } //end while/if looking for user-loaded data

  else { $print .= "<p class=bad>We'll look for this data once your tables are set up right.</p>\n"; }

  if ($verbose) { 
    print $print; 
    if ($show_data_load_instructions) {
      include("documentation_how_to_add_circ_data.php");
    }
  }

  if ($incomplete) 
    return false;
  else 
    return true;

} //end function GuideInstallation



function is_assoc($var) {
  //returns true if array is styled as associative array
  //code based on: http://us.php.net/manual/en/function.is-array.php#90727
  
  $comparison = (PHP4_array_diff_key($var,array_keys(array_keys($var))));
  return ((is_array($var)) &! empty($comparison));
} //end function is_assoc

function PHP4_array_diff_key()
{
  $arrs = func_get_args();
  $result = array_shift($arrs);
  foreach ($arrs as $array) {
    foreach ($result as $key => $v) {
      if (array_key_exists($key, $array)) {
	unset($result[$key]);
      }
    }
  }
  return $result;
}

?>
