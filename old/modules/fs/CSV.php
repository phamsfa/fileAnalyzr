<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace vznrw\FS;

/**
 * Description of CSV
 *
 * @author hamsfa
 */
class CSV {
    private $path;
    private $div;
    private $end;
    private $lines;
    private $lineNum;
    private $toUTF8;
    private $parseCols;
    private $cols;
    private $i;

    static $head = 'col';
    static $body = 'val';
    //put your code here
    public function config($path,$end,$div,$toUTF8 = false,$parseCols = false) {
        echo "[$path]";
        $this->path = $path;
        $this->div = $div;
        $this->end = $end;
        $this->toUTF8 = $toUTF8;
        $this->parseCols = $parseCols;
    }
    public function read() {
        if(is_file($this->path)) {
            $h = fopen($this->path,'r');
            $l = array();
            $c = 0;
//            while($line = fgets($h, 2400, $this->end)) {
//            while($line = fgetc($h)) {
            
            while($line = fgets($h, 9400)) {
                if($this->toUTF8) $line = utf8_encode($line);
                $lineArr = explode($this->div,$line);
                $l[] = $lineArr;
                $c++;
            }
            $this->lines = $l;
            $this->lineNum = $c;
            $this->checkCSV();
            $this->setCols();
            $this->i = 0;
            echo " \n CSV READ ".$this->i;
        } else {
            echo 'NO FILE '.$this->path;
            die();
        }
    }
    public function getLines() {
        return $this->lines;
    }
    public function getLinesNum() {
        return $this->lineNum;
    }
    public function nextLine() {
        $line = $this->lines[$this->i];
        $this->i++;
        return $line;
    }
    
    public function reset() {
        $this->i = 0;
    }
    public function getCols() {
        return $this->cols;
    }

    public function export($lines,$div,$lineEnd) {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Export.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $i = 0;

        foreach($lines as $line) {
            if($i == 0) {
                echo join($div,$this->line($line,self::$head)).$lineEnd;
                $i++;
            }

            echo join($div,$this->line($line,self::$body)).$lineEnd;
        }
    }

    public function exportFile($lines,$div,$lineEnd,$path) {
        $out = "";
        $i = 0;
        foreach($lines as $line) {
            if($i == 0) {
                $out .= join($div,$this->line($line,self::$head)).$lineEnd;
                $i++;
            }

            $out .= join($div,$this->line($line,self::$body)).$lineEnd;
        }
        file_put_contents($path, $out,FILE_APPEND);
    }

    /* exporthelper header */
    public function line($line,$which) {
        $ret = array();
        foreach($line as $col => $val) {
            switch ($which) {
                case self::$head:
                    $ret[] = $col;
                    break;
                case self::$body:
                    $ret[] = $this->mask($val);
                    break;
                default:
                    break;
            }
        }
        return $ret;
    }
    /*  R E P A I R   C S V   D A T A   -   R E T U R N S  */
    private function checkCSV() {
        /* detect linebreaks within Cols when masked with '"' */
        $newCsv = array();
        $firstPart = false;
        foreach($this->lines as $i => $line) {
            if(is_array($line)) {

                if($firstPart) {
                    $line = $this->montageLines($firstPart,$line);
                    $firstPart = false;
                }
                $last = trim($line[count($line)-1]);

                if( $this->hasUnencloseingMask($line) ) {
                    //echo "\nrun montage $last";
                    $firstPart = $line;
                } else {

                    //echo "\nadd montage $last";
                    $newCsv[] = $this->deMask($line);
                }
            } else {
//                print_r($line);
                die(gettype($line));
            }
        }
        $this->lines = $newCsv;
    }

    private function hasUnencloseingMask($line) {
        $last = trim($line[count($line)-1]);
        $firstChar = substr($last,0,1);
        $lastChar = substr($last,-1);
        if( $firstChar === '"'
            && (
                $lastChar !== '"'
                ||  strlen($last) === 1
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function deMask($line) {
        foreach($line as $i => $field) {
            $value = trim($field);
            $firstChar = substr($value,0,1);
            $lastChar = substr($value,-1);
            if($firstChar === '"' && $lastChar === '"') {
                $arr = \explode('"',$value);
                $line[$i] = $arr[1];
            }
        }
        return $line;
    }
    
    private function montageLines($line,$nextLine) {
        /* concat t lines separated by linebreak within a column */
        $numLastCol = count($line)-1;
        $line[$numLastCol] = substr($line[$numLastCol],1,-1);
        $nextLine[0] = substr($nextLine[0],0,-1);
        /*
        echo '['.$line[$numLastCol].']';
        echo '['.$nextLine[0].']';
        */
        foreach($nextLine as $i => $value) {
            if($i === 0) {
                $line[count($line)-1] .= ' '.$value;
            } else {
                $line[] = $value;
            }
        }
        return $line;
    }

    private function setCols() {
        $newLines = array();
        $this->cols = array();
        if($this->parseCols) {
            foreach($this->lines as $i => $line) {
                if($i == 0) {
                    $this->cols = $this->makeColNames($line);
                } else {
                    $newLines[] = $this->map($line,$this->cols);
                }
            }
        }
        $this->lines = $newLines;
    }

    private function makeColNames($line) {
        $ret = array();
        foreach($line as $col) {
            $ret[] = trim($col);
        }
        return $ret;
    }

    private function map($line,$cols) {
        $ret = array();
        if(is_array($line)) {

            foreach($line as $i => $val) {
                if(isset($cols[$i])) {
                    $ret[$cols[$i]] = $val;
                } else {
                    echo "\n no Col $i :: $val ";
//                    print_r($line);
                    die();
                }
            }
            return $ret;
        } else {
//            print_r($line);
            die(gettype($line));
        }
    }

    private function mask($val) {
        $posCar = strpos($val, "\r");
        $posLF = strpos($val, "\n");
        if(!is_int($posCar) || !is_int($posLF)) {
            return $val;
        } else {
            return '"'.$val.'"';
        }
    }
}
