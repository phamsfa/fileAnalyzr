<?php

namespace hmsf\fs;

/**
 * Description of Attribs
 *
 * @author peter
 */
class Attribs {
    
    public $id_file;
    public $id_parent;
    public $name;
    public $path;
    public $size;
    public $ctime;
    public $owner;
    public $hash;

    public function __construct($arr) {
        $this->id_file = NULL;
        $this->id_parent = (isset($arr['id_parent'])) ? $arr['id_parent'] : null;
        $this->name = (isset($arr['name'])) ? $arr['name'] : null;
        $this->path = (isset($arr['path'])) ? $arr['path'] : null;
        $this->size = (isset($arr['size'])) ? $arr['size'] : null;
        $this->ctime = (isset($arr['ctime'])) ? $arr['ctime'] : null;
        $this->owner = (isset($arr['owner'])) ? $arr['owner'] : null;
        $this->hash = (isset($arr['hash'])) ? $arr['hash'] : null;
    }
    
    public function get($col = false) {
        if($col) {
            if(isset($this->{$col})) {
                return $this->{$col};
            } else {
                return NULL;
            }
        } else {
            return get_object_vars($this);
        }
    }
    
    public function set($key, $value) {
        if(property_exists($this,$key)) {
            $this->key = $value;
        }
    }
}