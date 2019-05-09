<link href="pcirc_style.css" rel="stylesheet" type="text/css">

<?php
$debug = false; // true; 
if (! $debug) { ERROR_REPORTING(0); }

function HandleQuery ($q) {
  // $debug = "yes";
    global $db;
    $results = array();
  if ($debug) { print "<div style=\"border: 1px solid grey\">$q<br>\n"; }
  $stmt = $db->query($q);
  if ($stmt) {
    $results['success'] .= "<li class=good>SUCCESS: $q</li>\n";
    if ($debug) { print "<li class=good>SUCCESS: $q</li>\n"; }
  }
  else {
    $results['fail'] .= "<li class=bad>FAILED: $q</li>\n"; 
    if ($debug) { print "<li class=bad>FAILED: $q</li>\n"; }
  }
  if ($debug) { print "</div>\n";}
  return ($results);
}

function parse_mysql_dump($url, $ignoreerrors = false){

     /* 
      from:  http://code.google.com/p/simpleinvoices/source/browse/tags/20071231/install/connection_post.php

     modified by ken to allow $url to be an array
     array should be in list-style (not associative)
     */

  if (is_array($url)) {
    $file_content = $url;
  }
  else {
    $file_content = file($url);
  }  
  
  $query = "";
  foreach($file_content as $sql_line) {
    $tsl = trim($sql_line);
    if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
      $query .= $sql_line;
      if (preg_match("/;\s*$/", $sql_line)) {
	$stmt = $db->query($query);
	if (!$stmt && !$ignoreerrors) {
	  die("I die!");
	}
	$query = "";
      }
    }
  } //end foreach element in array
} //end function parse_mysql_dump

include ("config.php");
include ("pdo_connect.php");
include ("scripts.php");

if (! $MySQL_Database) { 
  echo "Please define \$MySQL_Database config.php before proceeding\n"; 
  $errors++;
}
if (sizeof($ptypes) == 0) {
  echo "Please define \$ptypes array in config.php before proceeding\n";
  $errors++;
}

if (! $MySQL_User) {
  echo "Please define \$MySQL_User in config.php before proceeding";
  $errors++;
}
if (! $MySQL_Password) {
  echo "Please define \$MySQL_Password in config.php before proceeding";
  $errors++;
}

