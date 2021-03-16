<?php


namespace vznrw\config;


class Params {
    private $action;
    private $method;
    private $option;
    private $names = ['action', 'method', 'option'];
    private $options;
    private $altOptions;

    /**
     * Params constructor.
     * @param $argv ## gets cli arguments from main script
     * @param array $options ## hash array, injects relevant options for args
     */
    public function __construct($argv, $options = []) {

        echo "\n __  create params ___\n";
        $this->options = $options;
        array_shift($argv);
        $this->altOptions = [];
        $this->parseOptions($argv,0,$options);

    }

    private function parseOptions($argv,$i,$options) {

         $param = $this->names[$i];
         $matches = false;
         if(isset($argv[$i])) {

             $arg = $argv[$i];
             $listAllowed = [];
             foreach($options as $option => $siblings) {

                if(is_array($siblings) && is_string($option) && $arg === $option) {

                    $this->{$param} = $option;
                    //echo "\n $param => $option [arr]";
                    $this->parseOptions($argv,($i+1),$siblings);
                    $matches = true;

                } else if(is_int($option) && is_string($siblings) && $arg === $siblings) {

                    $this->{$param} = $siblings;
                    //echo "\n $param => $siblings [str]";

                    $this->storeAdditional($argv, $i);

                    $matches = true;

                } else {

                    $listAllowed[] = (is_array($siblings) && is_string($option)) ? $option : $siblings;
                }

             }
             if(!$matches) {

                 echo "\n $arg not allowed, try: [" . join('|', $listAllowed) . ']';
             }
         } else {
             $missingList = [];
             foreach($options as $key => $val) {
                 $missingList[] = (is_array($val) && is_string($key)) ? $key : $val;
             }
             echo "\n missing argument, try: [" . join('|', $missingList) . ']';
         }
    }

    private function storeAdditional($argv ,$i) {
        foreach($argv as $j => $arg) {
            //echo "\n $j: $arg ";
            if($j > $i) {
                //echo "X";
                $this->altOptions[] = $arg;
            }
        }
    }

    /**
     * @param $name # contains name of argument $names
     * @param $arg # user given arg
     * @return bool
     *
     * checks if user arg is within injected options
     */
    private function isKeyInOption($name, $arg) {
        if (isset($this->options[$name])) {
            $options = $this->options[$name];
            foreach ($options as $option) {
                if ($option == $arg) {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /* traditional getters all return strings */
    public function getScript() {
        return $this->script;
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

    /* getter for get by name otions */
    public function get($key) {
        foreach ($this->names as $name) {
            if ($key == $name) {
                return $this->{$name};
            }
        }
    }

    public function getAltOptions() {
        return $this->altOptions;
    }

    /* gets array with values which are preconfigured as allowed */
    public function getOptions($key) {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        } else {
            return false;
        }
    }
}