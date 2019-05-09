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
<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
define ('FIELD_SEP',"\t");
define ('LINE_SEP',"\n");
include ('config.php');
include ('pdo_connect.php');


if ($allow_uploads == true) { 
    if (isset($_POST['upload_button'])) {
        HandleUpload();
    }

    if ($test = fopen(UPLOAD_FOLDER."/temp","x")) { // try to make a file to test write permissions
        fclose ($test);
        unlink (UPLOAD_FOLDER."/temp");
        DisplayForm();
    }
    
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



function DisplayForm () {
    global $ptypes, $db;
  $files = array ("call","title");
  foreach ($files as $filetype) {
    $q = "SELECT DISTINCT(month_ending) FROM innreach_by_$filetype ORDER BY month_ending DESC LIMIT 0,1";
    $stmt = $db->query($q);
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $most_recent[$filetype] = $row[0];
  }
?>
<form>
   <p>Type of file to upload:</p>
<label><input type=radio onClick="showTab('call','title')" name="select"><strong>Call Number</strong> (Most recent in table = Month ending: <?php echo $most_recent['call'];?>)</label><br>
<label>
<input type=radio onClick="showTab('title','call')" name="select"><strong>Title</strong> (Most recent in table = Month ending: <?php echo $most_recent['title'];?>)</label><br>
</form>

<?php

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
  
}
 
function HandleUpload () {              
    if ($_FILES['userfile']['name'] == '' && $_FILES['userfile']['size'] == 0) { 
        die('<p>File not uploaded</p>');
    }
    $fileName = $_FILES['userfile']['name'];
    $tmpName  = $_FILES['userfile']['tmp_name'];
    $fileSize = $_FILES['userfile']['size'];
    $fileType = $_FILES['userfile']['type'];
    $fileLocation = UPLOAD_FOLDER.'/'.$fileName;
    
    if (! move_uploaded_file($tmpName, $fileLocation)) { //if fail to mv
        print "<p class=warning>failed to move file: check to be sure web server has write permissions to the tmp/ directory</p>";
        
    }
    try 
    {
        $pdo = new PDO(
            "mysql:host=".MYSQL_HOST.";dbname=".MYSQL_DATABASE,
            MYSQL_USER,
            MYSQL_PASSWORD,
            array
            (
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );
    } 

    catch (PDOException $e) 
    {
        die("database connection failed: ".$e->getMessage());
    }
    
    try {
        $table = 'innreach_by_' . $_REQUEST['filetype'];
        $affectedRows = $pdo->exec
                      (
                          "LOAD DATA LOCAL INFILE "
                          .$pdo->quote($fileLocation)
                          ." INTO TABLE `".$table."` FIELDS TERMINATED BY "
                          .$pdo->quote(FIELD_SEP)
                          ."LINES TERMINATED BY "
                          .$pdo->quote(LINE_SEP)
                      );

        print ("<p><strong>". $affectedRows . " records imported into ". $_REQUEST['filetype'] ." table from ". $fileName.".</strong></p><p>* Be sure to run the <strong><a href=\"pcirc_sum_stats.php\">Stats Update</a></strong> function once you've finished uploading data. (Note: it may take a few minutes to run!)</p><hr>");
        unlink ($fileLocation);

    } catch (Exception $e) {
        print '<p class="warning">Query Failed: ' . $e->getMessage() . '</p>'.PHP_EOL;
    }
}
?>