if ($errors < 1) {
  /* get list of ptypes and dynamically generate the right table rows */
  foreach ($ptypes as $ptype => $ptype_name) 
    $ptype_rows .= "`$ptype` int(11) NOT NULL default '0',";

  /* get a list of extant tables from prior installations */
  $extant_tables = array ();
  $q = "SHOW TABLES";
  $stmt = $db->query ($q);
  while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $temp = $myrow[0];
    $extant_tables[$temp] = $temp;
  }
  if ($debug)  { print_rr($extant_tables);}

  if ($extant_tables[innreach_by_call]) {
    $success .= "<li class=good>SUCCESS: innreach_by_call already exists</li>";
  }
  else {
  $q1 =  "CREATE TABLE innreach_by_call (
  `call` varchar(255) NOT NULL default '',
  $ptype_rows
  `total` int(11) NOT NULL default '0',
  `month_ending` date NOT NULL default '0000-00-00'
  )";
  $results = HandleQuery ($q1);
  $success .= $results[success];
  $fail .= $results[fail];
  }
  /* 
     $q2 -- query two was lost at sea, never to be heard from again
     and was never really missed at all.
  */

  if ($extant_tables[innreach_by_title]) {
    $success .= "<li class=good>SUCCESS: innreach_by_title already exists</li>";
  }
  else {
    $q3 = "CREATE TABLE innreach_by_title (
  `title` varchar(255) NOT NULL default '',
  $ptype_rows
  `total` int(11) NOT NULL default '0',
  `month_ending` date NOT NULL default '0000-00-00'
  )";
    $results = HandleQuery($q3);
    $success .= $results[success];
    $fail .= $results[fail];
  } //end else

  if ($extant_tables[innreach_titles_by_call]) {
    $success .= "<li class=good>SUCCESS: innreach_titles_by_call already exists</li>";
    // if table exists, learn its contents for later comparison
    $old_itbc = array (); //stores call numbers of known titles
    $q5 = "SELECT distinct(`call`) FROM innreach_titles_by_call";
    $stmt5 = $db->query($q5);
    while ($myrow = $stmt5->fetch(PDO::FETCH_ASSOC) {
      extract ($myrow);
      array_push ($old_itbc, $call);
    } //end while learning old data
    if ($debug) { print_rr($old_itbc); }
  } //end if itbc exists already
  

  else { // if itbc doesn't yet exist
    $q6 = "CREATE TABLE innreach_titles_by_call (
  `call` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `have` varchar(50) default NULL,
  `pub_date` year(4) default NULL,
  KEY `call` (`call`)
  )";
    $results = HandleQuery($q6);
    $success .= $results[success];
    $fail .= $results[fail];
  } //end else


  if ($extant_tables[major_lc]) {
    $success .= "<li class=good>SUCCESS: major_lc already exists</li>";
  }
  else {
    $q7 = "CREATE TABLE `major_lc` (
  `class` char(3) NOT NULL default '',
  `subject` varchar(255) NOT NULL default ''
  )";
    $results = HandleQuery($q7);
    $success .= $results[success];
    $fail .= $results[fail];
  } //end else 

  if ($extant_tables[innreach_stats_by_ptype]) {
    $success .= "<li class=good>SUCCESS: innreach_stats_by_ptype already exists</li>";
  }
  else {
    $q10 = "CREATE TABLE innreach_stats_by_ptype (
`ptype` VARCHAR( 255 ) NOT NULL ,
`call` VARCHAR( 255 ) NOT NULL ,
`title` VARCHAR( 255 ) NOT NULL ,
`pcircs` INT NOT NULL ,
`have` VARCHAR( 255 ) NULL ,
`last_pcirc` DATE NULL
)"; 
    $results = HandleQuery($q10);
    $success .= $results[success];
    $fail .= $results[fail];
  } //end else
} //end if no initial errors, proceed with creation

print "$success\n\n$fail\n";

/* if there is an old innreach_titles_by_call array, don't overwrite those 
   entries. Otherwise, just load up the new generic file */

if (sizeof($old_itbc) > 0) {
  $generic_itbc = file ("innreach_titles_by_call.sql");
  $new_itbc = array();
  foreach ($generic_itbc as $line) {
    if (preg_match("/\( *'([^\']+)'/",$line,$m)) {  // if match ('call'... 
      $call = $m[1];
      if (! in_array($call,$old_itbc)) {
	array_push($new_itbc, $line);
      } //end if call number not in old array
    } // end if found a call number
  }//end while reading new file
  if ($debug) { print_rr($new_itbc); }
  parse_mysql_dump ($new_itbc);
}//end if old_itbc is not empty
else { 
  parse_mysql_dump("innreach_titles_by_call.sql");
} //end if itbc exists

/* supply LC data if its not already there */
/* this will prevent overwriting local customizations */ 
if (! $extant_tables[major_lc]) {
  parse_mysql_dump("major_lc.sql");
}


/* this confirmation section was adapted from confirm_installation.php */
$tables_with_preloaded_data = array ("major_lc", "innreach_titles_by_call");
foreach ($tables_with_preloaded_data as $table) {
      $q = "SELECT count(*) FROM $table";
      $stmt = $db->query($q);
      $n = $stmt->fetch(PDO::FETCH_NUM);
      if ($n[0] > 200)
	print "<li class=good>SUCCESS: Loaded data into table $table</li>\n";
      else {
	print "<li class=bad>FAILED: Failed to load data into $table. Maybe try that by hand from the command line?</li>\n";
	$incomplete = true;
      } //end else if data is missing from table
} //end while checking for data in tables

print "<p><b>If everything went well, return to the <a href=\"index.php\">installation guide</a></b></p>.\n";

?>
