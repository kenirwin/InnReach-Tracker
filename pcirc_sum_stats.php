<?php
ERROR_REPORTING(0);
include("config.php");
include("pdo_connect.php");
include_once ("header.php");

$q = "SELECT * FROM innreach_by_call order by month_ending ASC";
$stmt = $db->query($q);
while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
  extract($myrow);
  $last_pcirc[$call] = $month_ending;
}

$q = "SELECT * FROM innreach_titles_by_call";
$stmt = $db->query($q);
while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
  extract($myrow);
  $titles[$call] = $title;
  $holdings[$call] = $have;
}

$q = "SELECT * FROM innreach_by_call";
$stmt = $db->query($q);
while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
if ($db->query($q2)) { 
    print "<li class=good>\nDELETED OLD STATS-BY-PTYPE</li>\n"; 
}



foreach ($totals as $call => $total) {
  /*
  // this part will probably be deprecated in 1.0.2:
  $q = "INSERT INTO innreach_stats VALUES ('".addslashes($call)."','".addslashes($titles[$call])."','$totals[$call]','".addslashes($holdings[$call])."','".addslashes($last_pcirc[$call])."')";
  if (mysql_query($q)) { $success++;}
    else {$failure++; print "<li class=bad>FAILED: $q\n</a>";}
  */

  // do it again under ptype=all for innreach_stats_by_ptype:
  $q = "INSERT INTO innreach_stats_by_ptype VALUES ('all','".addslashes($call)."','".addslashes($titles[$call])."','$totals[$call]','".addslashes($holdings[$call])."','".addslashes($last_pcirc[$call])."')";

  if ($db->query($q)) { 
      $success++;
  }
  else {$failure++; print "<li class=bad>FAILED: $q\n</a>";}



  //then do the same for each ptype

  foreach ($ptype_totals[$call] as $ptype => $ptype_total) {
    $q = "INSERT INTO innreach_stats_by_ptype VALUES ('".addslashes($ptype)."','".addslashes($call)."','".addslashes($titles[$call])."','$ptype_total','".addslashes($holdings[$call])."','".addslashes($last_pcirc[$call])."')";
    if ($db->query($q)) { 
      $success++;
    }
    else {$failure++; print "<li class=bad>FAILED: $q\n</a>";}
  } //end foreach ptype-per-book
} // end foreach total


print "<li class=good>added ". number_format($success) ." entries to innreach_stats_by_ptype</li>\n";
if ($failure > 0) { 
    print "<li class=bad>failed to add $failure entries</a>\n"; 
}

print "<p><a href=\"index.php\">Return to main page</a></p>\n";

?>
