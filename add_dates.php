<?php
/*
  Version 1.2.0 will add a pub_date field to the innreach_titles_by_call table.
  This script populates that field with data, 500 rows at a time.
  If you're upgrading from an old version, update your table structure, and then run this script as many times as necessary (i.e. until it returns no results.
 */ 
include("config.php");

try {
    $db = ConnectPDO($MySQL_Host,$MySQL_Database,$MySQL_User,$MySQL_Password);
    $query = 'SELECT `call` from innreach_titles_by_call WHERE pub_date IS NULL LIMIT 0,500';
    $stmt = $db->query($query);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print $row['call'];
        $year = GuessYear($row['call']);
        if ($year == null) { $year = '0000'; }
        AddYear($db, $row['call'], $year);
    }
} catch (PDOException $exception) {
    print ($exception->getMessage());
}

function AddYear($db, $call,$year) {
    try {
        $query = 'UPDATE innreach_titles_by_call SET `pub_date` = ? WHERE `call` = ?';
        $stmt = $db->prepare($query);
        $stmt->execute(array($year,$call));
        $affected = $stmt->rowCount();
        print "<li>$affected row(s) affected</li>";
    } catch (PDOException $exception) {
        print ('alert: ' .$exception->getException());
    }
}        


function GuessYear($call) {
    if (preg_match('/[A-Z]+ ?\d+.*\..* (\d\d\d\d)/', $call, $m)) {
        return $m[1];
    }
    else { return null; }
}

function ConnectPDO($MySQL_Host,$MySQL_Database,$MySQL_User,$MySQL_Password) { 
    $db = new PDO('mysql:host='.$MySQL_Host.';dbname='.$MySQL_Database.';charset=utf8mb4', $MySQL_User, $MySQL_Password);
    return $db;
}




?>