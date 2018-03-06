<?php

try { 
    $db = ConnectPDO($MySQL_Host,$MySQL_Database,$MySQL_User,$MySQL_Password);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
}

function ConnectPDO($MySQL_Host,$MySQL_Database,$MySQL_User,$MySQL_Password) { 
    $db = new PDO('mysql:host='.$MySQL_Host.';dbname='.$MySQL_Database.';charset=utf8mb4', $MySQL_User, $MySQL_Password);
    return $db;
}