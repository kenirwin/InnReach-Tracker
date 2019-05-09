<html>
<head>
<title>Matrix of Recent PCIRCs and Recent Publications</title>
<?php
include("config.php");
include("pdo_connect.php"); // defines $db
?>
<link href="pcirc_style.css" rel="stylesheet" type="text/css">
<style>
table, tr, th, td { border-collapse: collapse; border: 1px solid black;}
li { list-style: none; margin-bottom: .75em; width: 15em}
td { font-size: 9pt; font-family: Calibri, Arial Narrow, sans-serif; vertical-align: top;} 
caption { text-align: left } 
table a { color: black; text-decoration: none } 
a:hover { text-decoration: underline; color: blue;}
</style>
</head>
<body>
<h1>Matrix of Recent PCIRCs and Recent Publications</h1>
<p><a href="index.php">Return to View Top InnReach Requests</a></p>
<?php
$query = "SELECT innreach_titles_by_call.title,innreach_stats_by_ptype.call,pcircs,year(last_pcirc) as pcirc_year,pub_date, innreach_titles_by_call.have as have FROM `innreach_titles_by_call`,`innreach_stats_by_ptype` WHERE pcircs >= ? AND innreach_titles_by_call.call = innreach_stats_by_ptype.call AND ptype='all' and innreach_titles_by_call.have NOT LIKE '%Y%' order by pcircs DESC";

$grid = array();
$pcirc_years = array();
$pub_years = array();

$stmt = $db->prepare($query);
$stmt->execute(array(5)); //minimum pcircs
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $call = $row['call'];
    $pcirc_year = $row['pcirc_year'];
    $pub_date = $row['pub_date'];
    $grid[$pcirc_year][$pub_date][$call]['title'] = $row['title'];
    $grid[$pcirc_year][$pub_date][$call]['pcircs'] = $row['pcircs'];
    $grid[$pcirc_year][$pub_date][$call]['have'] = $row['have'];
    if (! in_array($pcirc_year,$pcirc_years)) { array_push($pcirc_years,$pcirc_year); }
    if (! in_array($pub_date,$pub_years)) { 
        if ($pub_date != '0000') { 
                array_push($pub_years,$pub_date); 
            }
    }
}
$pub_max = max(array_values($pub_years));
$pub_min = min($pub_years);
$pcirc_max = max(array_values($pcirc_years));
$pcirc_min = min($pcirc_years);
//print "$pcirc_min - $pcirc_max";

$header = '<tr>'.PHP_EOL.'<th></th>';
for ($j = $pcirc_max; $j>=$pcirc_min; $j--) {
    $header .= '<th>'.$j.'</th>';
}
$header .= '</TR>';

for ($i = $pub_max; $i>=$pub_min; $i--) {
    $rows .= '<tr><th>'.$i.'</th>';
    for ($j = $pcirc_max; $j>=$pcirc_min; $j--) {
        $rows .= '<td>';
        foreach ($grid[$j][$i] as $call=>$arr ) {
            $title = $arr['title'];
            if (preg_match("/(.*)\/.*/",$title,$m)) {
                $title = $m[1];
            }
            $circs = $arr['pcircs'];
            $have = $arr['have'];
            $rows .='<li><a target="innreach" href="http://'.$InnReach_Catalog_URL.'/search/t?SEARCH='.$title.'">'.$title.' ('.$circs.')</li>';
        }
        $rows .= '</td>';
    }
    $rows .= '</tr>'.PHP_EOL;

}

print '<table>'.PHP_EOL;
print '<caption>Publication Dates Down, Circulation Dates Across</caption>'.PHP_EOL; 
print $header;
print $rows;
print '</table>'.PHP_EOL;
?>
<hr>
<?php include ("license.php"); ?>

<p>For more information, please see the <strong><a href="readme.php">Documentation</a></strong>.</p>
