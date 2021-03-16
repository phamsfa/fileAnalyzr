<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db
 *
 * @author peter
 */
class DB  extends SQLite3 {
    
    private $db;
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
        $this->db = $this->open($name);
    }
}
