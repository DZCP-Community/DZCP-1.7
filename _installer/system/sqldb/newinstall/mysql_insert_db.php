<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('INSTALLER')) exit();

//===============================================================
//Insert DZCP-Database MySQL Installer
//===============================================================

function install_mysql_insert($type=1) {
    global $database;
    ignore_user_abort(true);
    $sql = $database->getInstance();
    
    
    
    $sql->disconnect();
}