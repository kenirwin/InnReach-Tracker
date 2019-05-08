<?php
include_once ("config.php");
include_once ("header.php");
include_once ("mysql_connect.php");
include_once ("scripts.php");

$q = "SELECT * FROM innreach_stats_by_ptype WHERE call = '$_REQUEST[call]' and ptype = 'all'";
$r = mysql_query($q);
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
}

print "<h2>Circ History for: $_REQUEST[call]</h2>\n";
if ($title) { print "<h3>$title</h3>\n"; }

$q = "SELECT * FROM innreach_by_call WHERE call = '$_REQUEST[call]'";
$r = mysql_query($q);

foreach ($ptypes as $code => $name) {
  $head .= "<th>$name</th>";
}
$head = "<tr><th>Month</th> $head <th>Total<th></tr>\n";

print (MysqlResultsTable ($r));

?>
