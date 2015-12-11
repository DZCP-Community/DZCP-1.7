<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('INSTALLER')) exit();

if($_COOKIE['agb'] =! true)
    $index = show("/msg/agb_error");
else {
    $set_chmod_ftp = false; $disabled = '';
    $ftp_host = isset($_SESSION['ftp_host']) ? $_SESSION['ftp_host'] : '';
    $ftp_pfad = isset($_SESSION['ftp_pfad']) ? $_SESSION['ftp_pfad'] : '';
    $ftp_user = isset($_SESSION['ftp_user']) ? $_SESSION['ftp_user'] : '';
    $ftp_pwd = isset($_SESSION['ftp_pwd']) ? $_SESSION['ftp_pwd'] : '';
    $ftp_ssl = isset($_SESSION['ftp_ssl']) ? $_SESSION['ftp_ssl'] : false;

    $array_script = array('admin','inc','inc/_cache_','inc/_logs','inc/images/newskat','inc/images/clanwars','inc/images/slideshow','inc/images/smileys',
                          'inc/images/squads','inc/images/tsviewer/custom_icons','inc/images/gameicons','inc/images/gameicons/custom','inc/images/maps',
                          'inc/images/smileys','inc/images/uploads','inc/images/uploads/useravatare','inc/images/uploads/usergallery','inc/images/uploads/userpics',
                          'inc/images/uploads/taktiken','inc/images/uploads/news','inc/images/uploads/artikel','gallery/images','banner/partners','banner/sponsors',
                          'inc/tinymce_files');
    $array_install = array('_installer','_installer/system/_logs');

    //Über FTP die Rechte setzen
    if(isset($_GET['do'])) {
        if($_GET['do'] == 'set_chmods') {
            if(!empty($_SESSION['ftp_host']) && !empty($_SESSION['ftp_pfad']) && !empty($_SESSION['ftp_user'])) {
                FTP::init(); $ftp_port = 21;
                $ftp_host_array = explode(':', $ftp_host);
                if(count($ftp_host) >= 2) {
                    $ftp_port = $ftp_host_array[1];
                    $ftp_host_save = $ftp_host_array[0];
                } else
                    $ftp_host_save = $ftp_host;

                FTP::set('host',$ftp_host_save);
                FTP::set('port',$ftp_port);
                FTP::set('user',$ftp_user);
                FTP::set('pass',$ftp_pwd);
                FTP::set('ssl' ,$ftp_ssl);

                if(FTP::connect()) {
                    if(FTP::login()) {
                        $next = true;
                        FTP::move($ftp_pfad);
                        asort($array_script);
                        foreach ($array_script as $list) {
                            FTP::chmod($list,'774');
                        }

                        asort($array_install);
                        foreach ($array_install as $list) {
                            FTP::chmod($list,'774');
                        }
                    } else
                        $success_status = writemsg(prepare_no_ftp_login,true);
                } else
                    $success_status = writemsg(prepare_no_ftp_connect,true);
            }
        }
    }

    //-> Check Installfiles
    $prepare_array_install = is_writable_array($array_install);

    //-> Check Scriptfiles
    $prepare_array_script = is_writable_array($array_script);

    $_SESSION['type'] = isset($_POST['type']) ? $_POST['type'] : $_SESSION['type'];

    //Schleife für Installationsdateien
    $install='';
    foreach($prepare_array_install['return'] as $get_check_result)
    { $install .= $get_check_result; }

    //Schleife für Scriptdateien
    $script='';
    foreach($prepare_array_script['return'] as $get_check_result)
    { $script .= $get_check_result; }

    if(empty($_SESSION['ftp_host']) || empty($_SESSION['ftp_pfad']) || empty($_SESSION['ftp_user']))
        $disabled = 'disabled="disabled"';

    if(!$set_chmod_ftp) {
        //Alle Dateien beschreibbar?
        if($prepare_array_script['status'] && $prepare_array_install['status']) {
            $disabled = 'disabled="disabled"';
            $success_status = writemsg(prepare_files_success,false);
            $nextlink = show("/msg/nextlink",array("ac" => 'action=mysql'));
        } else {
            if(empty($_SESSION['ftp_host']) || empty($_SESSION['ftp_pfad']) || empty($_SESSION['ftp_user']))
                $success_status = writemsg(prepare_files_error_non_ftpauto,true);
            else
                $success_status = writemsg(prepare_files_error,true);

            $nextlink = '';
        }
    }

    $index = show("prepare",array("script" => $script, "disabled" => $disabled, "install" => $install, "success_status" => $success_status, "next" => $nextlink, "ftp_host" => $ftp_host, "ftp_pfad" => $ftp_pfad, "ftp_user" => $ftp_user, "ftp_pwd" => $ftp_pwd));
}