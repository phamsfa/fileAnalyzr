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

//$methods = [0,10,11,17,18,19,20,21,23,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,'Templates-alt','V1','Elephant-180x180-final.png'];

$params = new hmsf\Service\Params($argv);
$params->verbose = false;

$action = $params->getAction();
$searchString = $params->getMethod();
$option = $params->getOption();

$content = new hmsf\reader\Content();

//$params->method = $methods;
$params->unSetDone();


$action = $params->getAction();
$searchString = $params->getMethod();
$option = $params->getOption();


if(isset($action) && ($action == 'save' || ($action == 'delete' && isset($searchString) && isset($option)))) {

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

