<?php
namespace vznrw\fs;
/**
 * Description of Read
 *
 * @author hamsfa
 */
class Read {
    /* CLASSES*/
    private $copy;
    private $db;
    /* PATH SHIFTERS */
    private $allDirs = array();
    private $exclude = array();
    private $numDirs = 0;
    private $numFiles = 0;
    /* configer */
    private $writeFS;
    private $writeDB;
    private $table;
    private $dirTable;
    private $path;
    private $col;
    
    public function __construct($loader) {
        //$db,$copy,$write
        $this->copy = $loader->get('copy');
        $this->db = $loader->get('db');
        $this->save = $loader->get('save');
    }
    
    public function start($config){
        $this->config($config);
        echo "TABELLE $this->table ";
        if($this->save->hasDoublesNum($this->table) == 0) {
            echo ' NO DOUBLES ';
            $this->readDir($this->getPath(),0);
        } else {
            //echo "DELETE DOUBLES";
	    $this->save->removeDoubles($this->table);
        }
    }
    
    public function config($config) {
        
        $this->writeFS = (isset($config['writeFS'])) ? $config['writeFS'] : false;
        $this->writeDB = (isset($config['writeDB'])) ? $config['writeDB'] : false;
        $this->dirTable = (isset($config['dirTable'])) ? $config['dirTable'] : false;
        $this->table = (isset($config['table'])) ? $config['table'] : false;
        $this->path = (isset($config['path'])) ? $config['path'] : false;
        $this->col = (isset($config['col'])) ? $config['col'] : false;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function readDir($myPath,$depth)
    {
        // read the base folder and all nested
        
        $path = $myPath;
        if(is_dir($path)) {
            $arrDir = array();
            // look in fs
            $handle = opendir($path);
            // loop for dir
            while($arrDir[] = readdir($handle)){
            }
            closedir($handle);
            sort($arrDir);
            $arrDir = array_reverse($arrDir);
            
            // handle content
            $f = 0;
            foreach($arrDir as $file) {
                // visible content
                if(!preg_match("|^\.|", $file) and strlen($file))  {
                    if(is_dir($path.'/'.$file)) {
			// D I R  
                        $this->numDirs++; 
                        $add = true;
                        foreach($this->exclude as $avoidDir) {
                            if($file == $avoidDir) $add = false;
                        }
                        if($add != false) {
                            //$this->allDirs[] = $myPath.'/'.$file;
                            $item = array(
                                'name' => $file,
                                'cont' => $this->readDir($myPath.'/'.$file,$depth + 1)
                            );
                        }
                    } else {
			// F I L E
                        $this->numFiles++;
                        $date = date ("Y-m-d H:i:s", filemtime($path.'/'.$file));
                        $copied = 0;
                        // handling files
			$fName = $myPath.'/'.$file;
			$cName = $this->save->cleanName($this->path,$fName);
                        $item = array(
                            'fName' => $fName,
			    'cName' => $cName,
                            'datum' => $date,
			    'size' => filesize($fName),
			);
                        if($this->writeDB) {
                            $obj = array(
                                'fName' => $fName,
				'cName' => $cName,
                                'copied' => $copied,
                                'table' => $this->table,
                                'path' => $this->path,
                                'col' => $this->col,
				'size' => $item['size'],
                            );
                            $saveAction = $this->save->item($obj);
                        }
                        if($this->writeFS) {
                            if($saveAction == \vznrw\db\Save::$insert) {
                                $copied = $this->copy->mv($item);
                            } else {
                                $copy = $this->copy->test($item);
                            }
                        }
                    }
                    $arr[$f] = $item;
                    $f++;
                }
            }
            if(isset($arr)) return $arr;
        } else {
            throw new \Exception('File '.$path.' nicht gefunden von !!');
        }
    }
    /*
    public function walkNode($node) {
        $list = array();
        $path = array();
        $name = $node['name'];
        if(isset($node['cont']) && is_array($node['cont']) && count($node['cont'])>0) {
            
            foreach($node['cont'] as $subNode) {
                $sublist = $this->walkNode($subNode);
                foreach($sublist as $subItem) {
                    array_push($list,$node['name'].'/'.$subItem);
                }
            }
        } else if(isset($node['datum'])){
             array_push($list,$node['name']);
        }
        return  $list;
    }
    */
    public function purge($config,$flag){
        $this->config($config);
        $emptyFiles = $this->save->getEmpty($flag);
        foreach ($emptyFiles as $file) {
            $file->file = $this->save->revertName($file->file);
            $file->path = $this->path;
            //print_r($file);
            $this->copy->delete($file);
        }
    }
    
    public function getDirs($config){
        $this->config($config);
        $allFiles = $this->save->getAll($this->table);
        $allDirs = array();
        foreach($allFiles as $row) {
            $file = $this->save->revertName($row->file);
            $pathArr = explode('/',$file);
            $fName = array_pop($pathArr);
            $dir = join('/',$pathArr);
            if(!isset($allDirs[$dir])) {
                $allDirs[$dir] = array();
            }
            $allDirs[$dir][] = $row->ID_files;
            //$this->save->dir($file);
        }
        $this->writeDirs($allDirs);
    }
    private function writeDirs($allDirs) {
        foreach($allDirs as $dir => $IDs) {
            $ID_dir = $this->save->insertDir($dir,$IDs,$this->dirTable);
            $this->save->updateDir($IDs,$ID_dir,$this->table);
        }
    }
}
