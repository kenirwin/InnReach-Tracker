<head><title>Add Local Holdings Information</title>
<link href="pcirc_style.css" rel="stylesheet" type="text/css">
</head>
<h2>Add Local Holdings Information</h2>
<p><a href="index.php">Return to View Top InnReach Requests</a></p>
<?
   /*
  error_reporting(E_ALL);
  ini_set("display_errors", true);
   */
include ("config.php");
include ("mysql_connect.php");

if ($_REQUEST['submit_button']) {
  foreach ($_REQUEST['holdings'] as $call=>$status) {
    if (strlen($status) > 0) { //if new info
      $realcall = $_REQUEST['calls'][$call];
      $q = "UPDATE `innreach_titles_by_call` SET `have`='$status' WHERE `call` = '$realcall'";
      if (mysql_query($q)) { print "<span class=good>DONE: $q</span><br>\n"; }
      else { print "<span class=bad>FAILED: $q</span><br>\n"; }
      $q2 = "UPDATE `innreach_stats_by_ptype` SET `have`='$status' WHERE `call` = '$realcall'";
      if (mysql_query($q2)) { print "<span class=good>DONE: $q2</span><br>\n"; }
      else { print "<span class=bad>FAILED: $q</span><br>\n"; }

    } // end if new info
  } //end foreach submission
} // end if submission

$q = "SELECT `call`,title FROM `innreach_stats_by_ptype` WHERE ((`have` is null) or (`have` = '')) and ((`title` is not null) and (`title` != '')) and `ptype` = 'all'"; 

$r = mysql_query ($q);

print "<form action=\"$PHP_SELF\" method=POST>\n";
print "<table>\n";
print "<tr><th>Call #</th> <th>Title</th> <th>Have? (Y/N/date of alt ed./ebook/etc)</th></tr>\n";
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
  if (preg_match("/(.*)(\/)/", $title,$m)) 
    $link_title = $m[1];
  else $link_title = $title;
  $tempcall = preg_replace("/ /","",$call);
  $link_title = preg_replace("/[\"\'\/]/","",$link_title);
  $link_title = preg_replace("/\&/","and",$link_title);
  print "<tr><td>$call</td> <td><a href=\"http://$Local_Catalog_URL/search/t?SEARCH=$link_title\" target=local_cat>$title</a></td> <td><input type=text size=15 name=holdings[$tempcall]><input type=hidden name=calls[$tempcall] value=\"$call\"></td></tr>\n";
} //end while results
print "</table>\n";
print "<input type=submit name=submit_button value=\"Add Holdings Info\"></form>\n";
?>
<hr>
<p><a href="index.php">Return to View Top InnReach Requests</a></p>
<? include("version.php");?>
