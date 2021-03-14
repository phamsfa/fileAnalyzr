<?php

namespace hmsf\fs;
/**
 * Description of File
 *
 * @author peter
 */
class File extends \hmsf\fs\FS {
    public $name;
    public $attribs;
    private $me;
    private $config;
    static $div = '/';
    
    
    
    public function __construct($path, $name, $parentID, $config) {
        echo '.';
        $this->config = $config;
        $this->me = $path . $config->attribHandler::$div . $name;
        
        if($name !== '.' && $name !== '..' && is_file($this->me)) {

            $this->attribs = $this->config->attribHandler->get($path, $name, $parentID);
            $this->name = $this->me;
            
            $this->write($this->attribs,$this->config->dbSocket);
            
            /*

             * TODO action on DEL             */
            
        }
    }
    
    public function get($property) {
        
        return ($this->attribs) ? $this->attribs->get($property) : 0;
    }

}
