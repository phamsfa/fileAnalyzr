<?php

namespace vznrw\db;
/*
 * handle connection details for the databases
 * uses credentials from conf object
 * 
 */

class Connection
{
    private $con;
    private $conf;
    public function __construct($conf) {
        $this->conf = $conf;
    }
    
    public function getMySQLConnection($DBNAME) 
    {
        $mysqlDetails = $this->conf->get($DBNAME);

        $mysqli = new \mysqli(
                $mysqlDetails->dbURL, 
                $mysqlDetails->dbUName, 
                $mysqlDetails->dbPWord, 
                $mysqlDetails->dbName);
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        
        return $mysqli;
        /*
        $conn = @ mysql_connect(
                $mysqlDetails->dbURL,
                $mysqlDetails->dbUName,
                $mysqlDetails->dbPWord) or die (
                " Error while connecting to db-server: ");
        $db = mysql_select_db($mysqlDetails->dbName,$conn)
            or die ("Error while connecting to database");
        return $db;
         * 
         */
        //return $con;
    }
}
?>
