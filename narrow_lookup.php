<div class="inline_lookup_pane">

<? 
extract($_REQUEST);

if (preg_match("/(^[^\_]+)\_/",$location,$m)) { $location_arg = $m[1]; } ?>

<a href="javascript:toggle('<?=$lookup_pcircs;?>','<?=$location_arg;?>')"><img src="images/blank.gif" class="hide_lookup_strip"></a>

<?
include ("config.php");
include ("mysql_connect.php");
include ("array_search_recursive.php");


/*********************************************************************
 **           Function: NarrowLookup: Show Possible Titles for Book
 *********************************************************************/

$lookupstring_param = array();

$q = "SELECT * FROM `innreach_by_call` WHERE `call` = '$lookup_pcircs' ORDER BY `month_ending` DESC";
$r = mysql_query($q);

while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);

  $month = date ("M Y",strtotime($month_ending));
  $lines .= " <tr><td>$total</td> <td>$month</td></tr>\n";
  $gtotal += $total;


  if ((strtotime($month_ending) >= strtotime($date_of_first_title_data)) || (! $date_of_first_title_data)) {
      $temp_search_array = array();
      foreach ($ptypes as $ptype => $ptype_label) {
	array_push ($temp_search_array, "`$ptype` = '$myrow[$ptype]'");
      }
      $search_string = join (" and ", $temp_search_array);
      $search_string .= " and `month_ending` = '$myrow[month_ending]'";
      array_push ($lookupstring_param, $search_string);
  } //end if since we started tracking title circ
} // end while myrow

print "<div class=\"hide_lookup\"><a href=\"javascript:toggle('$call','$location_arg')\">x</a></div>\n";
print "<h4>$gtotal InnReach circs for: $call</h4>\n";
print "<table>\n <tr><th>Copies</th> <th>Month</th></tr> $lines </table>\n";

$possible_titles = array();

foreach ($lookupstring_param as $search) {
  $temp_a = array();
  $i++;
  $q = "SELECT `title` from `innreach_by_title` WHERE $search and `title` like '%$narrow_lookup%' $add";
  //  print "<p>$q</p>\n";

    $r = mysql_query($q);
    while ($myrow = mysql_fetch_assoc($r)) {
      extract($myrow);
      array_push ($temp_a, $title);
    }
    if (sizeof($possible_titles) > 0) { 
      //      print ("intersecting: ". sizeof($possible_titles) ." AND ". sizeof($temp_a) ." = ");
      $possible_titles = array_intersect ($possible_titles, $temp_a);
      //print (sizeof($possible_titles)."<br>\n");
    }
    else { $possible_titles = $temp_a; }

  } //end foreach search


global $check_innreach_icon, $min;

$search_results = $possible_titles;

if (sizeof($search_results) > 0) {
  foreach ($search_results as $title) {
    if (preg_match("/(.+)\/(.+)/", $title, $m)) 
      $title_url = $m[1];
    else $title_url = $title;
    $title_url = str_replace ("&", "and", $title_url);
    $title_url = preg_replace ("/[^a-zA-Z0-9]+/","+", $title_url);
    $title_url = "http://$InnReach_Catalog_URL/search/t?SEARCH=$title_url";
    if ($location_arg == "bulk") { // for bulk titles, add title to bulk form
      $slash_title=addslashes($title);
      $form = "<input type=button onClick=\"document.getElementById('bulk_input_$lookup_pcircs').value = '$slash_title';\" value=\"Add Bulk Title\">";
    }
    else { // for solo titles, submit page
      $form = '<form action="index.php">
 <input name="min" value="'.$min.'" type="hidden">
 <input name="post_class" value="'.substr($call,0,1).'" type="hidden">
 <input name="call" value="'.$call.'" type="hidden">
 <input name="title" value="'.$title.'" type="hidden">
 <input name="add_title" value="Add Title" type="submit"> </form>';
    } //end else for solo titles, submit
    $title_lines .= "<tr><td><a href=\"$title_url\" target=\"ohiolink\">$check_innreach_icon</a></td><td>$title</td><td>$form</td></tr>";
  } //end foreach results
} //end if results

if ($title_lines) {
  // this next line is the text box for narrowing the lookup
  // currently deactivated (have to figure out how to reload the div
  // without reloading the page
  // print "<div><input type=text id=\"test_display\"></div>";
  print "<h2>Possible matches for this circ history:</h2>\n";
  print "<table>";
  print ($title_lines);
  print "</table>\n";
}
else { print "<h2>No suggested title matches based on circ history</h2>\n"; }

?>
</div><!-- inline lookup pane -->
