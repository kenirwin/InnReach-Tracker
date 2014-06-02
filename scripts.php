<?

include ("tabnav.php");
include ("sortLC.php");

/*
List of functions:
function HandleCookies
function PrintPageTop 
function OptionsMenu
function RecencyOptions
function BulkSubmit 
function AddTitle
function ShowBooksByClass
function ShowAllKnownTitles
function ShowBooksByQuery
function MysqlResultsTable ($mysql_results) 
function ThisFolder
function print_rr
*/

/************************************************************************
 **                   Get Coverage Dates to Display
 ************************************************************************/

$q = "SELECT COUNT(DISTINCT(`month_ending`)) FROM `innreach_by_call`";
$r = mysql_query($q);
$myrow = mysql_fetch_row($r);
$total_months = $myrow[0];


$q ="SELECT  DISTINCT ( `month_ending` ) FROM  `innreach_by_call` WHERE month_ending != '0000-00-00' ORDER  BY month_ending";
$r = mysql_query($q);
$i = 0;
while ($myrow = mysql_fetch_row($r)) { 
  if ($i == 0) {
    $start = date("M Y", strtotime($myrow[0]));
  }
  if ($i == (mysql_num_rows($r)-1)) {
    $end = date("M Y", strtotime($myrow[0]));
  }
  $i++;
} // end while checking date

//check to see if pcirc_stats is up-to-date
$q = "SELECT  DISTINCT ( `last_pcirc` ) FROM  `innreach_stats_by_ptype`  ORDER  BY `last_pcirc` DESC  LIMIT 0 , 1";
$r = mysql_query($q);
while ($myrow = mysql_fetch_row($r)) {
  $myrow[0] = date("M Y", strtotime($myrow[0]));
  if ($myrow[0] != $end)
    $warning = "<center><p class=warning>This display is not up-to-date! <a href=\"pcirc_sum_stats.php\">Click here</a> to update display to reflect recently-loaded data</p></center>\n";
}

/***************************************************************
 **         Functions: HandleCookies
 ***************************************************************/


function HandleCookies () {
  global $_REQUEST, $_SESSION;
  $session_vars = array ("min", "post_class", "ptype_limit", "most_recent_pcirc_date", "limit_to_month", "sort", "mytab", "view");

  /*
    NOTE: there's also a $_SESSION['last_query'] defined in ShowBooksByQuery()
  */

  if ($_REQUEST['delete_limit']) {
    // remove limits at user's request
    $cookie = $_REQUEST['delete_limit'];
    //FIX:    session_unregister($cookie);
  }

  if ($_REQUEST['min']) { $_SESSION['min'] = $_REQUEST['min']; }
  elseif (! $_SESSION['min']) { $_SESSION['min'] = 3; }

  if ($_REQUEST['post_class']) { 
    $_SESSION['post_class'] = $_REQUEST['post_class']; 
    $_SESSION['view'] = "show_class";
  }
  elseif (! $_SESSION['post_class']) { $_SESSION['post_class'] = "A"; }

  if ($_REQUEST['mytab']) { $_SESSION['mytab'] = $_REQUEST['mytab']; }
  elseif (! $_SESSION['mytab'] || $_SESSION['mytab'] = "") { $_SESSION['mytab'] = "ShowIndividualTitles"; }

  if ($_REQUEST['ptype_limit_button']) { $_SESSION['ptype_limit'] = $_REQUEST['ptype_limit']; }
  elseif (! $_SESSION['ptype_limit']) { $_SESSION['ptype_limit'] = "all"; }

  if ($_REQUEST['show_all']) { 
    $_SESSION['show_all'] = $_REQUEST['show_all'];
    $_SESSION['view'] = "show_all";
  }

  if ($_REQUEST['most_recent_pcirc_date']) { 
    $_SESSION['most_recent_pcirc_date'] = $_REQUEST['most_recent_pcirc_date'];
    $_SESSION['limit_to_month'] = "";
 } 

  if ($_REQUEST['limit_month_button']) { 
    $_SESSION['limit_to_month'] = $_REQUEST['limit_to_month']; 
    $_SESSION['most_recent_pcirc_date'] = "";
  }
  
  if ($_REQUEST['sort']) {
    $_SESSION['sort'] = $_REQUEST['sort'];
  } 
} //end HandleCookies

