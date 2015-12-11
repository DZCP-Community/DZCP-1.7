<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('INSTALLER')) exit();

if($_COOKIE['agb'] =! true)
    $index = show("/msg/agb_error");
else {
    $_SESSION['type'] = isset($_POST['type']) ? $_POST['type'] : $_SESSION['type'];
    if(function_exists('ftp_connect') && function_exists('ftp_login') && function_exists('ftp_site')) {
        FTP::init(); $jumplink = show("/msg/jumplink"); $ftp_port = 21; $main = ''; $core = ''; $next = false;
        $set_chmod_ftp = false; $disabled = ''; $nextlink = ''; $jumplink = ''; $success_status = '';
        $ftp_host = isset($_POST['host']) ? $_POST['host'] : 'localhost';
        $ftp_pfad = isset($_POST['pfad']) ? $_POST['pfad'] : '/';
        $ftp_user = isset($_POST['ftp_user']) ? $_POST['ftp_user'] : 'root';
        $ftp_pwd = isset($_POST['ftp_pwd']) ? $_POST['ftp_pwd'] : '';
        $ftp_ssl = isset($_POST['ftp_ssl']) ? $_POST['ftp_ssl'] : '';
        $_SESSION['ftp_host'] = ''; $_SESSION['ftp_pfad'] = '';
        $_SESSION['ftp_user'] = ''; $_SESSION['ftp_pwd'] = '';
        $_SESSION['ftp_port'] = ''; $_SESSION['ftp_ssl'] = '';

        if(isset($_GET['do']) && $_GET['do'] == 'check') {
            $ftp_host_array = explode(':', $ftp_host);
            if(count($ftp_host) >= 2)
            {
                $ftp_port = $ftp_host_array[1];
                $ftp_host_save = $ftp_host_array[0];
            } else
                $ftp_host_save = $ftp_host;

            FTP::set('host',$ftp_host_save);
            FTP::set('port',$ftp_port);
            FTP::set('user',$ftp_user);
            FTP::set('pass',$ftp_pwd);
            FTP::set('ssl', $ftp_ssl);

            if(FTP::connect()) {
                if(FTP::login()) {
                    $next = true;
                    FTP::move($ftp_pfad);
                    $dirs = FTP::nlist();
                    $check_list = array('index.php','thumbgen.php','_installer','admin','artikel','awards','away','banner','clankasse','clanwars','contact','downloads','forum',
                        'gallery','gb','glossar','impressum','inc','kalender','links','linkus','membermap','news','online','rankings','search','server',
                        'serverliste','shout','sites','sponsors','squads','stats','teamspeak','upload','user','votes');
                    foreach ($check_list as $list) {
                        $what = "Ordner:&nbsp;";
                        $exp = explode('.', str_replace('/', '', str_replace($ftp_pfad, '', $list)));
                        if(count($exp) >= 2)
                        { if($exp[1] == 'php' || $exp[1] == 'xml') $what = "Datei:&nbsp;"; }

                        if(array_var_exists($list, $dirs))
                            $main .= "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td width=\"90\"><font color='green'>"._true."<b>".$what."</b></font></td><td><font color='green'>".$ftp_pfad.'/'.$list."</font></td></tr></table>";
                        else {
                            $next = false;
                            $main .= "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td width=\"90\"><font color='red'>"._false."<b>".$what."</b></font></td><td><font color='red'>".$ftp_pfad.'/'.$list."</font><br /></td></tr></table>";
                        }
                    }

                    FTP::move($ftp_pfad.'/inc');
                    $dirs = FTP::nlist();
                    $check_list = array('_spiders.txt','_version.php','ajax.php','bbcode.php','bot.php','buffer.php','common.php','config.php','cookie.php','crypt.php',
                        'cryptkey.php','database.php','debugger.php','gameq.php','phpmailer.php','pop3.php','secure.php','sessions.php','settings.php','sfs.php','smtp.php',
                        'steamapi.php','teamspeak.php','_cache_','_logs','_templates_','additional-functions','additional-kernel','additional-languages','api','gameq','images',
                        'lang','menu-functions','phpfastcache','securimage','tinymce','tinymce_files');
                    foreach ($check_list as $list) {
                        $what = "Ordner:&nbsp;";
                        $exp = explode('.', str_replace('/', '', str_replace($ftp_pfad, '', $list)));
                        if(count($exp) >= 2)
                        { if($exp[1] == 'php') $what = "Datei:&nbsp;"; }

                        if(in_array($list, $dirs))
                            $core .= "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td width=\"90\"><font color='green'>"._true."<b>".$what."</b></font></td><td><font color='green'>".$ftp_pfad.'/inc/'.$list."</font></td></tr></table>";
                        else {
                            $next = false;
                            $core .= "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td width=\"90\"><font color='red'>"._false."<b>".$what."</b></font></td><td><font color='red'>".$ftp_pfad.'/inc/'.$list."</font><br /></td></tr></table>";
                        }
                    }
                } else
                    $success_status = writemsg(prepare_no_ftp_login,true);
            } else
                $success_status = writemsg(prepare_no_ftp_connect,true);
        }

        if($next) {
            $_SESSION['ftp_host'] = $ftp_host;
            $_SESSION['ftp_pfad'] = $ftp_pfad;
            $_SESSION['ftp_user'] = $ftp_user;
            $_SESSION['ftp_pwd']  = $ftp_pwd;
            $_SESSION['ftp_port'] = $ftp_port;
            $_SESSION['ftp_ssl']  = $ftp_ssl;

            $disabled = 'disabled="disabled"';
            $success_status = writemsg(ftp_files_success,false);
            $nextlink = show("/msg/nextlink",array("ac" => 'action=prepare'));
        }

        $index = show("ftp",array("disabled" => $disabled, "ssl" => ($ftp_ssl ? ' checked' : ''), "main" => $main, "core" => $core, "success_status" => $success_status, "next" => $nextlink, "jump" => $jumplink, "ftp_host" => $ftp_host, "ftp_pfad" => $ftp_pfad, "ftp_user" => $ftp_user, "ftp_pwd" => $ftp_pwd, "ftp_s" => ( $ftp_ssl ? 'checked="checked"' : '')));
    } else
        $index = show("ftp",array("disabled" => '', "ssl" => ($ftp_ssl ? ' checked' : ''), "main" => '', "core" => '', "success_status" => '', "next" => '', "jump" => $jumplink, "ftp_host" => '', "ftp_pfad" => '', "ftp_user" => '', "ftp_pwd" => ''));
}