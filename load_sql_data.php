<?php
include ("config.php");
include ("mysql_connect.php");

parse_mysql_dump("innreach_titles_by_call.sql");

function parse_mysql_dump($filename) {
  //from http://us3.php.net/mysql_query
  $handle = @fopen($filename, "r");
  $query = "";
  while(!feof($handle)) {
    $sql_line = fgets($handle);
    if (trim($sql_line) != "" && strpos($sql_line, "--") === false) {
      $query .= $sql_line;
      if (preg_match("/;[\040]*\$/", $sql_line)) {
	$result = mysql_query($query) or die(mysql_error());
	$query = "";
      } //end if preg_match
    } //end if trimmed
  } //end while lines in file
} //end function parse_mysql_dump

?>
