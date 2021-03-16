<?php

namespace hmsf\fs;

use hmsf\fs\Folderdata;
use hmsf\Service\Params;
use hmsf\Service\ServiceHandler;

/**
 * Description of File
 *
 * @author peter
 */
class File extends \hmsf\fs\FS {
    public $name;
    public $attribs;
    private $me;
    private $data;
    private $srvHandler;
    static $div = '/';
    
    
    
    public function __construct(FolderData $data, ServiceHandler $srvHandler, Params $params) {
        //$path, $name, $parentID, $config
        echo '.';
        $this->srvHandler = $srvHandler;
        $this->data = $data;
        $this->me = $data->path . $srvHandler->attribHandler->div . $data->name;
        
        if($data->name !== '.' && $data->name !== '..' && is_file($this->me)) {

            $this->attribs = $this->config->attribHandler->get($data->path, $data->name, $data->ID_parent);
            if($params->action === 'save') {
                
                $this->write($this->attribs,$this->config->dbSocket);
                
            } else if($params->action === 'delete') {
                
                /* implement deletes with all three options */
            }

            
        }
    }
    
    public function get($property) {
        
        return ($this->attribs) ? $this->attribs->get($property) : 0;
    }

}
