<?php
/*
 * Created by PhpStorm.
 * User: hammesfahr
 * Date: 2/20/19
 * Time: 2:35 PM
 *
 * handle comandline arguments
 * and keep track on relevant globals
 *
 */

namespace hmsf\Service;


class Params
{
    public $action;
    public $method;
    public $option;
    public $verbose;

    /* extendes for making globals accessable */

    /* number of files deleted */
    private $deleteCounter = 0;
    /* number of matches to delete string  */
    private $deletionRound = 0;
    /* trigger to be activated for directory cleaning */
    private $deleteFlag = false;
    /* path of file found to be deleted */
    private $deletePath;
    /* job success */
    private $done;

    public function __construct ($argv)
    {
        /*
         * read cli arguments and transform strings to booleans if possible
         */
        $this->action = $this->check($argv,1);
        $this->method = $this->check($argv,2);
        $this->option = $this->check($argv,3);
    }

    /* GETTERS */
    public function getAction() {
        return $this->action;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getOption() {
        return $this->option;
    }

    /* get done */
    public function getDone() {
        return $this->done;
    }

    /* increase counter for files/folders deleted */
    public function deleteCounterInc() {
        $this->deleteCounter++;
    }

    /* increase number of finings by search string */
    public function deletionRoundInc() {
        $this->deletionRound++;
        if($this->deletionRound == $this->method && $this->method != 0) {
            $this->done = true;
        }
    }

    /* get num of done deletions */
    public function getDeletionRound() {
        return $this->deletionRound;
    }

    /* activate deletion modus to clean folder befor removing dir */
    public function setDeleteFlagAndPath($path) {
        if($this->option == 0 || $this->option > $this->deletionRound) {

            $this->deleteFlag = true;
            $this->deletePath = $path;
            //$this->deletionRound++;
        }
    }

    /* which item has triggered deletion mode on? */
    public function getDeletePath() {
        return $this->deletePath;
    }

    /* stop deletion mode after clearing target folder */
    public function unsetDeletMode() {
        $this->deleteFlag = false;
        $this->deletePath = NULL;
        $this->done = true;
    }

    /* is deletion mode on? */
    public function getDeletFlag() {
        return $this->deleteFlag;
    }

    /* get cli arguments and transform boolean values */
    private function check($args,$num)
    {
        if(!isset($args[$num])) {
            return NULL;
        }
        if($args[$num] === 'true' || $args[$num] === 'TRUE') {

            return true;
        } else  if($args[$num] === 'false' || $args[$num] === 'FALSE') {

            return false;
        } else {

            return $args[$num];
        }
    }

    public function get() {
        return get_object_vars($this);
    }
}