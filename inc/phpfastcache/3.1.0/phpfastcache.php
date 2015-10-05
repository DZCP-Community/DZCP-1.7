<?php
/*
 * khoaofgod@gmail.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://faster.phpfastcache.com
 * Modified by Godkiller_NT (Hammermaps.de) for DZCP - deV!L`z ClanPortal 1.7.0
 */

require_once(dirname(__FILE__)."/abstract.php");
require_once(dirname(__FILE__)."/driver.php");

if(!function_exists("phpFastCache")) {
    function phpFastCache($storage = "auto", $config = array()) {
        if(empty($config)) {
            $config = phpFastCache::$config;
        }
        
        if($storage == "" || strtolower($storage) == "auto") {
            $storage = phpFastCache::getAutoClass();
        }

        $instance = md5(json_encode($config).$storage);
        if(!isset(phpFastCache_instances::$instances[$instance])) {
            $class = "phpfastcache_".$storage;
            phpFastCache::required($storage);
            phpFastCache_instances::$instances[$instance] = new $class($config);
        }

        return phpFastCache_instances::$instances[$instance];
    }
}

class phpFastCache_instances {
    public static $instances = array();
}

// main class
class phpFastCache {
    public static $version = '3.1.0';
    public static $disabled = false;
    public static $config = array(
        "storage"       =>  "", // blank for auto
        "default_chmod" =>  0777, // 0777 , 0666, 0644
        "fallback"      => "files",
        "securityKey"   =>  "auto",
        "htaccess"      => true,
        "path"          =>  "",
        "memcache"      =>  array(
                array("127.0.0.1",11211,1)
                //  array("new.host.ip",11211,1),
        ),

        "redis" =>  array(
                "host"  => "127.0.0.1",
                "port"  =>  "",
                "password"  =>  "",
                "database"  =>  "",
                "timeout"   =>  ""
        ),

        "ssdb" =>  array(
                "host"  => "127.0.0.1",
                "port"  =>  8888,
                "password"  =>  "",
                "timeout"   =>  ""
        ));

    protected static $tmp = array();
    public static $use_fallback = false;
    public static $storage_fallback = '';
    public static $storage = '';
    var $instance,$fallback;

    function __construct($storage = "", $config = array()) {
        $config__ = phpFastCache::$config;
        if(!empty($config)) {
            $config__['storage'] = $storage;
        }
        
        if($storage == "" || strtolower($storage) == "auto") {
            $storage = self::getAutoClass();
        }

        self::$storage_fallback = phpFastCache::$config['fallback'];
        self::$storage = $storage;
        $this->fallback = phpFastCache(phpFastCache::$config['fallback'],$config);
        $this->instance = phpFastCache($storage,$config);
    }

    public function __call($name, $args) {
        if(!$this->instance->cacheEnabled) {
            self::$use_fallback = true;
            return call_user_func_array(array($this->fallback, $name), $args);
        }
        
        self::$use_fallback = false;
        return call_user_func_array(array($this->instance, $name), $args);
    }

    /*
     * Cores
     */
    public static function getAutoClass() {
        if(extension_loaded('apc') && ini_get('apc.enabled') && strpos(PHP_SAPI,"CGI") === false) {
            return "apc";
        }  elseif(extension_loaded('wincache') && function_exists("wincache_ucache_set")) {
            return "wincache";
        } elseif(extension_loaded('xcache') && function_exists("xcache_get")) {
            return "xcache";
        } else if(function_exists("memcache_connect") && count($this->config["memcache"])) {
            $config_mem = $this->config["memcache"];
            foreach($config_mem as $key) {
                if(ping_port($key[0], $key[1], 0.1)) {
                    return "memcache";
                }
            }
        } else if(class_exists("Redis")) {
            return "redis";
        } else if(class_exists("SimpleSSDB")) {
            return "ssdb";
        }
        
        return "files";
    }

