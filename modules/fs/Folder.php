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
    private $deletionMark;
    private $delitionSwitch;
    private $deleted;

    public function __construct(Attribs $attribs, ServiceHandler $srvHandler, Params $params, $deletionMark)
    {
        /* init */
        $this->params = $params;
        $this->srvHandler = $srvHandler;
        $this->attribs = $attribs;
        $this->deleted = false;
        /* flag id detion mode is on already */
        $this->deletionMark = $deletionMark;
        /* flag if delete pattern matches own name */
        /* if this is set and folder cleared - deletion mode will be turned off */
        $this->delitionSwitch = false;
        /* attribs contains all relevant data about the fs object */
        if($attribs->name != '.' && $attribs->name != '..' ) {

            $ID = 0;
            $filePath = $attribs->getWithPath();
            if(is_dir($filePath)) {

                if($params->action == 'save') {

                    /* just trigger db action */
                    $ID = $this->write($attribs,$this->srvHandler->dbSocket);
                } else   if($params->action === 'delete') {
                    /* try trigger deletion action - has to wait until folder is empty */
                    $this->delitionSwitch = $this->setDeleteSwitch($attribs, $params, $this->deletionMark);
                    $attribs->id_file = $this->write($attribs,$this->srvHandler->dbSocket);
                }
                /* Done reflects if given deletes are done */
                if(!$this->params->getDone()) {

                    /* read and parse folder content */
                    $this->walkContent($filePath, $ID);

                    $this->postWalkAction($ID);

                } else if($this->params->verbose) {
                    echo "\n ignore $filePath";
                }
                
            } else {
                echo "\n NO DIR $filePath";
            }
        }
    }

    private function walkContent($filePath, $ID)
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

                /* create container by details
                 * get and aggregate file-/folder-Size
                 * */
                $fileObj = $this->getSibling($filePath,$file,$ID);
                $this->folderSize += $fileObj->getSize();

            }
        }
    }

    private function getSibling($filePath, $file, $ID)
    {
        /* create siblings attribute */
        $siblingAttribs = $this->srvHandler->attribHandler->get($filePath,$file,$ID);
        $sibling = $siblingAttribs->getWithPath();
        $deletionMark = ($this->deletionMark || $this->delitionSwitch) ? true : false;



        if( is_dir($sibling)) {
            /* create folder item */
            $fileObj = new Folder( $siblingAttribs, $this->srvHandler, $this->params, $deletionMark);

        } else if (is_file($sibling)) {
            /* create fileitem */
            $fileObj = new File( $siblingAttribs, $this->srvHandler, $this->params, $deletionMark);

        } else {
            echo " $sibling is no file or has ugly'Name ";
            die();
        }
        return $fileObj;
    }

    private function postWalkAction($ID)
    {

        if($this->params->getAction() == 'save') {

            /* just update size in db on read mode */
            $this->update($ID,['size'=>$this->folderSize],$this->srvHandler->dbSocket);

        }
        if($this->params->getAction() == 'delete' || $this->folderSize === 0) {

            $this->deleted = $this->checkDeleteFolder($this->params, $this->attribs, $this->deletionMark , $this->delitionSwitch, $this->srvHandler->dbSocket, $this->folderSize);
            if($this->deleted) {
                $this->delete($this->attribs,$this->srvHandler->dbSocket);
                echo ']';
            } else {
                echo ')';
            }
        }
    }

    public function getSize()
    {
        return $this->folderSize;
    }

    public function getDeletions()
    {
        return $this->params->getDeletionCount();
    }

    public function isDeleted()
    {
        return $this->deleted;
    }
}