/***************************************************************
 **         Functions: PrintPageTop
 ***************************************************************/

function PrintPageTop () {
  global $start, $end, $warning, $ptypes, $total_months; //defined in config.php
  
  $limit_to_month = $_SESSION['limit_to_month'];
  $most_recent_pcirc_date = $_SESSION['most_recent_pcirc_date'];
  print "<h1>Top InnReach Requests: $start - $end ($total_months total months)</h1>\n";
  if ($warning) { print $warning; }
  if ($limit_to_month) { 
    $display_limit_to_month = date ("M Y", strtotime($limit_to_month));
    $limit_note = "<br>Limiting to items that circulated during month: $display_limit_to_month\n<a href=\"$PHP_SELF?delete_limit=limit_to_month\" class=\"delete_limit\">x</a>\n"; }
  elseif ($most_recent_pcirc_date) { 
    $display_most_recent_pcirc_date = date ("M Y",strtotime($most_recent_pcirc_date));
    $limit_note = "<br>Limiting to items that have circulated since $display_most_recent_pcirc_date\n<a href=\"$PHP_SELF?delete_limit=most_recent_pcirc_date\" class=\"delete_limits\">x</a>\n"; 
  }

  if ($_SESSION[ptype_limit] != "all") {
    $ptype_limit = $_SESSION[ptype_limit];
    $limit_note .= "<br>Limiting to circs by Patron Type = $ptypes[$ptype_limit]\n<a href=\"$PHP_SELF?delete_limit=ptype_limit\" class=\"delete_limits\">x</a>";
  } //end if ptype-limit is set

  $q = "SELECT subject from major_lc WHERE class = '$_SESSION[post_class]'";
  $r = mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    // $subject is now defined
  }

  if ($_SESSION[view] == "show_all") { 
    print "<h2>Showing all known titles with at least $_SESSION[min] circs $limit_note</h2>\n";
  } 
  else { //usually: if view == show_class
    print "<h2>Showing LC class: $_SESSION[post_class] ($subject) with at least $_SESSION[min] circs $limit_note</h2>";
  } //end else / if not show-all

} //end PrintPage Top



/***************************************************************
 **         Functions: OptionsMenu
 ***************************************************************/

function OptionsMenu() {
  global $PHP_SELF, $_SESSION, $_REQUEST, $debug;
  $post_class = $_SESSION['post_class'];
  $min = $_SESSION['min'];

  $pre_form = "<h3>Settings</h3>\n";
  $pre_form .= "<form name=min_submit method=get>\n"; // will be used in next section; here for layout reasons
  $pre_form .= "<input type=hidden name=post_class value=$post_class>\n";
  
  $q = "SELECT * FROM `major_lc` order by `class`";
  $r = mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $space = " ";  // "&nbsp;";
    if (strlen($class) == 1) { 
      $line = "<a href=\"$PHP_SELF?post_class=$class\" title=\"$subject\">$class</a>"; 
      if ($class==$post_class) 
	$letter_menu .= "<span class=selected_letter title=\"$subject\">$line</span>$space";
      else { $letter_menu .= "$line$space"; }
    } //end if class given 
  }
  if ($post_class == "%") { $all_selected = " class=selected_letter"; }
  else { $all_selected = ""; }
  $letter_menu = "<br><span class=\"letter_menu\">$letter_menu | <a href=\"$PHP_SELF?post_class=%\"$all_selected>All</a></span>";
  $pre_form .= $letter_menu;

  $menu.= "<select name=min onChange=\"document.min_submit.submit()\">\n";
  for ($i=1; $i<10; $i++) {
    if ($min == $i) { $selected = "SELECTED"; }
    else {$selected = ""; }
    $menu .= " <option $selected>$i</option>\n";
  }
  $menu .= "</select>\n";
  $menu .= "<input type=submit value=\"Go!\">\n";
  $extra = "<form action=\"$PHP_SELF\" method=GET>