    public static function getPath($skip_create_path = false, $config) {
        if ( !isset($config['path']) || $config['path'] == '' ) {
            if(self::isPHPModule()) {
                $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
                $path = $tmp_dir;
            } else {
                $path = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(GetServerVars('DOCUMENT_ROOT'),"/")."/../" : rtrim(dirname(__FILE__),"/")."/";
            }

            if(self::$config['path'] != "" && array_key_exists("path", $config)) {
                $path = $config['path'];
            }
        } else {
            $path = $config['path'];
        }

        $securityKey = "";
        if(array_key_exists("securityKey", $config)) {
            $securityKey = $config['securityKey'];
        }
        
        if($securityKey == "" || $securityKey == "auto") {
            $securityKey = self::$config['securityKey'];
            if($securityKey == "auto" || $securityKey == "") {
                $securityKey = isset($_SERVER['HTTP_HOST']) ? ltrim(strtolower(GetServerVars('HTTP_HOST')),"www.") : "default";
                $securityKey = preg_replace("/[^a-zA-Z0-9]+/","",$securityKey);
            }
        }
        
        if($securityKey != "") {
            $securityKey.= "/";
        }

        $full_path = $path."/".md5($securityKey);
        $full_pathx = md5($full_path);
        if($skip_create_path  == false && !isset(self::$tmp[$full_pathx])) {
            if(!@file_exists($full_path) || !@is_writable($full_path)) {
                if(!@file_exists($full_path)) {
                    @mkdir($full_path,self::__setChmodAuto($config));
                }
                if(!@is_writable($full_path)) {
                    @chmod($full_path,self::__setChmodAuto($config));
                }
                if(!@file_exists($full_path) || !@is_writable($full_path)) {
                    throw new Exception("PLEASE CREATE OR CHMOD ".$full_path." - 0777 OR ANY WRITABLE PERMISSION!",92);
                }
            }
            
            if(!array_key_exists("htaccess", $config)) {
                $config['htaccess'] = true;
            }
            
            self::$tmp[$full_pathx] = true;
            self::htaccessGen($full_path, $config['htaccess']);
        }

        return $full_path;
    }

    public static function __setChmodAuto($config) {
        if(!isset($config['default_chmod']) || $config['default_chmod'] == "" || is_null($config['default_chmod'])) {
            return 0777;
        } else {
            return $config['default_chmod'];
        }
    }

    protected static function getOS() {
        return array("os" => PHP_OS,
                     "php" => PHP_SAPI,
                     "system" => php_uname(),
                     "unique" => md5(php_uname().PHP_OS.PHP_SAPI));
    }

    public static function isPHPModule() {
        if(PHP_SAPI == "apache2handler") {
            return true;
        } else {
            if(strpos(PHP_SAPI,"handler") !== false) {
                return true;
            }
        }
        return false;
    }

    protected static function htaccessGen($path, $create = true) {
        if($create == true) {
            if(!is_writeable($path)) {
                try {
                    chmod($path,0777);
                }
                catch(Exception $e) {
                    throw new Exception("PLEASE CHMOD ".$path." - 0777 OR ANY WRITABLE PERMISSION!",92);
                }
            }
            if(!@file_exists($path."/.htaccess")) {
                if(!@file_put_contents($path."/.htaccess","order deny, allow \r\ndeny from all \r\nallow from 127.0.0.1")) {
                    throw new Exception("PLEASE CHMOD ".$path." - 0777 OR ANY WRITABLE PERMISSION!",92);
                }
            }
        }
    }

    public static function setup($name,$value = "") {
        if(is_array($name)) {
            self::$config = $name;
        } else {
            self::$config[$name] = $value;
        }
    }
    
    public static function required($class) {
        if(file_exists(dirname(__FILE__)."/drivers/".$class.".php")) {
            require_once(dirname(__FILE__)."/drivers/".$class.".php");
            return true;
        }
        
        return false;
    }
}