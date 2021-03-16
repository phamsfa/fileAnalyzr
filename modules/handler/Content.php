<?php
namespace hmsf\reader;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use hmsf\fs\Folderdata;
use hmsf\service\Params;
use hmsf\service\ServiceHandler;

/**
 * Description of Content
 *
 * @author peter
 */
class Content {
    
    private $path;
    private $folder;
    private $serviceHandler;
    //put your code here
    public function __construct() {

        $conf = new \vznrw\config\Conf();
        $con = new \vznrw\db\Connection($conf);
        $dbSocket = new \vznrw\db\dbSocket($con,'files');
        $attribHandler = new \hmsf\fs\AttribHandler();

        $this->serviceHandler = new ServiceHandler($dbSocket,$attribHandler);

        $pathData = $conf->get('content');
        $this->path = $pathData->path;
        $this->folder = $pathData->folder;

    }
    
    public function read(Params $params) {

        $data = new FolderData($this->path,$this->folder,NULL);
        $root = new \hmsf\fs\Folder($data, $this->serviceHandler, $params);

    }
}
