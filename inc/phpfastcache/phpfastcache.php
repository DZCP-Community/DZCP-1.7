<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */
if(file_exists(basePath."/inc/phpfastcache/3.1.0/phpfastcache.php")) {
    require_once(basePath."/inc/phpfastcache/3.1.0/phpfastcache.php");
} else if(file_exists(basePath."/inc/phpfastcache/3.0.0/phpfastcache.php")) {
    require_once(basePath."/inc/phpfastcache/3.0.0/phpfastcache.php");
} else {
    require_once(basePath."/inc/phpfastcache/2.4.3/base.php");
}