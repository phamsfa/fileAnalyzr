<?php
namespace hmsf\fs;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use hmsf\fs\Folderdata;
use hmsf\Service\Params;
use hmsf\service\ServiceHandler;

/**
 * Description of Folders
 *
 * @author peter
 */
class Folder extends \hmsf\fs\FS {
    //put your code here
    public $name;
    public $path;
    public $ID_parent;
    public $attribs;
    private $content;

    private $action;
    private $params;
    private $srvHandler;
    
    static $div = '/';
    
    public function __construct(FolderData $data, ServiceHandler $srvHandler, Params $params) {
        // $path,$name,$parentID
        //echo '|';
        $this->name = $data->name;
        $this->path = $data->path;
        $this->ID_parent = $data->ID_parent;
        $this->params = $params;

        $this->content = array();
        $me = $this->path . $srvHandler->attribHandler->div . $this->name;
        $this->srvHandler = $srvHandler;

        if($this->name != '.' && $this->name != '..' ) {
            
            if(is_dir($me)) {

                //$this->path = $me;
                

                if($params->action == 'save') {

                    $this->attribs = $this->srvHandler->attribHandler->get($this->path, $this->name, $this->ID_parent);
                    $ID = $this->write($this->attribs,$this->srvHandler->dbSocket);
                    echo '|';

                }
                $this->handle($me, $ID);
                
            } else {
                
                echo "\n NO DIR $me";
            }
        }
    }
    
    private function handle($me, $ID) {
        if ($dh = opendir($me)){
            $folderSize = 0;
            while ( ($file = readdir($dh)) !== false ){
                //$pathArr = [$this->path,$this->name,$file];
                $me = $this->path . $this->srvHandler->attribHandler->div . $file;
                //$me = join($this->srvHandler->attribHandler->div,$pathArr);
                echo "\n $me";
                $data = new FolderData($this->path, $file, $ID);
 
                if( is_dir($me)) {

                    $this->content[] = new Folder( $data, $this->srvHandler, $this->params);

                } else if (is_file($me)) {
                    
                    $fileObj = new File( $data, $this->srvHandler, $this->params);
                    $folderSize += $fileObj->get('size');
                    //$this->content[] = $file;

                } else {
                    echo " no file sorry ";
                }
            }
            $this->update($ID,['size'=>$folderSize],$this->srvHandler->dbSocket);
            closedir($dh);
            if($this->params->action == 'delete') {
                /* to be triggered when and implemented */
            }
        }
    }
}
