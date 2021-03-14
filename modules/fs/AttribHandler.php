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
    
    static $div = '/';
    
    public function __construct() {
        
    }
    
    public function get($path, $name, $id_parent) {
        $me = $path.self::$div.$name;
        $ownerDetails = posix_getpwuid(fileowner($me));
        return new Attribs(array(
            'id_parent' => $id_parent,
            'name' => addslashes($name),
            'path' => addslashes($me),
            'size' => filesize($me),
            'ctime' => date("Y-d-m H:i:s", filectime($me)),
            'owner'=> $ownerDetails['name'],
            'hash'=> (is_file($me)) ? hash_file('md5', $me) : NULL,
        ));
        
    }
    
}
