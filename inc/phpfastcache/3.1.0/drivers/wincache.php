<?php
/*
 * khoaofgod@gmail.com
 * Website: http://www.phpfastcache.com
 * Example at our website, any bugs, problems, please visit http://faster.phpfastcache.com
 * Modified by Godkiller_NT (Hammermaps.de) for DZCP - deV!L`z ClanPortal 1.7.0
 */

class phpfastcache_wincache extends BasePhpFastCache implements phpfastcache_driver  {

    function checkdriver() {
        if(extension_loaded('wincache') && function_exists("wincache_ucache_set"))
        {
            return true;
        }
	    $this->fallback = true;
        return false;
    }

    function __construct($config = array()) {
        $this->setup($config);
        if(!$this->checkdriver() && !isset($config['skipError'])) {
            $this->fallback = true;
            $this->cacheEnabled = false;
        }

        $this->cacheEnabled = true;
        $this->cacheType = 'wincache';
    }

    function driver_set($keyword, $value = "", $time = 300, $option = array() ) {
        if(isset($option['skipExisting']) && $option['skipExisting'] == true) {
            return wincache_ucache_add($keyword, $this->compress($value), $time);
        } else {
            return wincache_ucache_set($keyword, $this->compress($value), $time);
        }
    }

    function driver_get($keyword, $option = array()) {
        // return null if no caching
        // return value if in caching

        $x = wincache_ucache_get($keyword,$suc);

        if($suc == false || empty($x)) {
            return null;
        } else {
            return $this->uncompress($x);
        }
    }

    function driver_delete($keyword, $option = array()) {
        return wincache_ucache_delete($keyword);
    }

    function driver_stats($option = array()) {
        $res = array(
            "info"  =>  "",
            "size"  =>  "",
            "data"  =>  wincache_scache_info(),
        );
        return $res;
    }

    function driver_clean($option = array()) {
        wincache_ucache_clear();
        return true;
    }

    function driver_isExisting($keyword) {
        if(wincache_ucache_exists($keyword)) {
            return true;
        } else {
            return false;
        }
    }



}