<input type=hidden name=post_class value=\"$post_class\">
<input type=hidden name=min value=$min>
<input type=submit name=show_all value=\"Show all vols with known titles\">
</form>\n";


  $recency_options = RecencyOptions ($_SESSION[most_recent_pcirc_date]); 

  $recent_pcirc_select = "<select name=most_recent_pcirc_date>$recency_options</select>\n";

  $extra .= "<hr><form action=\"$PHP_SELF\" method=GET>
Limit to items with last InnReach circ of $recent_pcirc_select or&nbsp;more&nbsp;recent: 
<input type=hidden name=min value=$min>
<input type=submit name=show_all_recent value=\"Go!\">
</form>\n";

  // build separate recency_options list for second pulldown
  $recency_options = RecencyOptions($_SESSION[limit_to_month]);

  $extra .= "<hr><form action=\"$PHP_SELF\" method=GET>
    Limit to items circulating in one particular month:
    <select name=limit_to_month>$recency_options</select>\n
    <input type=submit name=limit_month_button value=\"One Month Only\">
    </form>\n";
  
  global $ptypes;
  $ptype_options = "<option value=\"all\">All Patron Types</option>\n";
  foreach ($ptypes as $ptype => $ptype_label) {
    if ($_SESSION['ptype_limit'] == $ptype) {
      $selected = "SELECTED";
    } //end if this limit is currently set
    else { $selected = ""; }
    $ptype_options .= "<option value=\"$ptype\" $selected>$ptype_label</option>\n";
  } //end foreach ptype
  if ($_SESSION[view] == "show_all") { 
    $optional_show_all_indicator = "<input type=hidden name=show_all value=true>";
  }
  $extra .= "<hr><form action=\"$PHP_SELF\" method=GET>
    Limit to patron type: 
    <select name=\"ptype_limit\">$ptype_options</select>
    <input type=submit name=ptype_limit_button value=\"Set Patron Type Limit\">
    $optional_show_all_indicator
    </form>\n";

  if ($debug) { 
    $extraextra .= "<form action=\"$PHP_SELF\" method=GET>
      <input type=submit name=delete_session_vars value=\"Delete Cookies\">
      </form>";
  } //end if debug 

  $menu = "$pre_form <br><span class=change_min>Change minimum circs to display: $menu</span>\n</form>$extra\n\n$extraextra";

  

  return $menu;
} // end function OptionsMenu


/*********************************************************************
 **           Function: RecencyOptions
 *********************************************************************/


function RecencyOptions ($date_to_match) {
  $q= "SELECT distinct `last_pcirc` from `innreach_stats_by_ptype` order by `last_pcirc` DESC";
  $r= mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $disp_date = date ("M Y", strtotime($last_pcirc));

    if ($last_pcirc == $date_to_match)
      $selected = "SELECTED";
    else { $selected = ""; }
    $recency_options .= "<option value=\"$last_pcirc\" $selected>$disp_date</option>\n";
  }
  $recency_options = "<option value=\"\">None Selected</option>\n$recency_options";

  return $recency_options;
} // end function RecencyOptions



/*********************************************************************
 **           Function: BulkSubmit
 *********************************************************************/

function BulkSubmit () {
  global $_REQUEST;
  extract ($_REQUEST);
  foreach ($add_title_array as $i => $title_string) { // $i is just an integer, not call#
    if ($title_string) {
      $title_string = addslashes($title_string);
      $q = "INSERT INTO `innreach_titles_by_call` VALUES ('$call[$i]', '$title_string','')";
      if (mysql_query($q)) { $successes .="<li>Title added: $title_string</li>\n"; }
      else { $errors .= "<li><b>Unable to add:</b> $title_string<br>$q</li>\n"; }

      $q = "UPDATE `innreach_stats_by_ptype` SET `title` = '$title_string' where `call` = '$call[$i]'";
      if (mysql_query($q)) { $successes .= "<li>Title added to stats: $title_string</li>\n"; }
      else { $errors .= "<li><b>Unable to add to stats:</b> $title_string<br>$q</li>\n"; }
    } // end if title_string
  } //end foreach add_title_array item

  if ($errors) { 
    $errors = stripslashes ($errors);
    print "<ol class=\"error\">$errors</ol>\n"; 
  } //end if errors
  if ($successes) { 
    $successes = stripslashes ($successes);
    print "<ol class=\"success\">$successes</ol>\n"; 
  } //end if successes
} //end function BulkSubmit


