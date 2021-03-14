<?php
namespace hmsf\fs;

/**
 * Description of Folders
 *
 * @author peter
 */
class Folder extends \hmsf\fs\FS {
    //put your code here
    public $name;
    public $path;
    private $content;
    private $me;
    
    private $config;
    
    static $div = '/';
    
    public function __construct($path,$name,$parentID, $config) {
        //
        //echo '|';
        $this->content = array();
        $me = $path . $config->attribHandler::$div . $name;
        $this->config = $config;

        if($name != '.' && $name != '..' ) {
            
            if(is_dir($me)) {
                
                $this->name = $name;
                $this->path = $me; 
                
                $this->attribs = $this->config->attribHandler->get($path, $name, $parentID);
                $ID = $this->write($this->attribs,$this->config->dbSocket);
                
                $this->handle($me, $ID);
                
            } else {
                
                echo "\n NO DIR $me";
                //die(); SSS
            }
        }
    }
    
    private function handle($me, $ID) {
        if ($dh = opendir($me)){
            $folderSize = 0;
            while ( ($file = readdir($dh)) !== false ){
                $me = $this->path . $this->config->attribHandler::$div . $file;
                
                if( is_dir( $me)) {
                   
                    $this->content[] = new Folder( $this->path, $file, $ID, $this->config);

                } else {
                    
                    $file = new File( $this->path, $file, $ID, $this->config);
                    $folderSize += $file->get('size');
                    $this->content[] = $file;

                }
            }
            $this->update($ID,['size'=>$folderSize],$this->config->dbSocket);
            closedir($dh);
            
            /*

             * TODO action on DEL             */
        }
    }
}
