<?php
namespace vznrw\db;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class dbSocket
{

    private $mysqli;
    private $tbl;
    private $tblAlias;
    private $fields;
    private $defaults;
    private $keys;
    private $conf;
    private $connection;

    /* OPERATORS */
    const LT = '<';
    const LE = '<=';
    const GT = '>';
    const GE = '>=';
    const UE = '!=';
    const OP = '=';
    const LIKE = 'LIKE';

    /* TYPES */
    const PASSWORD = 'PASSWORD';
    const DEFAULTVAL = ' DEFAULT ';
    const UUID = 'uuid()';
    const FORIGNTABLE = 'ForignTable';

    /*
     * 
     * SHOULD ONLY CONTAIN BASIC DATABASE FUNCTION 
     * 
     * 
     */

    /**
     * dbSocket constructor.
     * @param $conf
     * @param $con
     * @param $DBNAME
     */
    public function __construct ($con, $DBNAME)
    {

        /** @var object/self $this */
        // $this->conf = $conf;
        $this->connection = $con;
        $this->tbl = NULL;
        $this->tblAlias = NULL;
        $this->mysqli = $this->connection->getMySQLConnection($DBNAME);
    }


    /***
     * private Helper methods
     */

    private function getTable ()
    {

        /** @var string $strTable */
        $strTable = '';
        if (isset($this->tbl) &&
            !empty($this->tbl)) {

            $strTable .= "`" . $this->tbl . "`";
        }

        return $strTable;
    }

    /**
     * @return null|string
     */
    private function getTableAlias ()
    {

        /** @var string $strAlias */
        $strAlias = '';
        if (isset($this->tblAlias) &&
            !empty($this->tblAlias)) {

            $strAlias = $this->tblAlias;
        }

        if (empty($strAlias)) {

            $strAlias = $this->tbl;
        }

        return $strAlias;
    }

    /**
     * @return string
     */
    private function getTableString ()
    {

        $strTable = '';

        if (isset($this->tbl) &&
            !empty($this->tbl)) {

            $strTable .= "`" . $this->tbl . "`";
        }

        if (isset($this->tblAlias) &&
            !empty($this->tblAlias)) {

            $strTable .= " AS `" . $this->tblAlias . "`";
        }

        return $strTable;
    }

