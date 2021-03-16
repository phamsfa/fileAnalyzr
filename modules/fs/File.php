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
    
    public function __construct(Attribs $attribs, ServiceHandler $srvHandler, Params $params) {
        //$path, $name, $parentID, $config
        $this->srvHandler = $srvHandler;
        $this->attribs = $attribs;
        $filePath = $attribs->getWithPath();
        
        if($attribs->name !== '.' && $attribs->name !== '..' && is_file($filePath)) {

            if($params->action === 'save') {
                
                $this->write($attribs,$this->srvHandler->dbSocket);
                
            } else if($params->action === 'delete') {
                
                /* implement deletes with all three options */
                $this->deleteFile($attribs, $params, $this->srvHandler->dbSocket);
            }

            
        }
    }
    
    public function get($property) {
        
        return ($this->attribs) ? $this->attribs->get($property) : 0;
    }

    public function getSize() {
        return $this->attribs->get('size');
    }
}
