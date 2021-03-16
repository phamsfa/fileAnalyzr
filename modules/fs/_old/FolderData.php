<?php


namespace hmsf\fs;


class FolderData
{
    public $path;
    public $name;
    public $ID_parent;

    public function __construct($path = null, $name = null, $ID_parent = null) {
        $this->name = $name;
        $this->path = $path;
        $this->ID_parent = $ID_parent;
    }

    public function set($key, $value) {
        if(property_exists($this,$key)) {
            $this->$key = $value;
        }
    }
}