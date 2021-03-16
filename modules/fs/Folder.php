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
    private $attribs;
    private $srvHandler;
    private $folderSize;

    public function __construct(Attribs $attribs, ServiceHandler $srvHandler, Params $params)
    {
        /* init */
        $this->params = $params;
        $this->srvHandler = $srvHandler;
        $this->attribs = $attribs;
        /* attribs contains all relevant data about the fs object */
        if($attribs->name != '.' && $attribs->name != '..' ) {

            $filePath = $attribs->getWithPath();
            if(is_dir($filePath)) {

                if($params->action == 'save') {

                    /* just trigger db action */
                    $ID = $this->write($attribs,$this->srvHandler->dbSocket);
                } else {

                    $ID = 0;
                    if($params->action === 'delete') {
                        /* try trigger deletion action - has to wait until folder is empty */
                        $this->checkTriggerDeleteFolder($attribs, $params);
                    }
                }
                /* Done reflects if given deletes are done */
                if(!$this->params->getDone()) {

                    echo "\n  -> search more ";
                    /* read and parse folder content */
                    $this->handle($filePath, $ID, $attribs);
                }
                
            } else {
                var_dump($attribs);
                echo "\n NO DIR $filePath";
            }
        }
    }
    
    private function handle($filePath, $ID, $attribs)
    {
        $this->folderSize = 0;
        if ($dh = opendir($filePath)){
            $folderArr = [];
            /* Read Directory */
            while ( ($file = readdir($dh)) !== false ){

                $folderArr[] = $file;
            }
            closedir($dh);
            /* sort */
            natsort($folderArr);
            /* walk content od directory */
            foreach($folderArr as $file) {

                if(!$this->params->getDone()) {

                    echo "\n work on $filePath/$file";
                    /* create container by details */
                    $this->folderSize += $this->getSibling($filePath,$file,$ID);
                    /* get and aggregate file-/folder-Size */

                } else if($this->params->verbose) {

                    echo "\n ignore $filePath/$file";
                }
            }
            /* if deletion is not done already */
            if(!$this->params->getDone()) {

                if($this->params->action == 'delete') {

                    /* check if item is searched for */
                    $this->deleteFolderOnTrigger($this->attribs, $this->params, $this->srvHandler->dbSocket);

                } else if($this->params->getAction() == 'save') {

                    /* just update size in db on read mode */
                    $this->update($ID,['size'=>$this->folderSize],$this->srvHandler->dbSocket);
                }
            }
        }
    }

    private function getSibling($filePath, $file, $ID)
    {
        /* create siblings attribute */
        $siblingAttribs = $this->srvHandler->attribHandler->get($filePath,$file,$ID);
        $sibling = $siblingAttribs->getWithPath();

        if( is_dir($sibling)) {
            /* create folder item */
            $fileObj = new Folder( $siblingAttribs, $this->srvHandler, $this->params);

        } else if (is_file($sibling)) {
            /* create fileitem */
            $fileObj = new File( $siblingAttribs, $this->srvHandler, $this->params);

        } else {
            echo " $sibling is no file sorry ";
            die();
        }
        $fileObj->getSize();
    }

    public function getSize() {
        return $this->folderSize;
    }
}
