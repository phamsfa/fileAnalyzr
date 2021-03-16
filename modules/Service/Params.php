<?php
/**
 * Created by PhpStorm.
 * User: hammesfahr
 * Date: 2/20/19
 * Time: 2:35 PM
 */

namespace hmsf\Service;


class Params
{
    public $action;
    public $method;
    public $option;

    public function __construct ($argv)
    {
        $this->action = $this->check($argv,1);
        $this->method = $this->check($argv,2);
        $this->option = $this->check($argv,3);
    }
    
    public function getAction() {
        return $this->action;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getOption() {
        return $this->option;
    }

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
}