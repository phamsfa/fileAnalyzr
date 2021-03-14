<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Socket
 *
 * @author peter
 */
class Socket {
    
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    
    
    public function insert($obj) {
        $vals = array();
        
        foreach($obj as $k => $v) {
            
            $vals["'$val'"];
        }
        
        $str = join(', ',$vals);
         $sql =<<<EOF
INSERT INTO files (name,path,ctime,size,owner,hash)
VALUES ($str );
EOF;
         
        $ret = $this->db->exec($sql);
        if(!$ret) {
            
           echo $this->db->lastErrorMsg();
           
        } else {
            
           echo "Records created successfully\n";
        }
        $this->db->close();
    }
    
    public function close() {
        $this->db->close();
    }
    
    public function open() {
        $this->db->open();
    }
}