/*********************************************************************
 **           Function: AddTitle (just one)
 *********************************************************************/

function AddTitle() {
  global $_REQUEST;
  extract ($_REQUEST);

  $q = "INSERT INTO `innreach_titles_by_call` VALUES ('$call', '$title','')";
  if (mysql_query($q)) { $successes .= "<li>Title added: $title</li>\n"; }
  else { $errors .= "<li>Unable to add: $title<br>$q</li>\n"; }
  $q = "UPDATE `innreach_stats_by_ptype` SET `title` = '$title' where `call` = '$call'";
  if (mysql_query($q)) { $successes .= "<li>Title added to stats: $title</li>\n"; }
  else { $errors .= "<li>Unable to add to stats: $title<br>$q</li>\n"; }
  if ($errors) { 
    $errors = stripslashes ($errors);
    print "<ol class=\"error\">$errors</ol>\n"; 
  } //end if errors
  if ($successes) { 
    $successes = stripslashes ($successes);
    print "<ol class=\"success\">$successes</ol>\n"; 
  } //end if successes
} //end function AddTitle

/**********************************************************
 **      Function: ShowBooksByClass
 **********************************************************/

function ShowBooksByClass() {
  global $_SESSION;
  global $debug;
  $limit_to_month = $_REQUEST['limit_to_month'];
  $most_recent_pcirc_date = $_REQUEST['most_recent_pcirc_date'];
  if ($limit_to_month) { $added_query = " and `last_pcirc` like '$limit_to_month%'"; }
  elseif ($most_recent_pcirc_date) 
    $added_query = " and `last_pcirc` >= '$most_recent_pcirc_date'";
  $q = "SELECT * FROM `innreach_stats_by_ptype` WHERE `call` like '$_SESSION[post_class]%' and `pcircs` >= $_SESSION[min] $added_query and `ptype` = '$_SESSION[ptype_limit]'";
  if ($debug) { 
    print "<li>ShowBooksByClass: $q</li>\n"; 
  }
    $count = ShowBooksByQuery($q);
    return ($count);
} // end function ShowBooksByClass


/**********************************************************
 **      Function: ShowAllKnownTitles()
 **********************************************************/

