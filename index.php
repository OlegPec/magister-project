<?php

// Показывать всю информацию, по умолчанию INFO_ALL
phpinfo();

//$connectstr_dbhost = '';
//$connectstr_dbname = '';
//$connectstr_dbusername = '';
//$connectstr_dbpassword = '';

//foreach ($_SERVER as $key => $value) {
//    if (strpos($key, "MYSQLCONNSTR_localdb") !== 0) {
//        continue;
//    }
    
//    $connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
//    $connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
//    $connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
//    $connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
//}

//$link = mysqli_connect($connectstr_dbhost, $connectstr_dbusername, $connectstr_dbpassword,$connectstr_dbname);

//if (!$link) {
//    echo "Error: Unable to connect to MySQL." . PHP_EOL;
//    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
//    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
//    exit;
//}

//echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
//echo "Host information: " . mysqli_get_host_info($link) . PHP_EOL;

//mysqli_close($link);

 //$db = "(DESCRIPTION =
       // (ADDRESS = (PROTOCOL = TCP)(HOST = oraclebi.avalon.ru)(PORT = 1521))
       // (CONNECT_DATA =
       //   (SID=ORCL12)
       // )
     // )" ;
//$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = oraclemonitor.cflmxpmmc7tu.us-east-2.rds.amazonaws.com)(PORT = 1521)))(CONNECT_DATA=(SID = ORCLMON)))" ;

    //if($c = OCILogon("OLEPEC", "olepec", $db))
    //{
        //echo "Successfully connected to Oracle.\n";
        //OCILogoff($c);
    //}
    //else
    //{
        //$err = OCIError();
        //var_dump($err);
        //echo "Connection failed." . $err[text];
    //}
//PutEnv("ORACLE_SID=ORCLMON"); 
//PutEnv("ORACLE_HOME=D:\home\site\instantclient_12_2"); 
//PutEnv("TNS_ADMIN=D:\home\site\instantclient_12_1"); 

//PutEnv("TNS_ADMIN=D:\home\site\instantclient_12_1");
//PutEnv("AL32UTF8");
 /*$db = '(DESCRIPTION =
            (ADDRESS = (PROTOCOL = TCP)
            (HOST = oraclebi.avalon.ru)(PORT = 1521))
            (CONNECT_DATA =
                (SID=orcl12)
            )
        )';*/
/*$conn = oci_connect('orclmon', 'orcl10505560', $db);
if (!$conn) {
    $e = oci_error();
    //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    var_dump($e);
}else{
    echo "conn";
}

try {

            $server         = "oraclemonitor.cflmxpmmc7tu.us-east-2.rds.amazonaws.com";
            $db_username    = "orclmon";
            $db_password    = "orcl10505560";
            $service_name   = "ORCLMON";
            $sid            = "ORCLMON";
            $port           = 1521;
            $dbtns          = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = $server)(PORT = $port)) (CONNECT_DATA = (SID = $sid)))";

            //$this->dbh = new PDO("mysql:host=".$server.";dbname=".dbname, $db_username, $db_password);

            $this->dbh = new PDO("oci:dbname=" . $dbtns . ";charset=utf8", $db_username, $db_password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));

        } catch (PDOException $e) {
            echo $e->getMessage();
        }*/
        
/*$db = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=oraclebi.avalon.ru)(PORT=1521))(CONNECT_DATA=(SID=ORCL12)))";
$conn = oci_connect('OLEPEC', 'olepec', $db);
if (!$conn) {
    $e = oci_error();
    //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    var_dump($e);
}else{
    echo "conn";
}
*/

?>