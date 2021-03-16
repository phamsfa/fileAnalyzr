<?php
namespace vznrw\config;

use vznrw\db\Connection;
use vznrw\db\dbSocket;
use vznrw\db\DB;
use vznrw\fs\Copy;
use vznrw\db\Save;

/*
 * lade zend Modell files
 */
class Loader
{
    private $modules;
    private $conf;
    private $basePath;
    private $scriptPath;
    
    private $con;
    private $db;
    private $copy;
    private $save;
    
    public function __construct($conf) {
        $this->conf = $conf;
        $this->modules = $this->conf->getConf('modules');
        
        $this->scriptPath = $this->modules->scriptpath;
        foreach($this->modules->controller as $key => $modul) {
            include_once($this->scriptPath."/$modul.php");
        }
        
        /*
        $this->basePath = $this->modules->zf2models;
        foreach ($this->modules->models as $key => $modul) {
            include_once($this->basePath."$modul/src/$modul/Model/$modul.php");
        }
        */
    
        $this->conf = new Conf();
        $this->con = new Connection($this->conf);
        $this->db = new DB($this->conf,$this->con);
        $this->save = new Save($this->db);
        $this->copy = new Copy($this->conf);
    }
    public function get($str) {
        return $this->{$str};
    }
}
?>
