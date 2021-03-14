<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace vznrw\fs;

/**
 * Description of ReadJS
 *
 * @author hamsfa
 */
class ReadJS {
    private $data;
    private $exclude;
    public function __construct($exclude) {
        $this->data = array();
        $this->exclude = $exclude;
    }
    public function run($arr,$path) {
        $i = 0;
        foreach($arr as $file) {
            if(is_file($path.$file)) {
                $fCont = $this->parse($path,$file);
                if($i < 1) {
                    //print_r($fCont);
                    $this->sort($fCont);
                }
                $i++;
            }
        }
    }
    private function sort($fCont) {
        $l = 0;
        foreach($fCont as $line) {
//            $class = $this->isClass($line);
//            if($class) echo "$class";
//            $func = $this->isFunction($line);
//            if($func) echo "$func";
            $this->brackets($line,$l);
            
            $l++;
        }
    }
    private function isJquery($line) {
        $suchmuster = "|(?P<pre>\W+)\(|i";
        $isQuery = preg_match($suchmuster, $line, $treffer, PREG_OFFSET_CAPTURE);
        //print_r($treffer);
        return (isset($treffer['pre']) && trim($treffer['pre'][0]) === '$')? true : false;
    }
    private function brackets($line,$l) {
        if(!$this->isJquery($line)) {
            $objPattern =   "|(?P<name>\w+) \= \{|";
            $namePattern =  "|(?P<name>\w+) (?P<pre>\S+)\((?P<cond>\S+)\)|i";
            $funcPatternA =  "|(?P<pre>\S+): function\((?P<cond>\S+)\)|i";
            $funcPatternB =  "|(?P<pre>\S+):function\((?P<cond>\S+)\)|i";
            $methodPatter = "|(?P<pre>\S+)\((?P<cond>\S+)\)|i";
            $plainPattern = "|(?P<pre>\S+)\(\)|i";
            if(preg_match($objPattern, $line, $treffer, PREG_OFFSET_CAPTURE)) {
                 $name = $treffer['name'][0];
                echo "\n $l) -OBJ- $name";
            } else if(preg_match($namePattern, $line, $treffer, PREG_OFFSET_CAPTURE)) {
                $pre = $treffer['pre'][0];
                $cond = $treffer['cond'][0];
                $name = $treffer['name'][0];
                echo "\n $l) -NAME- $name - $pre";
//                print_r($treffer);
            } else if( preg_match($funcPatternA, $line, $treffer, PREG_OFFSET_CAPTURE)
                    || preg_match($funcPatternB, $line, $treffer, PREG_OFFSET_CAPTURE)) {
                $pre = $treffer['pre'][0];
                $cond = $treffer['cond'][0];
                echo "\n $l) -func- $pre";
//                print_r($treffer);
            } else if(preg_match($methodPatter, $line, $treffer, PREG_OFFSET_CAPTURE)) {
                $pre = $treffer['pre'][0];
                $cond = $treffer['cond'][0];
                echo "\n $l) -METH- $pre";
//                print_r($treffer);
            } else if(preg_match($plainPattern, $line, $treffer, PREG_OFFSET_CAPTURE)) {
                $pre = $treffer['pre'][0];
                echo "\n $l) -PLAIN- $pre";
//                print_r($treffer);
            }
        } else {
//            echo "\n ++ jQuery";
        }
        $suchmuster = "|\W+|i";
        if(preg_match($suchmuster, $line, $treffer, PREG_OFFSET_CAPTURE)) {
            //$type = $treffer['type'][0];
            //echo "\n type:      $type";
            //print_r($treffer);
        }
        $suchmuster = "|(?P<type>\w+) {|i";
        if(preg_match($suchmuster, $line, $treffer, PREG_OFFSET_CAPTURE)) {
            $type = $treffer['type'][0];
            //echo "\n type:      $type";
        }
        
    }
    private function isClass($line) {
        $suchmuster = "|(?P<name>\w+) = {|";
        if(preg_match($suchmuster, $line, $treffer, PREG_OFFSET_CAPTURE)) {
            //print_r($treffer);
            if($treffer['name'][0]) {
                return $treffer['name'][0];
            } 
                
        }
        return false;
    }
    private function isFunction($line) {
        $suchmuster = "|(?P<name>\w+):function((?P<attr>\*))|";
        if(preg_match($suchmuster, $line, $treffer, PREG_OFFSET_CAPTURE)) {
            //print_r($treffer);
            if($treffer['name'][0]) {
                return $treffer['name'][0].':'.$treffer['attr'];
            } 
                
        }
        return false;
    }
    private function parse($path,$file) {
        if($this->filter($file) === 0) {
            echo "\n $path :: $file\n";
             $fCont = $this->read($path, $file);
            return $fCont;
        }
        
    }
    private function filter($file) {
        foreach($this->exclude as $str) {
            $suchmuster = "|^$str|";
            return preg_match($suchmuster, $file, $treffer, PREG_OFFSET_CAPTURE);
        }
    }
    private function read($path, $file) {
        $handle = @fopen($path.$file, "r");
        $content = array();
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $content[] = $buffer;
            }
            if (!feof($handle)) {
                echo "Fehler: unerwarteter fgets() Fehlschlag\n";
            }
            fclose($handle);
        }
        return $content;
    }
}
