<?
ERROR_REPORTING(0);
include("config.php");
include("mysql_connect.php");
include_once ("header.php");

$q = "SELECT * FROM innreach_by_call order by month_ending ASC";
$r = mysql_query($q);
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
  $last_pcirc[$call] = $month_ending;
}

$q = "SELECT * FROM innreach_titles_by_call";
$r = mysql_query($q);
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
  $titles[$call] = $title;
  $holdings[$call] = $have;
}

$q = "SELECT * FROM innreach_by_call";
$r = mysql_query($q);
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
  $totals[$call] += $total;
  foreach ($ptypes as $ptype => $ptype_label) {
    // $ptype is a ptype; ${$ptype} is a stat-number from the query
    if ($$ptype > 0) {
      $ptype_totals[$call][$ptype] += ${$ptype};
    }
  } //end get stats foreach ptype
} //end while looking at innreach_by_call stats

$failure = 0;
$q2 = "DELETE FROM innreach_stats_by_ptype";
if (mysql_query($q2)) { print "<li class=good>\nDELETED OLD STATS-BY-PTYPE</li>\n"; }



foreach ($totals as $call => $total) {
  /*
  // this part will probably be deprecated in 1.0.2:
  $q = "INSERT INTO innreach_stats VALUES ('".addslashes($call)."','".addslashes($titles[$call])."','$totals[$call]','".addslashes($holdings[$call])."','".addslashes($last_pcirc[$call])."')";
  if (mysql_query($q)) { $success++;}
    else {$failure++; print "<li class=bad>FAILED: $q\n</a>";}
  */

  // do it again under ptype=all for innreach_stats_by_ptype:
  $q = "INSERT INTO innreach_stats_by_ptype VALUES ('all','".addslashes($call)."','".addslashes($titles[$call])."','$totals[$call]','".addslashes($holdings[$call])."','".addslashes($last_pcirc[$call])."')";
  if (mysql_query($q)) { $success++;}
    else {$failure++; print "<li class=bad>FAILED: $q\n</a>";}



  //then do the same for each ptype

  foreach ($ptype_totals[$call] as $ptype => $ptype_total) {
    $q = "INSERT INTO innreach_stats_by_ptype VALUES ('".addslashes($ptype)."','".addslashes($call)."','".addslashes($titles[$call])."','$ptype_total','".addslashes($holdings[$call])."','".addslashes($last_pcirc[$call])."')";
  if (mysql_query($q)) { $success++;}
    else {$failure++; print "<li class=bad>FAILED: $q\n</a>";}
  } //end foreach ptype-per-book
} // end foreach total


print "<li class=good>added ". number_format($success) ." entries to innreach_stats_by_ptype</li>\n";
if ($failure > 0) { print "<li class=bad>failed to add $failure entries</a>\n"; }


/*
// Also do stats for title-only info
// 2009.05.21: I think this is no longer relevant and can be deleted 
// --Ken


$q = "SELECT * FROM innreach_by_title";
$r = mysql_query($q);
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
  $titles2[$title] += $total;
  $last[$title] = $month_ending;
}

$q = "DELETE FROM innreach_title_only_stats";
$failure = 0;
if (mysql_query($q)) { print "<li class=good>\nDELETED OLD STATS</li>\n"; }
foreach ($titles2 as $title => $total) {
  $q = "INSERT INTO innreach_title_only_stats VALUES ('".addslashes($title)."','$total','".addslashes($last[$title])."')";
  if (mysql_query($q)) { $success++;}
    else {$failure++; print "<li class=bad>FAILED: $q</li>\n";}
} // end foreach total

print "<li class=good>added $success entries to innreach_title_only_stats</li>\n";
if ($failure > 0) { print "<li class=bad>failed to add $failure entries</li>\n"; }

//end of commented-out section on title-only stats
*/



print "<p><a href=\"index.php\">Return to main page</a></p>\n";
?>
