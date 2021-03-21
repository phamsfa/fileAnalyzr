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
 * works as factory for readers mimik
 * and as tiny service manager
 *
 * @author hmsf
 */
class Content {

    private $conf;
    private $path;
    private $folder;
    private $serviceHandler;

    public function __construct() {

        $this->conf = new \vznrw\config\Conf();
        $con = new \vznrw\db\Connection($this->conf);
        $dbSocket = new \vznrw\db\dbSocket($con,'files');
        $attribHandler = new \hmsf\fs\AttribHandler();

        $this->serviceHandler = new ServiceHandler($dbSocket,$attribHandler);
    }
    
    public function read(Params $params) {

        $pathData = $this->conf->get('content');
        $attribs = $this->serviceHandler->attribHandler->get($pathData->path,$pathData->name,NULL);

        $tree = new \hmsf\fs\Folder($attribs, $this->serviceHandler, $params, false);

        echo 'Dateien gelöscht: '.$tree->getDeletions();

    }
}
