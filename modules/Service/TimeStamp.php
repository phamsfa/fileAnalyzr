<?php
namespace vznrw\service;

/**
 * handle of TimeStamp
 * no file: create
 * has file read / write
 * remember timestamp for last call
 * 
 * get()        [last saved timestamp + set readAt to tstamp of call]
 * set(opt)     [save actual or given timestamp]
 * reset()      [remove saved timestamp file]
 * writeLast()    [set saved timestamp to tstamp saved in readAt]
 *
 * @author hammesfahr
 */
class TimeStamp {
    /* saved tstamp */
    private $file;
    /* save tstamp of last call of get() */
    private $readAt;
    
    public function __construct($file) {
        $this->file = $file;
    }
    
    public function get() {
        
        if($this->has()) {
            return $this->last();
        } else {
            return "Error: Tstamp file not created";
            die();
        }
    }
    
    public function set($stmp = false) {
        if(!$stmp) {
            $stmp = $this->stamp();
        }
        $written = file_put_contents($this->file, $stmp);
        if($written == true) {
            return $stmp;
        } else {
            echo "Error: no Timestamp file saved";
            die();
        }
    }
    
    public function reset() {
        unlink($this->file);
    }
    
    public function writeLast() {
        $pass = false;
        if(isset($this->readAt)) {
            $pass = $this->readAt;
            $this->set($pass);
            unset($this->readAt);
        }
        return $pass;
    }
    /* P R I V A T E _____________________________________________ */
    private function stamp() {
        date_default_timezone_set("Europe/Berlin");
        return date("Y-m-d H:i:s");
    }
    
    private function has() {
        if(!is_file($this->file)) {
            touch($this->file);
            $this->set('0000-00-00 00:00:00');
            return is_file($this->file);
        } else {
            return true;
        }
    }
    
    private function last() {
        $this->rememberLast();
        return file_get_contents($this->file);
    }
    
    private function rememberLast() {
        if(!isset($this->readAt)) {
            $this->readAt = $this->stamp();
        }
    }
}
