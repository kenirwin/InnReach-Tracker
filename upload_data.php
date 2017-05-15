<html>
<head><title>Upload New InnReach Data</title>
<link href="pcirc_style.css" rel="stylesheet" type="text/css">

<script language="javascript">
function showTab (id, not) {
  var toShow = document.getElementById(id);
  toShow.style.display = "block";

  var not_array = not.split (";");

  for (var i in not_array) {
    var div= not_array[i];
    var toHide = document.getElementById(div);
    toHide.style.display = "none";
  } //end foreach element in the not array
} //end function showTab
</script>

</head>

<body>
<h2>Upload New InnReach Data</h2>

<p><a href="index.php">Return to View Top InnReach Requests</a></p>
<?
ERROR_REPORTING(0);
include ("config.php");
include ("mysql_connect.php");
include ("scripts.php");


if ($allow_uploads == true) { 
  if ($_REQUEST[upload_button]) { 
    HandleUpload();
  }
  
  if (fopen("$Upload_Folder/temp","x")) { // try to make a file to test write permissions
    fclose ("$Upload_Folder/temp");
    unlink ("$Upload_Folder/temp");
    ShowUploadForm();
  } // end if web server has write permissions

  else { // if web server doesn't have write permissions
    print "<div class=warning><h3>This Function is Unavailable</h3><p>The web server does not have write permissions to the <code>$Upload_Folder</code> directory, so this function is unavailable. To allow direct file uploads, talk with your system administrator about granting write permission to the <code>$Upload_Folder</code> directory. In the mean time you can still upload files through your MySQL interface (<a href=\"documentation_how_to_add_data.php\">see documentation</a>).</p>";
 
    /*
      Don't know the username for your webserver's process? 
      You can un-comment the next line and it will display in the error msg.
      This is left commented-out because it may be regarded as sensitive info.
      You should re-comment-out the line once you get the answer. 
    */
    // print "Web server username and group info: " . exec("id") ."</p>\n";

    print "</div>\n";
  } //end else if not able to write to $Upload_Folderdirectory
} //end if the allow-uploads setting is allowed in config.php

else { 
  print "<h3>This function is not enabled.</h3>";
  print "<p>To enable this function, change the \$allow_uploads setting to TRUE in config.php</p>"; 
}

function ShowUploadForm() {
  global $ptypes;
  $files = array ("call","title");
  foreach ($files as $filetype) {
    $q = "SELECT DISTINCT(month_ending) FROM innreach_by_$filetype ORDER BY month_ending DESC LIMIT 0,1";
    $r = mysql_query($q);
    $row = mysql_fetch_row($r);
    $most_recent[$filetype] = $row[0];
  }
?>
<form>
   <p>Type of file to upload:</p>
<input type=radio onClick="showTab('call','title')" name="select"><strong>Call Number</strong> (Most recent in table = Month ending: <?=$most_recent[call];?>)<br>
<input type=radio onClick="showTab('title','call')" name="select"><strong>Title</strong> (Most recent in table = Month ending: <?=$most_recent[title];?>)<br>
</form>

<?

  foreach ($ptypes as $code => $colname) { 
    $row .= "<th>$colname</th>\n";
  }
  


  
  foreach ($files as $filetype) {
    if ($filetype == "call") {
      $call_or_title_label = "Call Number";
      $call_or_title_example = "GB608.24 .P34 1985";
    }
    else { 
      $call_or_title_label = "Title";
      $call_or_title_example = "The blue holes of the Bahamas / Robert Palmer";
    }

    $row1 = "<tr><th>$call_or_title_label</th>\n$row <th>Totals</th> <th>Month Ending</th></tr>\n";
    $row2 = "<tr><td>$call_or_title_example</td> <td>2</td> <td>0</td> <td>0</td> <td>0</td> <td>0</td> <td>2</td> <td>2004-03-31</td> </tr>\n";
    
    
    $content[$filetype] = "<form action=\"$PHP_SELF\" method=\"POST\" enctype=\"multipart/form-data\">";
    $content[$filetype] .= "<input type=\"hidden\" name=\"filetype\" value=\"$filetype\">";
    
    $content[$filetype] .= '
<table width="350" border="0" cellpadding="1" cellspacing="1" class="box">
<tr>
<td width="246">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
<input name="userfile" type="file" id="userfile">
</td>
<td width="80"><input name="upload_button" type="submit" class="box" value=" Upload "></td>
</tr>
</table>
</form>';
$content[$filetype] .= "<div class=deckle><h3>Example of data structure:</h3> <p>(<strong>Note:</strong> files should be in tab-delimited format and EXCLUDE the column headers)</p>
<table class=grid> $row1 $row2 </table></div>\n";

print "<div id=\"$filetype\" style=\"display: none\">$content[$filetype]</div>";


} //end foreach file

  
} //end function ShowUploadForm



function HandleUpload () {
    global $Upload_Folder;
  if(isset($_POST['upload_button']) && $_FILES['userfile']['size'] >  0)
    {
      $fileName = $_FILES['userfile']['name'];
      $tmpName  = $_FILES['userfile']['tmp_name'];
      $fileSize = $_FILES['userfile']['size'];
      $fileType = $_FILES['userfile']['type'];

      if (! move_uploaded_file($tmpName, "$Upload_Folder/".$fileName)) { //if fail to mv
	print "<p class=warning>failed to move file: check to be sure web server has write permissions to the tmp/ directory</p>";
      }

      $path = preg_replace ("/[^\/]+$/", "", $_SERVER[SCRIPT_FILENAME]);
      
      $q = "LOAD DATA INFILE '$Upload_Folder/$fileName' INTO TABLE innreach_by_" . $_REQUEST[filetype];
      $q .=" FIELDS TERMINATED BY '\\t' LINES TERMINATED BY '\\n';";
      
      if ($debug) {print "<li>$q</li>"; }

      if (mysql_query($q)) {
	print ("<p><strong>".mysql_affected_rows() . " records imported into ". $_REQUEST[filetype] ." table from ". $fileName.".</strong></p><p>* Be sure to run the <strong><a href=\"pcirc_sum_stats.php\">Stats Update</a></strong> function once you've finished uploading data. (Note: it may take a few minutes to run!)</p><hr>");
	unlink ("$Upload_Folder/".$fileName);
      }
      else 
	die('Query Failed: ' . mysql_error() . '<br />'. $q);

    } //end if isset

  else { print "<div class=\"warning\"><h2>File is too large or too small?</h2><p>We are unable to upload this file. This could be because it is empty; more likely, the file is too large to upload over the web.</p> 

<p>Your web server is set to upload files no larger than ".ini_get(upload_max_filesize).". You can break the file into smaller segments to upload, or you can talk with your system administrator about increasing the \"upload_max_filesize\" setting in the php_ini settings on your server.</p></div>\n"; }
} //end HandleUpload


?>
</body>
</html>
