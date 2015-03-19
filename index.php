<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

ob_start();
    define('basePath', dirname(__FILE__));
    if(!file_exists(basePath . "/inc/mysql.php")) {
        header('Location: _installer/index.php');
    } else {
        $global_index = true;
        include(basePath."/inc/common.php");
        header('Location: '.($chkMe ? startpage() : 'news/'));
    }
ob_end_flush();