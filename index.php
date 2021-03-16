<?php
include_once('modules/conf/Conf.php');
include_once('modules/fs/FsObject.php');
include_once('modules/fs/File.php');
include_once('modules/fs/Folder.php');
include_once('modules/fs/Attribs.php');
include_once('modules/fs/AttribHandler.php');
include_once('modules/db/Connection.php');
include_once('modules/db/DB.php');
include_once('modules/db/dbSocket.php');
include_once('modules/reader/Content.php');
include_once('modules/Service/Params.php');
include_once('modules/Service/ServiceHandler.php');



$params = new hmsf\Service\Params($argv);
$params->verbose = true;

$action = $params->getAction();
$searchString = $params->getMethod();
$option = $params->getOption();


if(isset($action) && ($action == 'save' || ($action == 'delete' && isset($searchString) && isset($option)))) {

    $content = new hmsf\reader\Content();
    $content->read($params);

} else {

    echo "\n choose 'save|delete' as arg 1";
    echo "\n on delete: choose a file- or directory name or an *";
    echo "\n  and an option:  ";
    echo "\n    a = all items with given name; ";
    echo "\n    i(int) = number of occurance of item with given name; ";
    echo "\n    e = every item in path; ";
}


/*

 * 
 * 
 * DONE SAVE FOLDER TO OWN TABLE
 * AND DECORATE FILES WITH FOLDER ID
 * 
 * 
 * 
 *  */

