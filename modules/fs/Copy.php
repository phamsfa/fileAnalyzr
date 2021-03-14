<?php
namespace vznrw\fs;
/**
 * Description of Copy
 *
 * @author hamsfa
 */
class Copy {
    private $toPath;
    private $file;
    private $inc = 0;
    private $maxLen = 222;
    private $conf;
    
    public function __construct($conf) {
        $this->conf = $conf->getConf('PATHS');
        $this->toPath = $this->conf->toPath;
        //echo "toPath = $this->toPath";
        
    }
    
    public function mv($item) {
	$this->file = $item['fName'];
        
	if($item['size'] !== 0) {
	    $newFile = $this->cleanPath($this->file);
	    echo 'c';
	    return $this->mkdir($newFile);
	} else {
	   echo '_';
	    return 'empty file';
	} 
    }
    public function test($item) {
        $newFile = $this->cleanPath($item['fName']);
        if(is_file($newFile)) {
            $sizeOrg = $item['size'];
            $sizeSaved = filesize($newFile);
            if($sizeOrg !== $sizeSaved) {
		if($sizeSaved === 0 || $sizeSaved < $sizeOrg) {
		    $this->mv($item);
		} else if ($sizeOrg === 0) {
		    file_put_contents('log', 'SIZE-NONE: '.$newFile." empty \n",FILE_APPEND);
		   echo '_';
		} else {
		   echo '=';
                   file_put_contents('log', 'SIZE-ODD: '.$newFile." ($sizeSaved/$sizeOrg)\n",FILE_APPEND);
                }
            }
        } else {
            $this->mv($item);
        }
    }
    private function cleanPath($file) {
	$arr = explode('/',$file);
	$arr[0] = $this->toPath;
	return join('/',$arr);
	
    }
    private function mkdir($newFile) {
        $ret = 0;
	$arr = explode('/',$newFile);
        
        foreach($arr as $key => $string) {
            $arr[$key] = $this->cutLongNames($string);
        }
        $file = array_pop($arr);
        $dirs = join('/',$arr);
        $myFile = $dirs.'/'.$file;
        if(!is_dir($dirs)) {
            echo "\n makeDir: ".$dirs.'\n';
            
            if(!mkdir($dirs,0777,true)) {
                file_put_contents('log', 'MKDIR-ERROR: '.$dirs."\n",FILE_APPEND);
                $ret++;
            }
        } 
        if(!is_file($myFile)) {
            if(!copy($this->file,$myFile)) {
                file_put_contents('log', ' COPY-ERROR: '.$myFile."\n",FILE_APPEND);
                $ret++;
            } 
        }
        return $ret;
    }
    private function cutLongNames($dir) {
        if(strlen($dir) > $this->maxLen) {
            $dir = substr($dir, 0, $this->maxLen).'__$'.$this->inc;
            $this->inc++;
        }
        return $dir;
    }
    public function delete($file) {
        $localPath = explode('/',$this->toPath);
        $empty = array_pop($localPath);
        
        $fileName = join('/',$localPath).$file->path.$file->file;
        if(is_file($fileName)) {
            //echo "\n DEL $fileName ";
            unlink($fileName);
        } else {
            echo "\n NOT FOUND $fileName ";
        }
        
    }
   
}
