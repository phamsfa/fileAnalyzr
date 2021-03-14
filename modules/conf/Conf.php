<?php
namespace vznrw\config;
/*
 * reads the credentials and auto loader details from json
 */
class Conf 
{
    private $config;
    
    public function __construct($pref = '') {

        echo "\n__C_O_N_F_I_G_\n";
//        $path = explode('/', getcwd());
        echo getcwd();
        $paths = array("config/$pref.json", "config/local.json", "config/global.json"); 
        $this->config = new \stdClass();
        foreach($paths as $path) {
            if(is_file($path)) {
                echo "\n HAS FILE: $path";
                $this->read($path);
            } else {
                echo "\n NO FILE: $path";
            }
        }
        echo "\n______________\n";
    }

    private function read($path) {
        $lines =@ file($path);
        $conf = json_decode(
            join(' ',$lines)
        );
//        var_dump($conf);
        foreach($conf as $attrib => $value) {
//            echo "\n READ: $path ## $attrib ## VALUE: ";
//            print_r($value);
            $this->config->{$attrib} = $value;
        }
    }
    
    public function get($str)
    {
        if(property_exists($this->config, $str)) {

            return $this->config->{$str};

        } else {
            foreach($this->config as $k => $v) {
                echo "\n $k -> ";
            }

            echo ('NO item '.$str. ' in conf');
            die();
        }
    }
}
?>
