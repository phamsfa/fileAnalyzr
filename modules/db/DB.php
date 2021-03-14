<?php
namespace vznrw\db;
/**
 * Description of DB
 *
 * @author hamsfa
 */
class DB {
    private $conf;
    private $connection;
    private $dbSocket;
    
    public function __construct($conf,$con,$DBNAME) {
    
        $this->conf = $conf;
        $this->connection = $con;
        $this->dbSocket = $this->connection->getMySQLConnection($DBNAME);
    }
    
    public function select($sql) {
        $return = array();
        $result = $this->dbSocket->query($sql);
        if($this->dbSocket->error) echo $sql.' ## '.$this->dbSocket->error;
        else {
            while($obj = $result->fetch_object()) {
                $return[] = $obj;
            }
            $result->close();
            return $return;
        }
    }

    public function prepare($sql, $params=null){
        if($stmt = $this->dbSocket->prepare($sql)) {
            if($params){
                $a_params = array();
                $a_params[] = & $params['types'];
                $arrlength = count($params['vars']);
                for($i = 0; $i < $arrlength; $i++){
                    $a_params[] = & $params['vars'][$i];
                }
                call_user_func_array(array($stmt, 'bind_param'), $a_params);
                //$stmt->bind_param($params['types'], $params['vars']);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            //falls $sql ein select statement ist
            if($result){
                return $result->fetch_all(MYSQLI_ASSOC);
            }
            //return $stmt->get_result()->fetch_assoc();
        }
        else{
            return die("SQL statement invalid\n"); 
        }

    }
    
    public function insert($sql) {
        $result = $this->dbSocket->query($sql);
        if($this->dbSocket->error) echo $sql.' ## '.$this->dbSocket->error;
        return mysqli_insert_id($this->dbSocket);
    }

    public function escape($string) {
        //echo $string;
        return $this->dbSocket->real_escape_string($string);
    }
}
