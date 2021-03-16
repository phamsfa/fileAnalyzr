<?php
namespace hmsf\fs;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttribHandler
 *
 * @author peter
 */
class AttribHandler {
    
    public $div = '/';
    
    public function __construct() {
        
    }
    
    public function get($path, $name, $id_parent) {
        $me = $path.$this->div.$name;
        echo "\n $me";
        if(is_file($me) || is_dir($me)) {
            $ownerDetails = $this->getOwner($me);
            return new Attribs(array(
                'id_parent' => $id_parent,
                'name' => addslashes($name),
                'path' => addslashes($me),
                'size' => (is_file($me)) ? filesize($me) : 0,
                'ctime' => $this->getTime($me),
                'owner'=> $ownerDetails['name'],
                'hash'=> (is_file($me)) ? hash_file('md5', $me) : NULL,
            ));
        } else {
            die("$me is no file");
        }
        
        
    }

    public function getByObject(Folderdata $data) {
        return $this->get($data->path,$data->name,$data->ID_parent);
    }
    
    private function getOwner($fileName) {
        $owner = NULL;
        try {
            $owner = posix_getpwuid(fileowner($fileName));
        } catch (Exception $e) {
            print_r($e);
        }
        return $owner;
    }
    
    private function getTime($fileName) {
        $time = NULL;
        try {
            $time = date("Y-d-m H:i:s", filemtime($fileName));
        } catch (Exception $e) {
            print_r($e);
        }
        return $time;
    }
}
