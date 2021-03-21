<?php

namespace hmsf\fs;

use hmsf\fs\Folderdata;
use hmsf\Service\Params;
use hmsf\Service\ServiceHandler;

/**
 * Description of File object triggering selected access options
 *
 * @author peter
 */
class File extends FsObject {
    public $name;
    public $attribs;
    private $srvHandler;
    private $deleted;
    
    public function __construct(Attribs $attribs, ServiceHandler $srvHandler, Params $params, $deletionMark)
    {
        //$path, $name, $parentID, $config
        $this->srvHandler = $srvHandler;
        $this->attribs = $attribs;
        $this->deleted = false;
        $filePath = $attribs->getWithPath();
        
        if($attribs->name !== '.' && $attribs->name !== '..' && is_file($filePath)) {

            if($params->action === 'save') {
                
                $this->write($attribs,$this->srvHandler->dbSocket);
                
            } else if($params->action === 'delete' ) {
                if($deletionMark || $this->matchesSearch($attribs, $params)) {

                    /* implement deletes with all three options */
                    $this->deleted = $this->deleteFile($attribs, $params, $this->srvHandler->dbSocket);
                } else {
                    $this->write($attribs,$this->srvHandler->dbSocket);
                }
            }

            
        }
    }
    
    public function get($property)
    {
        return ($this->attribs) ? $this->attribs->get($property) : 0;
    }

    public function getSize()
    {
        return $this->attribs->get('size');
    }

    public function isDeleted()
    {
        return $this->deleted;
    }
}
