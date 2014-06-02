<h2>Notes on installing 1.0.2 if you've already installed 1.0.1</h2>

<ol>
<li>Try installing in a parallel directory, e.g.:
<pre>
drwxrwxr-x   3 kirwin   users       1024 Oct 28  2008 InnReachTracker-1.0.1
drwxrwxr-x   4 kirwin   users       1536 Jun  5 12:33 InnReachTracker-1.0.2
</pre>

<li>As before, the main page (index.php) will guide you through the installation and mention some things you'll need to change.</li>
<li>There's some new stuff in the config.php file, so you don't want to overwrite it with the old one, but you can copy and paste the first 10 variables from your old config file, eg:<pre>
$Local_Institution_Name = ""; // ex: Wittenberg University
$Local_Institution_Short_Name = ""; // ex: Witt
$Local_Catalog_Name= ""; // ex: EZRA
$Local_Catalog_URL = ""; // ex: ezra.wittenberg.edu
$InnReach_Catalog_URL = "olc1.ohiolink.edu";
$InnReach_Catalog_Name = "OhioLINK";

$MySQL_Host = "localhost";   /* 
$MySQL_Database = "innreach_test"; $MySQL_User = "";
$MySQL_Password = "";
</pre>
</li>
<li>In config.php, you'll need to update the $ptypes variable. It is now an associative array, so it'll be formatted like this:<pre>
$ptypes = array("students" => "Students",
                "faculty" => "Faculty",
		"staff" => "Staff",
		"sce" => "School of Community Ed.",
		"hs" => "High School Scholars");
</pre>
<li>You'll need to run create_mysql_tables.php again (when prompted by the installation guide). It won't overwrite your old data, but it will set up some a new table that wasn't in use on the earlier versions.</li>
</ol>