    private function setFieldTypes ()
    {

        $this->fields = array();

        /* check if table available */
        if (!isset($this->tbl) ||
            empty($this->tbl)) {

            return FALSE;
        }

        $sql = "describe " . $this->tbl;
        $result = $this->mysqli->query($sql);
        if($result) {
            while ($obj = $result->fetch_object()) {

                // store table information
                $this->fields[$obj->Field] = substr($obj->Type, 0, 3);
                $this->defaults[$obj->Field] = $obj->Default;
                $this->keys[$obj->Field] = ($obj->Key != '') ? $obj->Key : false;
            }
            $result->close();

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @param $tbl
     * @return bool
     */
    private function checkTable ($tbl)
    {

        // echo 'call checkTable()'." \r\n";

        if (is_array($tbl)) {

            $arrTable = $tbl;

            if (count($arrTable) === 1) {
                foreach ($arrTable as $t => $a) {

                    $this->tbl = (isset($t)) ? $t : NULL;
                    $this->tblAlias = (isset($a)) ? $a : NULL;
                }
            } else {

                return FALSE;
            }
        } else {

            $this->tbl = $tbl;
        }

        if (!$this->setFieldTypes()) {

            return FALSE;
        }

        return TRUE;
    }

    /**
     * @param $result (mysql result object)
     * @return array (result as array)
     */
    private function read($result) {
        $ret = array();
        while ($obj = $result->fetch_object()) {

            $ret[] = $obj;
        }
        $result->close();
        return $ret;
    }

    /* public function getDBcon() { return $this->mysqli; } */

    /**
     * @param $query
     * @param bool $verbose
     * @return bool|void
     */
    public function insert ($query, $verbose = FALSE)
    {

        if (!$this->checkTable($query['table'])) {

            return FALSE;
        }

        $arrColNames = $this->extractCols($query['values']); // MYSQL Insert
        $arrCols = $this->maskCols($arrColNames, FALSE); // MYSQL Insert

        $arrVals = $this->maskVals($query['values']); // MYSQL Insert

        $sql = "";

        $sql .= "INSERT";
        $sql .= " INTO " . $this->getTable();
        $sql .= " (" . join(', ', $arrCols) . ")";
        $sql .= " VALUES";
        $sql .= " (" . join(', ', $arrVals) . ")";

        if ($verbose) {

            echo $sql . "\r\n";
            return;
        }
        $this->mysqli->query($sql) or die("\n" . $sql . "\nERROR: " . $this->mysqli->error);

        return $this->mysqli->insert_id;
    }

    /**
     * @param $query
     * @param bool $verbose
     * @return bool
     */
    public function update ($query, $verbose = FALSE)
    {

        if (!$this->checkTable($query['table'])) {

            return FALSE;
        }

        $arrVals = $this->maskVals($query['values']); // MYSQL Update
        $pairVals = $this->pair($arrVals); // MYSQL Update

        $sql = "";

        $sql .= "UPDATE " . $this->getTableString();
        $sql .= " SET " . join(", ", $pairVals);

        $where = (isset($query['where'])) ? $query['where'] : null;
        if ($where) {

            $arrVals = $this->maskVals($where); // MYSQL Update
            print_r($arrVals);
            $arrWhere = $this->group($arrVals); // MYSQL Update

            $sql .= " WHERE (" . join(' AND ', $arrWhere) . ")";
        }

        if ($verbose) {

            echo $sql;
            return true;
        }
        $this->mysqli->query($sql) or die($sql . ' // ' . $this->mysqli->error);

        return TRUE;
    }

    public function delete ($query, $verbose = FALSE)
    {

        if (!$this->checkTable($query['table'])) {

            return FALSE;
        }

        $arrVals = $this->maskVals($query['where']); // MYSQL Delete
        $arrWhere = $this->group($arrVals, FALSE); // MYSQL Delete

        $sql = "";

        $sql .= "DELETE";
        $sql .= " FROM " . $this->getTable();
        $sql .= " WHERE (" . join(' AND ', $arrWhere) . ")";

        if ($verbose) {

            echo $sql;
        }
        $this->mysqli->query($sql) or die($sql . $this->mysqli->error);

        return TRUE;
    }

    public function show() {
        $sql = " show tables";

        $result = $this->mysqli->query($sql);
        if ($this->mysqli->error) {

            $this->alert($sql);
            die();
        }
        return $this->read($result);
    }

    public function describe($table) {
        $sql = " describe $table";

        $result = $this->mysqli->query($sql);
        if ($this->mysqli->error) {

            $this->alert($sql);
            die();
        }

        return $this->read($result);
    }

    public function select ($query, $verbose = FALSE, $needAlias = true, $checkByFieldList = true)
    {

        /*
         * $query['values'] accepts extended hash arrays 
         * 
         * $query(
         *      'table' => tableName
         *      'values' => array('colName' => array(value, type, operater))
         * );
         * See accepted Operaters and Types as const in CLASS Definition
         * handing of type an Operater is done in method group()
         * 
         */

        // $tbl = $query['table'];
        if (!$this->checkTable($query['table'])) {

            return FALSE;
        }

        if (isset($query['cols'])) {
//            print_r($query['cols']);

            $arrCols = $this->maskCols($query['cols'], $needAlias, $checkByFieldList); // MYSQL Select
            $cols = join(', ', $arrCols);
        } else {

            $cols = '*';
        }

        $sql = "";
        $sql .= "SELECT " . $cols;
        $sql .= " FROM " . $this->getTableString();

        if (isset($query['join'])) {

            $joins = $query['join'];
            if (isset($joins)) {

                $sql .= $this->join(' ', $joins);
            }
        }

        $arrVals = array();
        if (isset($query['where'])) {

            $arrVals = $this->maskVals($query['where'], $checkByFieldList); // MYSQL Select
            $arrWhere = $this->group($arrVals, $needAlias); // MYSQL Select

            if (is_array($arrWhere) &&
                count($arrWhere) != 0) {

                $sql .= "\n WHERE (" . join(' AND ', $arrWhere) . ")";
            }
        }
        if (isset($query['limit'])) {
            $sql .= "limit ".$query['limit'];
        }


        if ($verbose) {

            echo '[' . $sql . ']';
            return true;
        }

        $result = $this->mysqli->query($sql);
        if ($this->mysqli->error) {

            $this->alert($sql);
            die();
        }

        $ret = array();
        while ($obj = $result->fetch_object()) {

            $ret[] = $obj;
        }
        $result->close();


        return $ret;
    }

    private function join ($concat, $joins)
    {

        $tbl = $this->getTable();
        $alias = $this->getTableAlias();

        $joinArr = array();
        foreach ($joins as $joinTbl => $join) {

            $ret = "\n join $joinTbl on (";
            $onArr = array();

            foreach ($join as $arg) {
                if (!is_array($arg)) {

                    $onArr[] = " $joinTbl.$arg = $alias.$arg ";
                } else {

                    $argA = $arg[0];
                    $argB = $arg[1];
                    if (is_array($argB)) {
                        foreach ($argB as $k => $v) {
                            $alias = $k;
                            $argB = $v;
                        }
                    }
                    $onArr[] = " $joinTbl.$argA = $alias.$argB ";
                }
            }
            $joinArr[] = $ret . join(" $concat ", $onArr) . ')';
        }

        return join(" $concat ", $joinArr);
    }

    private function alert ($sql = false)
    {

        if ($sql) {

            echo $sql . " \n";
        }
        echo "\n " . $this->mysqli->error;
    }

    private function group ($data, $needsAlias = true)
    {

        $retArr = array();

        $colPref = '';
        if ($needsAlias) {

            $alias = $this->getTableAlias();
            if (!empty($alias)) {

                $colPref .= "`" . $alias . "`.";
            }
        }

        if (is_array($data)) {
            foreach ($data as $col => $value) {

                $useOp = self::OP;
                if (!is_array($value)) {

                    $ret = $colPref . "$col = $value";
                } else {

                    $details = $this->getDetails($value);

//                    print_r($details);

                    $op = $details['op'];
                    $flag = $details['flag'];
                    $prfx = $details['prfx'];

                    $useOp = $this->getOperator($op);
                    $val = $this->translateValue($value, $flag);
                    $usePref = ($prfx) ? $prfx . '.' : $colPref;

                    $ret = $usePref . "`$col` $useOp $val";
                }
                $retArr[] = $ret;
            }
        } else {

            $retArr = ($data) ? array($data) : $data;
        }
//        print_r($retArr);
        return $retArr;
    }


    private function translateValue ($value, $flag)
    {
        $val = $value[0];

        switch ($flag) {
            case self::PASSWORD:
                $val = $this->password($val[0]);
                break;
            case self::DEFAULTVAL:
                $val = ' DEFAULT ';
                break;
            default:
                break;
        }
        return $val;
    }

    private function getOperator ($op)
    {
        switch ($op) {
            case self::LT:
            case self::LE:
            case self::GT:
            case self::GE:
            case self::UE:
            case self::LIKE:
                $useOp = $op;
                break;
            default:
                $useOp = self::OP;
                break;
        }
        return $useOp;
    }

    private function password ($val)
    {

        return "PASSWORD('$val')";
    }

    private function pair ($vals)
    {

        $retArr = array();
        foreach ($vals as $col => $val) {

            $retArr[] = "`" . $col . "` = " . $val;
        }

        return $retArr;
    }

    private function extractCols ($values)
    {

        $cols = array();
        foreach ($values as $col => $val) {

            $cols[] = $col;
        }
        return $cols;
    }

    private function maskCols ($colsArr, $needsAlias = TRUE, $checkByFieldList = TRUE)
    {

        $colPref = '';
        if ($needsAlias) {

            $alias = $this->getTableAlias();
            if (!empty($alias)) {

                $colPref .= "`" . $alias . "`.";
            }
        }

        $cols = array();
        foreach ($colsArr as $col) {
            if (isset($this->fields[$col]) || !$checkByFieldList) {

//                $cols[] =  $colPref."`".$col."`";
                $cols[] = $this->backTick($colPref, $col, $needsAlias);
            }
        }

        return $cols;
    }

    private function backTick ($colPref, $col, $needsAlias)
    {
        if ($needsAlias) {
            return $colPref . "`$col`";
        } else {
            $ret = '';
            $colArr = explode('.', $col);
            return "`" . join('`.`', $colArr) . "`";
        }
    }

    private function maskVals ($data, $checkByFieldList = true)
    {

        $vals = array();
        if (is_array($data)) {

            foreach ($data as $key => $val) {

                $details = $this->getDetails($val);

                $flag = $details['flag'];
                $op = $details['op'];
                $prfx = $details['prfx'];

                if (isset($this->fields[$key]) || !$checkByFieldList) {

                    $passval = $this->getVal($data, $key, $flag);
                    if (is_array($val)) {

                        $ret = array($passval, $flag, $op, $prfx);
                    } else {

                        $ret = $passval;
                    }
                    $vals[$key] = $ret;
                }
            }
        } else {

            $vals = $data;
        }
        return $vals;
    }

    private function getDetails ($val)
    {

        $flag = false;
        $op = false;
        $prfx = false;
        if (is_array($val)) {

            $flag = (isset($val[1]) && !isset($val[2])) ? $val[1] : false;
            $op = (isset($val[2])) ? $val[2] : false;
            $prfx = (isset($val[3])) ? $val[3] : false;

        }

        return array(
            'flag' => $flag,
            'op' => $op,
            'prfx' => $prfx
        );
    }

    private function getVal ($data, $key, $flag)
    {
        $jump = 'jump:';
        $passval = NULL;
        $type = (isset($this->fields[$key])) ? $this->fields[$key] : 'var';
        if (isset($data[$key])) {
            $jump .= '1';
            $passval = (!is_array($data[$key])) ? $data[$key] : $data[$key][0];
        }


        if ($flag === false) {
            if ($type == 'dat' && empty($passval)) {

                $jump .= '1';
                $passval = self::DEFAULTVAL;

            } else if ($type == 'var'
                || $type == 'lon'
                || $type == 'tex'
                || $type == 'dat') {
                
                 $jump .= '3';
                $passval = '"' . addslashes($passval) . '"';
            }
        }
        if ($passval === '???'
            || $passval === false
        ) {
            $jump .= '4';
            var_dump($passval);

            $def = $this->defaults[$key];


            if (is_null($this->defaults[$key])
                && !$this->keys[$key]) {

                $jump .= '5';
                $passval = self::DEFAULTVAL;

            } else if (is_null($this->defaults[$key])
                && !$this->keys[$key]) {

                $jump .= '5';
                $passval = '0';
            }

        } else if ($key === 'ID' && $passval === '') {
            
            $jump .= '7';
            $passval = self::UUID;
        } else if(is_null($data[$key])) {
            
                $jump .= '8';
            $passval = 'NULL';
        }
        return $passval;
    }

    public function tell ()
    {
        return "DB-Socket ";
    }

    /* used for date/time modifications */
    public function ask ($sql)
    {
        $ret = array();
        $result = $this->mysqli->query($sql);
        if ($this->mysqli->error) {

            $this->alert($sql);
            die();
        }
        if(gettype($result) === 'object') {

            while ($obj = $result->fetch_object()) {

                $ret[] = $obj;
            }
            $result->close();
            return $ret;
        } else {
            return $result;
        }
    }
}

?>
