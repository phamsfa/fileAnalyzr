<?php
include_once('modules/conf/Conf.php');
include_once('modules/fs/FS.php');
include_once('modules/fs/File.php');
include_once('modules/fs/Folder.php');
include_once('modules/fs/FolderData.php');
include_once('modules/fs/Attribs.php');
include_once('modules/fs/AttribHandler.php');
include_once('modules/db/Connection.php');
include_once('modules/db/DB.php');
include_once('modules/db/dbSocket.php');
include_once('modules/handler/Content.php');
include_once('modules/Service/Params.php');
include_once('modules/Service/ServiceHandler.php');


$content = new hmsf\reader\Content();

$params = new hmsf\Service\Params($argv);

$action = $params->getAction();
$searchString = $params->getMethod();
$option = $params->getOption();

if(isset($action) && ($action == 'save' || ($action == 'delete' && isset($searchString) && isset($option)))) {

    $content->read($params);

} else {

    echo "\n choose 'save|delete' as arg 1";
    echo "\n on delete: choose a file- or directory name ";
    echo "\n  and an option:  ";
    echo "\n    a = all items with given name; ";
    echo "\n    i(int) = number of occurance of item with given name; ";
    echo "\n    e = every item in path; ";
}


/*

 * 
 * 
 * TODO SAVE FOLDER TO OWN TABLE 
 * AND DECORATE FILES WITH FOLDER ID
 * 
 * 
 * 
 *  */

