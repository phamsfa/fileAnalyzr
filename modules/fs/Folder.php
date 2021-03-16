<?php
namespace hmsf\fs;
/*
 * keep away!
 */

use hmsf\Service\Params;
use hmsf\service\ServiceHandler;

/**
 * represents a given folder from config
 * an handles logic to wander tree
 * an take action as configured
 *
 * @author peter
 */
class Folder extends FsObject {

    private $action;
    private $params;
    private $srvHandler;
    private $folderSize;

    public function __construct(Attribs $attribs, ServiceHandler $srvHandler, Params $params)
    {
        /* init */
        $this->params = $params;
        $this->srvHandler = $srvHandler;
        $this->folderSize = 0;

        /* attribs contains all relevant data about the fs object */
        if($attribs->name != '.' && $attribs->name != '..' ) {

            $filePath = $attribs->getWithPath();
            if(is_dir($filePath)) {

                if($params->action == 'save') {

                    /* just tricker db action */
                    $ID = $this->write($attribs,$this->srvHandler->dbSocket);
                } else {

                    $ID = 0;
                    if($params->action === 'delete') {
                        /* try trigger deletion action */
                        $this->checkTriggerDeleteFolder($attribs, $params);
                    }
                }
                /* read and parse folder content */
                $this->handle($filePath, $ID, $attribs);
                
            } else {
                var_dump($attribs);
                echo "\n NO DIR $filePath";
            }
        }
    }
    
    private function handle($filePath, $ID, $attribs)
    {
        if ($dh = opendir($filePath)){
            $folderArr = [];
            while ( ($file = readdir($dh)) !== false ){
                $folderArr[] = $file;
            }
            natsort($folderArr);

            foreach($folderArr as $file) {
                /* create container by details */
                $fileObj = $this->getSibling($filePath,$file,$ID);
                /* get and aggregate file-/folder-Size */
                $this->folderSize += $fileObj->getSize();
            }
            if($this->params->action == 'delete') {

                /* check if item is searched for */
                $this->deleteFolderOnTrigger($attribs, $this->params, $this->srvHandler->dbSocket);

            } else if($this->params->getAction() == 'save') {

                /* just update size in db on read mode */
                $this->update($ID,['size'=>$this->folderSize],$this->srvHandler->dbSocket);
            }
            closedir($dh);
        }
    }

    private function getSibling($filePath, $file, $ID)
    {
        /* create sibblings attribute */
        $siblingAttribs = $this->srvHandler->attribHandler->get($filePath,$file,$ID);
        $sibling = $siblingAttribs->getWithPath();

        if( is_dir($sibling)) {

            return new Folder( $siblingAttribs, $this->srvHandler, $this->params);

        } else if (is_file($sibling)) {

            return new File( $siblingAttribs, $this->srvHandler, $this->params);

        } else {
            echo " $sibling is no file sorry ";
            die();
        }
    }

    public function getSize() {
        return $this->folderSize;
    }
}
