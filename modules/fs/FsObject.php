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
            echo ($attribs->file === 'TRUE') ? '.' : ']';
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
        $dbSocket->ask($query,true);
        echo ")";
    }

    function delete(Attribs $attribs,dbSocket $dbSocket) {
        if(!isset($attribs->id_file)) {
            $id = $this->check($attribs,$dbSocket);
        } else {
            $id = $attribs->id_file;
        }
        if($id) {
            $query = "delete from `file` where id_file = $id";
            $dbSocket->ask($query);
            echo 'x';
        }
    }

    public function matchesSearch($attribs, $params)
    {
        $searchStrings = $params->getMethod();
        if(is_array($searchStrings)) {

            foreach($searchStrings as $searchString) {

                if($this->wildcardComparer($searchString, $attribs->name)) {
                    return true;
                }
            }
        } else if(is_string($searchStrings)){
            return $this->wildcardComparer($searchStrings, $attribs->name);
        }
        return false;
    }

    public function wildcardComparer($searchString, $name)
    {
        if($searchString === $name) {
            return true;
        } else if(strstr($searchString,'*')) {
            $searchArr = explode('*',$searchString);
            foreach($searchArr as $subString) {
                if(strstr($name,$subString)) {
                    return true;
                }
            }
        }
        return false;
    }

    function setDeleteSwitch(Attribs $attribs, Params $params, bool $deletionMark) {
        $matches = $this->matchesSearch($attribs, $params);
        if($matches && !$deletionMark) {
            return true;
        } else {
            return false;
        }
    }

    function checkDeleteFolder($params, $attribs, $deletionMark , $deletionSwitch, $folderSize)
    {
        $return = false;
        if(!$params->getDone() || $folderSize === 0) {
            if(($deletionMark || $deletionSwitch || $folderSize === 0)) {
                if($params->verbose) {

                    echo "\n DELETE FOldEr ".$attribs->getWithPath();
                } else {
                    try {
                        rmdir($attribs->getWithPath());
                    } catch (Exception $e) {
                        echo "del DIR ".$attribs->getWithPath()." NOT DONE ";
                        var_dump($e);
                        die();
                    }
                    echo 'D';
                }
                $return = true;
                $params->deleteCounterInc();
            } else {
                echo 'd';
            }
            if($deletionSwitch) {
                $params->setDone();
            }
        }
        return $return;
    }

    /* delete files  */
    function deleteFile(Attribs $attribs, Params $params, dbSocket $dbSocket)
    {
        if($params->verbose) {

            echo "\n DELETE FILE $attribs->path/$attribs->name";

        } else {

            try {

                unlink($attribs->getWithPath());
            } catch (Exception $e) {
                echo "del File ".$attribs->getWithPath()." NOT DONE ";
                var_dump($e);
                die();
            }
            echo 'F';
            $params->deleteCounterInc();
            return true;
        }
        return false;

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
