<?php

namespace vznrw\Service;

/**
 * adds method to auto fill object 
 * with available Fields from array
 *
 * @author hammesfahr
 */
class AutoFill {
    public function exchangeArray($data) {

        foreach($data as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function set($key, $value) {
        
        if(property_exists($this,$key)) {
//            $this->{$key} = ($value || $value === 0) ? $value : NULL;
            $this->{$key} = $value;
        }
    }

    public function get($attribute) {
        if(property_exists($this, $attribute)) {
            return $this->{$attribute};
        } else {
            return NULL;
        }
    }

    public function keys($proust = false) {
        $arr = array();
        foreach($this->getArrayCopy() as $col => $val) {
            if($this->havingKey($col, $val, $proust)) {
                $arr[$col] = $val; 
            }
        }
        return $arr;
    }

    public function havingKey($col, $val, $proust) {
        $IdCol = (!$proust) ? 'FK' : 'ID';
        $keyArr = explode('_',$col);
        foreach($keyArr as $seg) {
            if($seg === $IdCol && ($val!== '' && !is_null($val))) {
                return true;
            }
        }
        return false;
    }

    public function enc($data,$enc = false) {
        foreach($data as $key => $value) {
            $data[$key] = ($enc) ? utf8_encode($value) : utf8_decode($value);
        }
        return $data;
    }
}
