<?php
namespace hmsf\fs;
/**
 * small factory aggregates data for fs objects and returns modell
 *
 * @author peter
 */
class AttribHandler {
    
    public $div = '/';
    
    public function __construct() {
        
    }
    
    public function get($path, $name, $id_parent)
    {
        $me = $path.$this->div.$name;
        $isFile = is_file($me);
        if(is_file($me) || is_dir($me)) {
            return new Attribs(array(
                'id_parent' => $id_parent,
                'name' => addslashes($name),
                'path' => addslashes($path),
                'size' => ($isFile) ? filesize($me) : 0,
                'ctime' => $this->getTime($me),
                'owner'=> $this->getOwner($me),
                'hash'=> ($isFile) ? hash_file('md5', $me) : NULL,
                'file' => $isFile
            ), $this->div);
        } else {
            die("$me is no file");
        }
        
        
    }

    public function getByObject(Folderdata $data)
    {
        return $this->get($data->path,$data->name,$data->ID_parent);
    }
    
    private function getOwner($fileName)
    {
        $owner = NULL;
        try {

            $owner = posix_getpwuid(fileowner($fileName));
        } catch (Exception $e) {

            print_r($e);
        }
        return $owner['name'];
    }
    
    private function getTime($fileName)
    {
        $time = NULL;
        try {

            $time = date("Y-d-m H:i:s", filemtime($fileName));
        } catch (Exception $e) {

            print_r($e);
        }
        return $time;
    }
}
