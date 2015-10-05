<?php
/*
 * khoaofgod@gmail.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://faster.phpfastcache.com
 * Modified by Godkiller_NT (Hammermaps.de) for DZCP - deV!L`z ClanPortal 1.7.0
 */

class phpfastcache_files extends  BasePhpFastCache implements phpfastcache_driver  {
    function checkdriver() {
        if(is_writable($this->getPath())) {
            $this->cacheEnabled = true;
            return true;
        }
        
        $this->cacheEnabled = false;
        return false;
    }

    /*
     * Init Cache Path
     */
    function __construct($config = array()) {
        $this->setup($config);
        $this->getPath(); // force create path
        $this->cacheType = 'files';
        if(!$this->checkdriver() && !isset($config['skipError'])) {
            throw new Exception("Can't use this driver for your website!");
        }
    }

    /*
     * Return $FILE FULL PATH
     */
    private function getFilePath($keyword, $skip = false) {
        $path = $this->getPath();
        /* Skip Create Sub Folders */
        if($skip == false) {
            if(!@file_exists($path)) {
                if(!@mkdir($path,$this->__setChmodAuto())) {
                    throw new Exception("PLEASE CHMOD ".$this->getPath()." - 0777 OR ANY WRITABLE PERMISSION!",92);
                }

            } elseif(!is_writeable($path)) {
                if(!chmod($path,$this->__setChmodAuto())) {
                    throw new Exception("PLEASE CHMOD ".$this->getPath()." - 0777 OR ANY WRITABLE PERMISSION!",92);
                }
            }
        }

        return $path."/".md5($keyword).".bin";
    }

    function driver_set($keyword, $value = "", $time = 300, $option = array() ) {
        $file_path = $this->getFilePath($keyword);
        $data = $this->compress($value);
        $toWrite = true;
        /* Skip if Existing Caching in Options */
        if(isset($option['skipExisting']) && $option['skipExisting'] == true && @file_exists($file_path)) {
            $content = file_get_contents($file_path);
            $old = $this->uncompress($content);
            $toWrite = false;
            if($this->isExpired($old)) {
                $toWrite = true;
            }
        }

        if($toWrite == true) {
            try {
                file_put_contents($file_path, $data);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    function driver_get($keyword, $option = array()) {
        $file_path = $this->getFilePath($keyword);
        if(!@file_exists($file_path)) {
            return null;
        }

        $content = file_get_contents($file_path);
        $object = $this->uncompress($content);
        if($this->isExpired($object)) {
            @unlink($file_path);
            $this->auto_clean_expired();
            return null;
        }

        return $object;
    }

    function driver_delete($keyword, $option = array()) {
        $file_path = $this->getFilePath($keyword,true);
        return unlink($file_path);
    }

    /*
     * Return total cache size + auto removed expired files
     */
    function driver_stats($option = array()) {
        $res = array("info"  =>  "",
                     "size"  =>  "",
                     "data"  =>  "");

        $path = $this->getPath();
        $dir = @opendir($path);
        if(!$dir) {
            throw new Exception("Can't read PATH:".$path,94);
        }

        $total = 0;
        $removed = 0;
        while($file=@readdir($dir)) {
            if($file!="." && $file!=".." && is_dir($path."/".$file)) {
                // read sub dir
                $subdir = @opendir($path."/".$file);
                if(!$subdir) {
                    throw new Exception("Can't read path:".$path."/".$file,93);
                }

                while($f = @readdir($subdir)) {
                    if($f!="." && $f!="..") {
                        $file_path = $path."/".$file."/".$f;
                        $size = @filesize($file_path);
                        $object = $this->decode(file_get_contents($file_path));
                        if($this->isExpired($object)) {
                            @unlink($file_path);
                            $removed += $size;
                        }
                        $total += $size;
                    }
                } // end read subdir
            } // end if
       } // end while

       $res['size'] = $total - $removed;
       $res['info'] = array("Total [bytes]" => $total,
                            "Expired and removed [bytes]" => $removed,
                            "Current [bytes]" => $res['size']);
       return $res;
    }

    function auto_clean_expired() {
        $autoclean = $this->get("keyword_clean_up_driver_files");
        if($autoclean == null) {
            $this->set("keyword_clean_up_driver_files",3600*24);
            $this->stats();
        }
    }

    function driver_clean($option = array()) {
        $path = $this->getPath();
        if($files = get_files($path.'/',false,true,array('bin'))) {
            foreach($files AS $file) { 
                @unlink($path.'/'.$file);
            }
        }
    }

    function driver_isExisting($keyword) {
        $file_path = $this->getFilePath($keyword,true);
        if(!@file_exists($file_path)) {
            return false;
        } else {
            $value = $this->get($keyword);
            return ($value == null ? false : true);
        }
    }

    function isExpired($object) {
        return (isset($object['expired_time']) && time() >= $object['expired_time']);
    }
}