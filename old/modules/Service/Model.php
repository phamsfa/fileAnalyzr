<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace vznrw\service;

/**
 * Objectify DB - results
 *
 * @author hammesfahr
 */
class Model {

    private $dbSocket;
    private $model;

    public function __construct($dbSocket, $model) {

        $this->dbSocket = $dbSocket;
        $this->model = $model;
    }

    public function objectify($row, $model) {

        $arr = array();
        if(is_array($row)) {
            foreach($row as $line) {

                $obj = clone $model;
                $obj->exchangeArray($line);
                $arr[] = $obj;
            }
        }
        return $arr;
    }
    
}
