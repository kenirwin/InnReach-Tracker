<?
/*
$array = array ("Monkeys", "Fish", "Freedom");
$more = array ("IPL" => "http://www.ipl.org");

print (TabNavBar ($array, $more));
*/

function TabNavBar ($groups=array(), $added_links = array(), $sort = "", $default_tab = "" ) {
  /* 
     $groups is a one-dimensional array, e.g. array ("Monkeys", "Fish");
     $added_links is an assoc array (name => url): ("IPL"=> "http://ipl.org");
     $sort orders the entries; default is unsorted; valid value: alpha
     NOTE: added_links are never sorted into main $groups array
     
     any of the args may be an empty variable.
     if no groups are listed, only the added links will display
     if no added links are listed, only the groups array will display
  */

  global $QUERY_STRING;
  $mytab = $_SESSION['mytab'];
  $QUERY_STRING = preg_replace ("/&*mytab=[^&]+/","",$QUERY_STRING);
  if (strlen($QUERY_STRING) > 0) { $QUERY_STRING .= "&"; }

  $tabs_body_items_array = array();
  $tabs_body_links_array = array();

  $tabs_body .= "<ul id=\"tabnav\">\n";

  if ( $sort == "alpha") {
    if (is_array($groups)) { asort($groups); }
  }

  foreach ($groups as $group) {
    $display_group = $group;
    $group = preg_replace("/[^a-zA-Z]*/", "", $group);
    $tabs_body .="<li class=\"$group\"><a href=\"?$QUERY_STRING"."mytab=$group\">$display_group</a></li>\n";
    array_push ($tabs_body_items_array, "#$group li.$group");
    array_push ($tabs_body_items_array, "#$group li.$group a:link");
    array_push ($tabs_body_items_array, "#$group li.$group a:visited");
    array_push ($tabs_body_links_array, "#$group li.recipes a:link");
  }
  foreach ($added_links as $name => $url) {
    $tabs_body .= "<li><a href=\"$url\">$name</a></li>\n";
  }
  $tabs_body .= "</ul>";
  
  $tabs_body_items = join (", ", $tabs_body_items_array);
  $tabs_body_items_css = "border-bottom: 1px solid #fff; color: 000000; background-color: #FFFFFF;";
  
  $tabs_body_links = join (", ", $tabs_body_links_array);
  $tabs_body_links_css = "color: #000000; background-color: #FFFFFF";


  if (! $mytab) { $tabdiv_id = preg_replace ("/[^A-Za-z0-9]/", "", $default_tab); }
  else { $tabdiv_id = $mytab; }

  $return .= "<link href=\"tabs.css\" rel=\"stylesheet\" type=\"text/css\">\n";
  $return .= "<style><!--\n";
  $return .= "$tabs_body_items { $tabs_body_items_css }\n";
  $return .= "$tabs_body_links { $tabs_body_links_css }\n";
  $return .= "--></style>\n";

  $return .= "<div id=\"$tabdiv_id\">\n";
  $return .= "$tabs_body\n";
  $return .= "</div><!-- end tab div-->\n";
  $return .= "<div class=\"nav\"><ul class=\"nav_list\">$nav</a></li></div>\n";
  $return .= "<div class=\"main\">$main</a></div>\n";

  return ($return);
} //end function TabNavBar
?>
