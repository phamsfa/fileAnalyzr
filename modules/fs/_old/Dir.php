<?php
/**
 * Created by PhpStorm.
 * User: hammesfahr
 * Date: 6/13/18
 * Time: 2:36 PM
 */

namespace vznrw\fs;


class Dir
{
    public $path;
    public $files;
    public $details;
    public $dirs;

    public function __construct($path = false) {
        if($path) {
            $this->path = $path;
        }
        $this->files = array();
        $this->dirs = array();
        $this->details = array();
    }

    public function setPath($path = false) {
        $this->path = $path;
    }

    public function read($path, $getFiles = false, $getDirs = false) {
        $handle = opendir($path);
        // loop for dir
        $arrDir = array();


        while($arrDir[] = readdir($handle)){
        }
        closedir($handle);
        foreach($arrDir as $fObj) {
            $objPath = $path.'/'.$fObj;
            if($getFiles && is_file($objPath)) {
                $this->files[] = $objPath;
                $this->details[] = $this->info($objPath);
            } else if($getDirs && is_dir($objPath) && !$this->isSys($objPath)) {
                $this->dirs[] = $objPath;
                $this->read($objPath, $getFiles, $getDirs);
            }
        }
    }

    public function getFiles() {
        return $this->files;
    }
    public function getDirs() {
        return $this->dirs;
    }
    public function getDetails() {
        return $this->details;
    }
    public function info($fObj) {
        return stat($fObj);
    }

    private function isSys($path) {
        $pathArr = explode('/',$path);
        $lastFolderName = $pathArr[count($pathArr)-1];
        if($lastFolderName === '.' | $lastFolderName === '..' | $lastFolderName === '') {
            return true;
        } else {
            return false;
        }
    }
}