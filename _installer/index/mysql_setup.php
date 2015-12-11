<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('INSTALLER')) exit();

if($_COOKIE['agb'] =! true)
    $index = show("/msg/agb_error");
else {
    $write=false;
    if(function_exists('file_put_contents') && function_exists('file_get_contents')) {
        if(write_sql_config()) {
            $write=true;
            $from = '<form action="" method="post" id="from"><from>';
            $nextlink = show("/msg/nextlink",array("ac" => 'action=mysql_setup_tb'));
            $salt = show("salt",array("salt" => $_SESSION['mysql_salt']));
            $index = writemsg(mysql_setup_saved,false);
            $index = $from.$index.$salt.$nextlink;
        }
    }

    if(!$write) {
        $sql_text = file_get_contents(basePath.'/_installer/system/sql_vorlage.txt');
        $sql_salt_text = file_get_contents(basePath.'/_installer/system/sql_salt_vorlage.txt');
        $var = array("{prefix}", "{host}", "{user}" ,"{pass}" ,"{db}","{salt}","{engine}");
        $data = array($_SESSION['mysql_prefix'], $_SESSION['mysql_host'], $_SESSION['mysql_user'], $_SESSION['mysql_password'], $_SESSION['mysql_database'], mkpwd(), $_SESSION['mysql_engine']);
        $index = show("/msg/mysql_setup_fail",array("text" => str_replace($var, $data, $sql_text), "text2" => str_replace($var, $data, $sql_text) ));
    }
}