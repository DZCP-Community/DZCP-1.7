<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('INSTALLER')) exit();

if($_COOKIE['agb'] =! true)
    $index = show("/msg/agb_error");
else {
    function installer_run() {
        if(!isset($_GET['ajax'])) {
            $nextlink = show("/msg/nextlink",array("ac" => 'action=mysql_setup_users', 'options' => 'disabled="disabled"')); $from = '<form action="" method="post" id="from"></from>';
            return '<table width="100%" cellpadding="3" cellspacing="1"><tr><td class="head">&raquo; Datenbank Installation</td>
                    </tr><tr><td class="head"><div id="mysql"><div style="width:100%;padding:10px 0;text-align:center"><p>Einen Moment bitte..<br>
                    <br /><img src="../inc/images/ajax-loader-bar.gif" alt="" /></p></div><script language="JavaScript" type="text/javascript">DZCP.initDynLoader();</script></div></td></tr></table>'.$from.$nextlink;
        } else {
            unset($_SESSION['mysql_password']);
            unset($_SESSION['mysql_user']);
            unset($_SESSION['mysql_prefix']);
            unset($_SESSION['mysql_database']);
            unset($_SESSION['mysql_host']);
            unset($_SESSION['mysql_engine']);
            unset($_SESSION['mysql_salt']);
            unset($_SESSION['mysql_engine']);
            sql_installer();
            $index = writemsg(mysql_setup_created,false);
            $index .= "<script language=\"JavaScript\" type=\"text/javascript\">DZCP.enable('NextSubmit');</script>";
            exit($index);
        }
    }

    if($_SESSION['type'] == 0)
        $index = installer_run();
    else {
        $msg = '';
        if(isset($_POST['update']) && !empty($_POST['version']))
            sql_installer(false,$_POST['version'],false);
        else if(isset($_POST['update']) && empty($_POST['version']))
            $msg = writemsg(no_db_update_selected,true);

        $settings_tb = db("SELECT * FROM `".dba::get('settings')."` WHERE `id` = 1 LIMIT 0 , 1",false,true);
        $version = versions((array_key_exists('db_version',$settings_tb) ? $settings_tb['db_version'] : false));
        $index = show("update_version",array("versions" => $version['version'], "msg" => (!empty($msg) ? $msg : $version['msg']) ,"disabled" => $version['disabled']));
    }
}