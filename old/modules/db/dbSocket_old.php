<?php
namespace vznrw\db;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class dbSocket
{
    private $fields;
    private $tbl;
    private $mysqli;
    private $joins;
    const PASSWORD = 'PASSWORD';
    
    public function __construct(\mysqli $mysqli) {
        $this->mysqli = $mysqli;
    }
    
    public function select($query) {
        
        $tbl = $this->checkTable($query['table']);
        $vals = $this->maskVals($query['values']);
        if(isset($query['join'])) {
            //echo '
            //    try join';
            //print_r($query['join']);
            $this->joins = $query['join'];
            //print_r($this->maskVals($query['join']));
            //print_r($this->joins);
        }
        $valArr = $this->group($vals); 
        
        $cols = '*';
        if(isset($query['columns'])) {
            $cols = join(', ',$query['columns']);
        }
        $sql = "
            SELECT $cols 
            FROM `$tbl` ";
        if(isset($this->joins)) {
            $sql .= "    
            ".$this->join(' AND ',$this->joins,$tbl);
        }   
        $sql .= "   
            WHERE ".join(' AND ',$valArr);
        //echo $sql;
        
        $ret = array();
        
        $result = $this->mysqli->query($sql);
        if($this->mysqli->error) $this->alert();
        
        while($obj = $result->fetch_object()) {
            $ret[] = $obj;
        }
        $result->close();
        return $ret;
       
    }
    
    private function join($concat,$joins,$tbl) {
        $joinArr = array();
        foreach($joins as $joinTbl => $join) {
             $ret = "join $joinTbl on (";
             $onArr = array();
             foreach($join as $arg) {
                 if(!is_array($arg)) {
                    $onArr[] = " $joinTbl.$arg = $tbl.$arg ";
                 } else {
                     $argA = $arg[0];
                     $argB = $arg[1];
                     $onArr[] = " $joinTbl.$argA = $tbl.$argB ";
                 }
             }
             
             $joinArr[] = $ret.join(' AND ',$onArr).')';
        }
        return  join(' AND ',$joinArr);
    }
    
    private function alert() {
         echo "<hr>".$this->mysqli->error;
    }
    
    public function insert($query) {
        $tbl = $this->checkTable($query['table']);
        $vals = $this->maskVals($query['values']);
        $cols = $this->maskCols($query['values']);
        
        
        $this->checkTable($tbl);
        
        $sql = "
        INSERT INTO `$tbl`
            (".join(', ',$cols).") 
        VALUES 
            (".join(', ',$vals).")";
        
        $this->mysqli->query($sql) or die($sql . " // " . mysql_error());
        
        return mysql_insert_id();
    }
    
    public function update($query) {
        $tbl = $this->checkTable($query['table']);
        $vals = $this->maskVals($query['values']);
        $cols = $this->maskCols($query['values']);
        $varArr = $this->pair($vals);
        
        $conds = $this->maskVals($query['cond']);
        $candsArr = $this->group($conds);
        
        $this->checkTable($tbl);
        
        $sql = "
            UPDATE $tbl SET 
                ".join('
               ,',$varArr)." 
            WHERE 
               ".join(' and ',$candsArr);
        #echo $sql;
        $this->mysqli->query( $sql ) or die($sql.' // '.mysql_error());
    }
    
    private function group($data) {
        $retArr = array();
        foreach($data as $col => $val) {
            if(!is_array($val)) {
                $val = $val;
            } else {
                $flag = $val[1];
                switch($flag) {
                    case self::PASSWORD:
                        $val = $this->password($val[0]);
                        break;
                    default:
                        break;
                }
            }
            $retArr[] = $this->tbl.".`$col` = $val";
        }
        return $retArr;
    }
    
    private function password($val) {
        return "PASSWORD('$val')";
    }
    
    private function pair($vals) {
        $retArr = array();
        foreach($vals as $col => $val) {
           $retArr[] = "`$col` = ".$val;
        }
        return $retArr;
    }
    
    private function checkTable($tbl) {
       if($tbl != $this->tbl) {
            $this->tbl = $tbl;
            $this->getFieldTypes();
        }
        return $tbl;
    }
    
   private function getFieldTypes(){
        $this->fields = array();
        $sql = "describe $this->tbl";
        $result = $this->mysqli->query($sql);
        //print_r($result);
         while($obj = $result->fetch_object()) {
             $this->fields[$obj->Field] = substr($obj->Type,0,3);
         };
        $result->close();
        //print_r( $this->fields);
        return  $this->fields;
    }
    
    private function maskCols($data) {
        $cols = array();
        foreach($data as $key => $type) {
            if($key != 'inputFilter') $cols[] = '`'.$key.'`';
        }
        return $cols;
    }
    private function maskVals($data) {
        $vals = array();
        
        foreach($data as $key => $val) {
            $flag = false;
            if(is_array($val)) {
                $flag = $val[1];
                $val = $val[0];
            }
            $val = $this->mysqli->real_escape_string($val);
            if(isset($this->fields[$key])) {
                $type = $this->fields[$key];

                if(isset($data[$key])) {
                    $passval = $data[$key];
                } else {
                    $passval = '';
                }
                if(($type == 'var' || $type == 'lon') && $flag === false) {
                    $passval = '"'.$passval.'"';
                } else if($passval == '' || $passval == false || $passval == null) {
                        $passval = 0;
                }
                $vals[$key] = $passval;
            }
        }
        return $vals;
    }
}
?>
