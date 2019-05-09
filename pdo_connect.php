<?php

if (! function_exists('ConnectPDO')) {
    function ConnectPDO($MySQL_Host,$MySQL_Database,$MySQL_User,$MySQL_Password,$pdo_params) { 
        $db = new PDO('mysql:host='.$MySQL_Host.';dbname='.$MySQL_Database.';charset=utf8mb4', $MySQL_User, $MySQL_Password);
        return $db;
    }
        }



try { 
    $pdo_params = array(
        PDO::MYSQL_ATTR_LOCAL_INFILE => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    if (! isset($db)) {
        $db = ConnectPDO($MySQL_Host,$MySQL_Database,$MySQL_User,$MySQL_Password,$pdo_params);
    }
} catch (PDOException $exception) {
    print ($exception->getMessage());
}

