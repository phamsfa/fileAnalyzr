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
    public function getAction()
    {
        return $this->action;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getOption()
    {
        return $this->option;
    }

    /* get done */
    public function getDone()
    {
        return $this->done;
    }

    public function setDone()
    {
        $level = intval($this->getOption());
        if($this->verbose) {
            echo "\n del-nr: $this->deleteCounter ";
        }


        if($level >= $this->deleteCounter || $level != 0) {
            $this->done = true;
            echo "del-END!! ";
        }

    }

    public function unSetDone() {
        $this->done = false;
    }

    /* increase counter for files/folders deleted */
    public function deleteCounterInc()
    {
        $this->deleteCounter++;
    }

    public function getDeleteCounter() {
        return $this->deleteCounter;
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

    public function get()
    {
        return get_object_vars($this);
    }
}
