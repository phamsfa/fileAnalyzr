<?php

namespace hmsf\fs;

/**
 * Description of FS
 *
 * @author peter
 */
class FS {
    
    function write($attribs, $dbSocket) {
        $data = $attribs->get();
        $queryArr = [
            'table' => 'file',
            'values' => $data
        ];
        $insertID = $dbSocket->insert($queryArr);
        $attribs->set('ID_file',$insertID);
        
        return $insertID;
    }
    
    function update ($id_parent,$data,$dbSocket) {
        $query = "update `file` set `size` = ".$data['size']." WHERE id_file =  $id_parent";
        
        $dbSocket->ask($query,true);
    } 
    
}
