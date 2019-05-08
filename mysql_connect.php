<?php
/* connect to MySQL database */


$db = mysql_connect($MySQL_Host,$MySQL_User,$MySQL_Password);
mysql_select_db($MySQL_Database,$db) || die ("Unable to select MySQL database $MySQL_Database. Make sure it exists and is defined in config.php");

?>
