<?php
include_once('modules/conf/Conf.php');
include_once('modules/fs/FS.php');
include_once('modules/fs/File.php');
include_once('modules/fs/Folder.php');
include_once('modules/fs/Attribs.php');
include_once('modules/fs/AttribHandler.php');
include_once('modules/db/Connection.php');
include_once('modules/db/DB.php');
include_once('modules/db/dbSocket.php');
include_once('modules/handler/Content.php');

if(isset($argv)) {
    if( $argv[1] == 'read'){

        $content = new hmsf\reader\Content();
        $content->read();
    
    } else if($argv[1] == 'delete') {
        if(isset($argv[2])) {
            $name = $argv[2];
            
            $content = new hmsf\reader\Content();
            $content->delete($name);
            
        }
        
    } 
}



/*

 * 
 * 
 * TODO SAVE FOLDER TO OWN//SAME TABLE - - DONE
 * AND DECORATE FILES WITH FOLDER ID - - DONE
 * 
 * 
 * 
 *  */

