<?php
/*
 * khoaofgod@gmail.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://faster.phpfastcache.com
 * Modified by Godkiller_NT (Hammermaps.de) for DZCP - deV!L`z ClanPortal 1.7.0
 */

abstract class BasePhpFastCache {
    var $tmp = array();
    var $config = array();
    var $fallback = false;
    var $instant;
    var $cacheEnabled = false;
    var $cacheType = '';
    
    /* Basic Functions */
    public function set($keyword, $value = "", $time = 0, $option = array() ) {
        if((int)$time <= 0) {
            $time = 3600*24*365*5;
        }

        if(phpFastCache::$disabled === true) {
            return false;
        }
        
        $object = array("value" => $value,
                        "write_time"  => time(),
                        "expired_in"  => $time,
                        "expired_time"  => time() + (Int)$time);
       
        return $this->driver_set($keyword,$object,$time,$option);
    }

    public function get($keyword, $option = array()) {
        if(phpFastCache::$disabled === true) {
            return null;
        }

        $object = $this->driver_get($keyword,$option);
        if($object == null) {
            return null;
        }
		
        $value = isset( $object['value'] ) ? $object['value'] : null;
        return isset( $option['all_keys'] ) && $option['all_keys'] ? $object : $value;
    }


    function getInfo($keyword, $option = array()) {
        $object = $this->driver_get($keyword,$option);
        if($object == null) {
            return null;
        }
        
        return $object;
    }

    function delete($keyword, $option = array()) {
        return $this->driver_delete($keyword,$option);
    }

    function stats($option = array()) {
        return $this->driver_stats($option);
    }

    function clean($option = array()) {
        return $this->driver_clean($option);
    }

    function isExisting($keyword) {
        if(method_exists($this,"driver_isExisting")) {
            return $this->driver_isExisting($keyword);
        }

        $data = $this->get($keyword);
        if($data == null) {
            return false;
        } else {
            return true;
        }

    }

    function increment($keyword, $step = 1 , $option = array()) {
        $object = $this->get($keyword, array('all_keys' => true));
        if($object == null) {
            return false;
        } else {
            $value = (Int)$object['value'] + (Int)$step;
            $time = $object['expired_time'] - time();
            $this->set($keyword,$value, $time, $option);
            return true;
        }
    }

    function decrement($keyword, $step = 1 , $option = array()) {
        $object = $this->get($keyword, array('all_keys' => true));
        if($object == null) {
            return false;
        } else {
            $value = (Int)$object['value'] - (Int)$step;
            $time = $object['expired_time'] - time();
            $this->set($keyword,$value, $time, $option);
            return true;
        }
    }
    
    /* Extend more time */
    function touch($keyword, $time = 300, $option = array()) {
        $object = $this->get($keyword, array('all_keys' => true));
        if($object == null) {
            return false;
        } else {
            $value = $object['value'];
            $time = $object['expired_time'] - time() + $time;
            $this->set($keyword, $value,$time, $option);
            return true;
        }
    }
    
    public function setup($config_name,$value = "") {
        if(is_array($config_name)) {
            $this->config = $config_name;
        } else {
            $this->config[$config_name] = $value;
        }
    }

    /* Magic Functions */
    function __get($name) {
        return $this->get($name);
    }

    function __set($name, $v) {
        if(isset($v[1]) && is_numeric($v[1])) {
            return $this->set($name,$v[0],$v[1], isset($v[2]) ? $v[2] : array() );
        } else {
            throw new Exception("Example ->$name = array('VALUE', 300);",98);
        }
    }

    public function __call($name, $args) {
        return call_user_func_array( array( $this->instant, $name ), $args );
    }

    /* Base Functions */
    protected function backup() {
        return phpFastCache(phpFastCache::$config['fallback']);
    }

    /* return PATH for Files & PDO only */
    public function getPath($create_path = false) {
        return phpFastCache::getPath($create_path,$this->config);
    }

    /* Object for Files & SQLite */
    protected function encode($data) {
        return serialize($data);
    }

    protected function decode($value) {
        $x = @unserialize($value);
        if($x == false) {
            return $value;
        } else {
            return $x;
        }
    }

    /* Auto Create .htaccess to protect cache folder */
    protected function htaccessGen($path = "") {
        if($this->option("htaccess") == true) {
            if(!file_exists($path."/.htaccess")) {
                if(!file_put_contents($path."/.htaccess", "order deny, allow \r\ndeny from all \r\nallow from 127.0.0.1")) {
                    throw new Exception("Can't create .htaccess",97);
                }
            }
        }
    }

    /* Check phpModules or CGI */
    protected function isPHPModule() {
       return phpFastCache::isPHPModule();
    }
    
    public function isMemModule() {
        switch ($this->cacheType) {
            case 'apc': return true;
            case 'memcache': return true;
            case 'wincache': return true;
            case 'xcache': return true;
            case 'predis': return true;
            case 'redis': return true;
            case 'ssdb': return true;
            default: return false;
        }
    }

    protected function isExistingDriver($class) {
        if(file_exists(dirname(__FILE__)."/drivers/".$class.".php")) {
            require_once(dirname(__FILE__)."/drivers/".$class.".php");
            if(class_exists("phpfastcache_".$class)) {
                return true;
            }
        }

        return false;
    }

    /* Compress and Decompress data input */
    public function compress($input,$compress=true,$level=4) {
        $output = array('serialize' => '0', 'compress' => '0', 'data' => null);
        if(empty($input)) return $output;
        if(is_array($input) || is_object($input)) {
            $input = $this->encode($input);
            $output['serialize'] = '1';
        }

        if(function_exists('gzdeflate') && $compress) {
            $output['data'] = gzdeflate($input, $level);
            $output['compress'] = '1';
        } else {
            $output['data'] = $input;
        }

        return $output['serialize'].'|'.$output['compress'].'|'.$output['data'];
    }
    
    public function uncompress($input) {
        if(empty($input)) return null;
        $array = explode('|', $input, 3);
        $input = array();
        $input['serialize'] = intval($array[0]);
        $input['compress'] = intval($array[1]);
        $input['data'] = $array[2];
        if(function_exists('gzinflate') && $input['compress']) {
            $output = gzinflate($input['data']);
        } else { $output = $input['data']; }
        
        if($input['serialize']) { return $this->decode($output); } 
        else { return $output; }
    }

    protected function __setChmodAuto() {
        return phpFastCache::__setChmodAuto($this->config);
    }
}