function ShowAllKnownTitles($sort="circs") {
  global $check_local_icon, $check_innreach_icon, $PHP_SELF, $debug;
  global $Local_Catalog_URL, $InnReach_Catalog_URL, $ptypes;
  $min = $_SESSION['min'];

  if ($_SESSION[limit_to_month])
    $timeframe = "and `last_pcirc` = '$_SESSION[limit_to_month]'";
  elseif ($_SESSION[most_recent_pcirc_date]) 
    $timeframe = "and `last_pcirc` >= '$_SESSION[most_recent_pcirc_date]'";

  $q = "SELECT * from `innreach_stats_by_ptype` where `title` is not null and `title` !='' and `pcircs` >= $min and `ptype` = '$_SESSION[ptype_limit]' $timeframe";
  if ($debug) { print $q; }
  $r = mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $titles[$call] = $title;
    $count[$call] = $pcircs;
    $haves[$call] = $have;
  } //end while getting titles
  
  print "<table>";
  print "<tr><th><a href=\"$PHP_SELF?show_all=yes&sort=call\">Call #</a></th> <th><a href=\"$PHP_SELF?show_all=yes&sort=title\">Title</a></th> <th><a href=\"$PHP_SELF?show_all=yes&sort=circs\">Circs</a></th> <th>Have</th> <th>Lookup</th></tr>\n";
  if ($sort == "title") { 
    asort($titles); 
    foreach ($titles as $call => $title) {
      print "<tr><td>$call</td> <td>$title</td> <td>$count[$call]</td> <td>$haves[$call]</td>";
      print "<td><a href=\"http://$Local_Catalog_URL/search/c?SEARCH=$call\" target=\"local_cat\">$check_local_icon</a> <a href=\"http://$InnReach_Catalog_URL/search/c?SEARCH=$call\" target=\"innreach\">$check_innreach_icon</a></td></tr>\n";
    } // end foreach 
  } // end if sort by title
  

  
  else { 
    if ($sort == "call") { ksort($count); }
    else { arsort($count); }
    foreach ($count as $call => $total) {
      print "<tr><td>$call</td> <td>$titles[$call]</td> <td>$total</td> <td>$haves[$call]</td>";
      $short_title = $titles[$call];
      if (preg_match ("/(.+) \/ (.*)/",$short_title,$m)) {
	$short_title = $m[1];
      }
      $short_title = preg_replace("/[^a-zA-Z0-9]+/","+",$short_title); 
      print "<td><a href=\"http://$Local_Catalog_URL/search/t?SEARCH=$short_title\" target=\"local_cat\">$check_local_icon</a> <a href=\"http://$InnReach_Catalog_URL/search/t?SEARCH=$short_title\" target=\"innreach\">$check_innreach_icon</a></td></tr>\n";
    } // end foreach 
  }
  print "</table>\n";
}

  function ShowBooksByQuery($q) {
    global $google_icon, $check_innreach_icon, $have_icon, $nohave_icon, $min, $question, $debug;
    global $InnReach_Catalog_URL;
    $mytab = $_SESSION['mytab'];
    $r = mysql_query ($q);
    if ($debug) {
      print "<li>ShowBooksByQuery: $q</li>\n";
    }
    $_SESSION[last_query] = $q;
    $count = mysql_num_rows($r);
    $totals = array();
    while ($myrow=mysql_fetch_assoc($r)) {
      extract($myrow);
      $ti_known[$call] = $title;
      if ($have == "Y")
	$title = "$have_icon <span class=\"have\">$title</span>";
      elseif (($have) && ($have != "N")) 
	$title .= "$have_icon <span class=\"have\">Have $have ed.</span>";
      elseif ($have == "N") 
	$title = "$nohave_icon $title\n";
      else // if ($have == "") 
	$title = "$question $title\n";
      if (preg_match("/K?[A-Z]{1,2}[ 0-9]/", $call)) { // if LC call #
	$titles[$call] = $title;
	$holdings[$call] = $have;
	$totals[$call] = $pcircs;
      } //end if LC call number
    } // end while
  
  $sections = array ("Show Individual Titles", "Bulk Title Add");
  print (TabNavBar($sections, $added, $sort, "Show Individual Titles"));

  uksort($totals, "SortLC"); 
  foreach ($totals as $call=>$total) {
    $call_plus = preg_replace("/\s+/","+",$call);
    if ($ti_known[$call]) { $form = $titles[$call]; $bulk_form="";}
    else { // show title submission form
      $form = " <form action=\"$PHP_SELF\">\n
 <input type=hidden name=call value=\"$call\">\n
 <input type=text name=title value=\"\" size=30>\n
 <input type=submit name=add_title value=\"Add Title\">\n
 </form>\n";
      $bulk_form =  "<input type=hidden name=call[] value=\"$call\">
 <input type=text name=add_title_array[] id=\"bulk_input_$call\" value=\"\" size=30>";
    } // end else if show title submission form
    $google = preg_replace ("/ /","+",$call);
    $google = "<a href=\"http://www.google.com/search?hl=en&q=%22$google%22&btnG=Google+Search\" target=\"innreach\">$google_icon</a>\n";
    if ($total >= $_SESSION['min']) {

      //wrap total in link to more info:
      $total = "<a href=\"circ_history.php?call=$call\" target=\"pcirc_info\">$total</a>\n";
      if ($ti_known[$call]) {
	$call_number_entry = $call;
	$external_lookups = "<td></td><td></td><td></td>";
      }
      else {
	$call_number_entry = "<a href=\"javascript:toggle('$call','single')\">$call</a>";
	$external_lookups = "<td></td><td><a href=\"http://$InnReach_Catalog_URL/search/c?SEARCH=$call_plus\" target=\"innreach\">$check_innreach_icon</a></td><td>$google</td>";
      }
	$individual_form .= "<tr><td>$call_number_entry</td>$external_lookups<td><b>$total</b></td> <td>$form<div id=\"single_$call\" class=\"inline_lookup\"></div></td></tr>\n";
      if ($bulk_form) { $bulk_add .= "<tr id=\"row_$call\"><td><a href=\"javascript:toggle('$call','bulk');\">$call</a></td><td><td><a href=\"http://$InnReach_Catalog_URL/search/c?SEARCH=$call_plus\" target=\"innreach\" onFocus=\"document.getElementByID('row_$call').style.background = '#ff0000';\">$check_innreach_icon</a></td><td>$google</td><td><b>$total</b></td> <td> $bulk_form <div id=\"bulk_$call\" class=\"inline_lookup\"></div></td></tr>\n\n"; 

      }
      $printed++;
    } // end if big enough to display
  } //end foreach topcall

    $header = "<tr><th>Call #</th> <th colspan=3>Lookup</th> <th>Circs</th> <th>Title</th></tr>\n";

  if ($mytab == "ShowIndividualTitles" || $mytab == "") {
    print "<table>$header$individual_form</table></div>\n";
    if ($debug)  { print "<h2>$q</h2>\n"; }
    if ($printed < 1) { 
      $q = "SELECT count(*) FROM `innreach_stats_by_ptype` WHERE 1";
      $r = mysql_query($q);
      while ($myrow = mysql_fetch_row($r)) {
        $rows = $myrow[0];
      }
      if ($rows == 0) {
        print "<center><p class=warning>This display is not up-to-date! <a href=\"pcirc_sum_stats.php\">Click here</a> to update display to reflect recently-loaded data</p></center>\n";
      } //end if no stats in table
      else { //if stats in table
	print "<h2>No titles in this LC Class ($_SESSION[post_class]) with at least $_SESSION[min] InnReach circs</h2> <p>Use the letter and number selection table on the right hand side to choose another LC class or to display items with a lower number of circulations.</p>\n"; 
      //      print "<h2>$q</h2>\n";
      } //end else if no stats in table
      print "<p>For more information on using this program, see the <a href=\"readme.php\">documentation</a>.</p>\n";
    } // end if nothing printed
  } //end if show individual titles

  if ($bulk_add && ($mytab == "BulkTitleAdd" || $mytab == "")) {
    print "<form action=\"$PHP_SELF\" method=POST>
<h2>Bulk submission of titles</h2>
 <input type=hidden name=limit_to_month value=$limit_to_month>
 <div id=\"bulk_submit_table\">
<table>$header$bulk_add</table></div><input type=submit name=bulk_submit value=\"Submit Titles in Bulk\"></form>\n";
  } //end if bulk add

  return ($count);
  } //end function ShowBooksByQuery($q)




function MysqlResultsTable ($mysql_results) {
  while ($myrow = mysql_fetch_assoc($mysql_results)) {
    if (! ($headers))
      $headers = array_keys($myrow);
    $rows .= " <tr>\n";
    foreach ($headers as $k)
      $rows .= "  <td class=$k>$myrow[$k]</td>\n";
    $rows .= " </tr>\n";
  } // end while myrow
  $header = join("</th><th>",$headers);
  $header = "<tr><th>$header</th></tr>\n";
  $rows = "<table>$header$rows</table>\n";
  return ($rows);
} //end function MysqlResultsTable



function ThisFolder () {
  if (preg_match("/(.*\/)[^\/]+/", $_SERVER[SCRIPT_NAME], $m)) {
    $folder = $m[1];
    return $folder;
  }
} //end function ThisFolder

function print_rr ($var) {
  //print_rr: print "really readable" -- preformatted version of print_r
  print "<pre>\n";
  print_r($var);
  print "</pre>\n";
} // end function print_rr

?>
