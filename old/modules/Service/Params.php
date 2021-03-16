<?php
/**
 * Created by PhpStorm.
 * User: hammesfahr
 * Date: 2/20/19
 * Time: 2:35 PM
 */

namespace vznrw\service;


class Params
{
    public $action;
    public $method;
    public $option;

    public function __construct ($argv)
    {
        $this->action = (isset($argv[1])) ? $argv[1] : NULL;
        $this->method = (isset($argv[2])) ? $argv[2] : NULL;
        $this->option = (isset($argv[3])) ? $argv[3] : NULL;
    }
}