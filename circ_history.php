<?php
error_reporting(E_ALL &  ~E_NOTICE);
ini_set('display_errors', 1);
include_once ("config.php");
include_once ("header.php");
include_once ("pdo_connect.php");
include_once ("scripts.php");

$q = "SELECT * FROM innreach_stats_by_ptype WHERE call = ? and ptype = 'all'";
$stmt = $db->prepare($q);
$stmt->execute(array($_REQUEST['call']));
while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
  extract($myrow);
}

print '<h2>Circ History for: '.$_REQUEST['call'].'</h2>'.PHP_EOL;
if ($title) { print '<h3>'.$title.'</h3>'.PHP_EOL; }

$q = "SELECT * FROM innreach_by_call WHERE `call` = ?";
$stmt = $db->prepare($q);
$stmt->execute(array($_REQUEST['call']));
//while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {

foreach ($ptypes as $code => $name) {
  $head .= "<th>$name</th>";
}
$head = "<tr><th>Month</th> $head <th>Total<th></tr>\n";

print (PdoResultsTable ($stmt));

?>
