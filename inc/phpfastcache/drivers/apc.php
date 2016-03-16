<?php
/*
 * khoaofgod@gmail.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://faster.phpfastcache.com
 * Modified by Godkiller_NT (Hammermaps.de) for DZCP - deV!L`z ClanPortal 1.7.0
 */

class phpfastcache_apc extends BasePhpFastCache implements phpfastcache_driver {
    function checkdriver() {
        return (extension_loaded('apc') && ini_get('apc.enabled'));
    }

    function __construct($config = array()) {
        $this->setup($config);
        if(!$this->checkdriver() && !isset($config['skipError'])) {
            $this->cacheEnabled = false;
            return;
        }
        
        //Save Test
        if(!apc_exists('apc_test')) {
            apc_store('apc_test', 'test', 0.2);
            $apc_test = apc_fetch('apc_test');
            if($apc_test == 'test') {
                apc_delete('apc_test');
                $this->cacheEnabled = true;
                $this->cacheType = 'apc';
            } else {
                $this->cacheEnabled = false;
            }
        }
    }

    function driver_set($keyword, $value = "", $time = 300, $option = array() ) {
        if($this->cacheEnabled) {
            if(isset($option['skipExisting']) && $option['skipExisting'] == true) {
                return apc_add($keyword,$this->compress($value),$time);
            } else {
                return apc_store($keyword,$this->compress($value),$time);
            }
        }
    }

    function driver_get($keyword, $option = array()) {
        if($this->cacheEnabled) {
            $data = apc_fetch($keyword);
            if($data === false || empty($data)) {
                return null;
            }

            return $this->uncompress($data);
        }
    }

    function driver_delete($keyword, $option = array()) {
        if($this->cacheEnabled) {
            return apc_delete($keyword);
        }
    }

    function driver_stats($option = array()) {
        if($this->cacheEnabled) {
            $res = array("info" => "",
                         "size"  => "",
                         "data"  =>  "");

            try {
                $res['data'] = apc_cache_info("user");
            } catch(Exception $e) {
                $res['data'] =  array();
            }

            return $res;
        }
    }

    function driver_clean($option = array()) {
        if($this->cacheEnabled) {
            @apc_clear_cache();
            @apc_clear_cache("user");
        }
    }

    function driver_isExisting($keyword) {
        if($this->cacheEnabled) {
            return apc_exists($keyword);
        }
    }
}