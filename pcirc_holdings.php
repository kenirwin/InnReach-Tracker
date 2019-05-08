<head><title>Add Local Holdings Information</title>
<link href="pcirc_style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>
<script type="text/javascript" src="highlight.js"></script>
</head>
<h2>Add Local Holdings Information</h2>
<p><a href="index.php">Return to View Top InnReach Requests</a></p>
<?php

  error_reporting(E_ALL);
  ini_set("display_errors", true);

include ("config.php");
include ("pdo_connect.php");

if ($_REQUEST['submit_button']) {
  foreach ($_REQUEST['holdings'] as $call=>$status) {
    if (strlen($status) > 0) { //if new info
      $realcall = $_REQUEST['calls'][$call];
      $q = "UPDATE `innreach_titles_by_call` SET `have`=? WHERE `call` = ?";
      $params = array($status,$realcall);
      $stmt = $db->prepare($q);
      $ok = $stmt->execute($params);
      $params_print = print_r($params, true);
      if ($ok) { print "<span class=good>DONE: $q;$params_print</span><br>\n"; }
      else { print "<span class=bad>FAILED: $q;$params_print</span><br>\n"; }

      $q2 = "UPDATE `innreach_stats_by_ptype` SET `have`=? WHERE `call` = ?";
      $stmt = $db->prepare($q2);
      $ok = $stmt->execute($params);      
      if ($ok) { print "<span class=good>DONE: $q2; $params_print</span><br>\n"; }
      else { 
          print "<span class=bad>FAILED: $q; $params_print</span><br>\n"; 
      }
    } // end if new info
  } //end foreach submission
} // end if submission

$q = "SELECT `call`,title FROM `innreach_stats_by_ptype` WHERE ((`have` is null) or (`have` = '')) and ((`title` is not null) and (`title` != '')) and `ptype` = 'all'"; 

$stmt = $db->query($q);

print '<form action="'.$_SERVER['PHP_SELF'].'" method=POST>'.PHP_EOL;
print "<table>\n";
print "<tr><th>Call #</th> <th>Title</th> <th>Have? (Y/N/date of alt ed./ebook/etc)</th></tr>\n";
while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

