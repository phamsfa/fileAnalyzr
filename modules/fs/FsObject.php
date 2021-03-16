<?php

namespace hmsf\fs;

use hmsf\Service\Params;
use vznrw\db\dbSocket;

/**
 * contains all access functions for db an fs for file and folder objects
 *
 * @author peter
 */

class FsObject {

    /* write db representation of fs */
    function write(Attribs $attribs, dbSocket $dbSocket)
    {
        $isAvailWithID = $this->check($attribs, $dbSocket);
        if(!$isAvailWithID) {

            $data = $attribs->get();
            $queryArr = [
                'table' => 'file',
                'values' => $data
            ];
            $insertID = $dbSocket->insert($queryArr);
            $attribs->set('ID_file',$insertID);
            echo ($attribs->file === 'TRUE') ? '.' : '[';
            return $insertID;
        } else {
            echo ($attribs->file === 'TRUE') ? '_' : '(';
            return $isAvailWithID;
        }

    }
    /* update value for size in db after recursively adding sizes of files to folder */
    function update ($id_parent, $data, $dbSocket)
    {
        $query = "update `file` set `size` = ".$data['size']." WHERE id_file =  $id_parent";
        echo "]";
        $dbSocket->ask($query,true);
    }

    function delete(Attribs $attribs,dbSocket $dbSocket) {
        $id = $this->check($attribs,$dbSocket);
        if($id) {
            $query = "delete from `file` where id_file = $id";
            $dbSocket->ask($query);
        }
    }


    /* set delete flag for recursively removing files/folders with and within deletion target */
    function checkTriggerDeleteFolder(Attribs $attribs, Params $params)
    {
        if($attribs->name === $params->getMethod() && $params->getDeletFlag() === false) {

            $params->setDeleteFlag($attribs->getWithPath());
            if($params->verbose) {

                echo "\n start Deleting Content on $attribs->path/$attribs->name";
            }
        }
    }

    /* delete folders contained in deletion target and when empty: target itself */
    function deleteFolderOnTrigger(Attribs $attribs, Params $params, dbSocket $dbSocket)
    {
        if($attribs->name !== $params->getMethod() && $params->getDeletFlag() === true) {

            $msg = "\n just delete folder $attribs->path/$attribs->name because of trigger";

        } else if ($attribs->name === $params->getMethod() && $attribs->getWithPath() === $params->getDeletePath()) {

            $msg = "\n delete folder $attribs->path/$attribs->name because of search-term and now is empty ";
            /* item we have searched for is now empty for deletion */
            $params->unsetDeletFlag();
        }

        if($params->verbose) {
            echo $msg;
        } else {
            rmdir($attribs->getWithPath());
            $this->delete($attribs,$dbSocket);
        }
    }

    /* delete files  */
    function deleteFile(Attribs $attribs, Params $params, dbSocket $dbSocket)
    {
        if($attribs->name === $params->getMethod() || $params->getDeletFlag() === true){

            if($params->verbose) {

                echo "\n DELETE FILE $attribs->path/$attribs->name";
                echo ($params->getDeletFlag()) ? ' upper folder to delete' : 'my name matches';
            } else {

                unlink($attribs->getWithPath());
                $params->deleteCounterInc();
                $this->delete($attribs,$dbSocket);
            }
            if(!$params->getDeletFlag()) {
                /* when not flagged by folder - do increase on rounds */
                $params->deletionRoundInc();
            }
        }

    }

    private function check($attribs, $dbSocket)
    {
        $query = "select `id_file` from `file` where name = '$attribs->name' and `path` = '$attribs->path'";
        $result = $dbSocket->ask($query,true);
        if(count($result) > 0) {

            return $result[0]->id_file;
        } else {

            return false;
        }
    }
}
