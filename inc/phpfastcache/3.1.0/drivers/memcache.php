<?php
/*
 * khoaofgod@gmail.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://faster.phpfastcache.com
 * Modified by Godkiller_NT (Hammermaps.de) for DZCP - deV!L`z ClanPortal 1.7.0
 */

class phpfastcache_memcache extends BasePhpFastCache implements phpfastcache_driver {
    var $instant;

    function checkdriver() {
        if(function_exists("memcache_connect")) {
            return true;
        }
        
        $this->fallback = true;
        return false;
    }

    function __construct($config = array()) {
        $this->setup($config);
        if(!$this->checkdriver() && !isset($config['skipError'])) {
            $this->fallback = true;
        }
        
        if(class_exists("Memcache")) {
            $this->instant = new Memcache();
            $this->cacheEnabled = true;
            $this->cacheType = 'memcache';
        } else {
            $this->fallback = true;
            $this->cacheEnabled = false;
        }
    }

    function connectServer() {
        $server = $this->config['memcache'];
        if(count($server) < 1) {
            $server = array(array("127.0.0.1",11211));
        }

        foreach($server as $s) {
            $name = $s[0]."_".$s[1];
            if(!isset($this->checked[$name])) {
                try {
                    if(!$this->instant->addserver($s[0],$s[1])) {
                            $this->fallback = true;
                    }

                    $this->checked[$name] = 1;
                } catch(Exception $e) {
                    $this->fallback = true;
                }
            }
        }
    }

    function driver_set($keyword, $value = "", $time = 300, $option = array() ) {
        $this->connectServer();
        if($time>2592000) {
            $time = time()+$time;
        }

        if(isset($option['skipExisting']) && $option['skipExisting'] == true) {
            return $this->instant->add($keyword, $this->compress($value), false, $time );
        } else {
            return $this->instant->set($keyword, $this->compress($value), false, $time );
        }
    }

    function driver_get($keyword, $option = array()) {
        $this->connectServer();
        $x = $this->instant->get($keyword);
        return ($x == false ? null : $this->uncompress($x));
    }

    function driver_delete($keyword, $option = array()) {
        $this->connectServer();
        $this->instant->delete($keyword);
    }

    function driver_stats($option = array()) {
        $this->connectServer();
        return array("info"  => "",
                     "size"  =>  "",
                     "data"  => $this->instant->getStats());
    }

    function driver_clean($option = array()) {
        $this->connectServer();
        $this->instant->flush();
    }

    function driver_isExisting($keyword) {
        $this->connectServer();
        $x = $this->get($keyword);
        return ($x != null);
    }
}
