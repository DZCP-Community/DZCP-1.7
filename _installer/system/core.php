<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

$_SESSION['installer'] = true;
$host = str_replace('www.','',$_SERVER['HTTP_HOST']);

if((isset($_GET['action']) ? $_GET['action'] : '') == 'mysql_setup_tb')
    $_SESSION['db_install'] = true;

require_once(basePath."/inc/common.php");
require_once(basePath."/inc/_version.php");
require_once(basePath.'/_installer/system/ftp.php');
require_once(basePath.'/_installer/system/global.php');
require_once(basePath.'/_installer/system/emlv.php');

//-> Sichert die ausgelagerten Dateien gegen directe Ausführung
define('INSTALLER', true);

//-> Generiert die installations Schritte
function steps() {
    $lizenz = ''; $type = ''; $prepare = ''; $mysql = '';
    $db = ''; $update = ''; $adminacc = ''; $done = ''; $ftp = '';

    switch($_SESSION['setup_step']):
        default:
            $lizenz = show(_step,array("text" => _link_start));
            $type = show(_step,array("text" => _link_type_1));
        break;
        case 'installtype'; //Auswahl: Installation / Update
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type));
        break;
        case'prepare'; //Schreibrechte PrÃ¼fen
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type_1));
            $ftp = show(_step,array("text" => _link_ftp_1));
            $prepare = show(_step,array("text" => _link_prepare));
            $mysql = show(_step,array("text" => _link_mysql_1));
            $db = show(_step,array("text" => _link_db_1));

            if($_SESSION['type'] == 1)
                $update = show(_step,array("text" => _link_update_1));
            else
                $adminacc = show(_step,array("text" => _link_adminacc_1));

            $done = show(_step,array("text" => _link_done_1));
        break;
        case'ftp'; //Schreibrechte PrÃ¼fen
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type_1));
            $ftp = show(_step,array("text" => _link_ftp));
            $prepare = show(_step,array("text" => _link_prepare_1));
            $mysql = show(_step,array("text" => _link_mysql_1));
            $db = show(_step,array("text" => _link_db_1));

            if($_SESSION['type'] == 1)
                $update = show(_step,array("text" => _link_update_1));
            else
                $adminacc = show(_step,array("text" => _link_adminacc_1));

            $done = show(_step,array("text" => _link_done_1));
        break;
        case'mysql'; //MySQL Verbindung abfragen & herstellen
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type_1));
            $ftp = show(_step,array("text" => _link_ftp_1));
            $prepare = show(_step,array("text" => _link_prepare_1));
            $mysql = show(_step,array("text" => _link_mysql));
            $db = show(_step,array("text" => _link_db_1));

            if($_SESSION['type'] == 1)
                $update = show(_step,array("text" => _link_update_1));
            else
                $adminacc = show(_step,array("text" => _link_adminacc_1));

            $done = show(_step,array("text" => _link_done_1));
        break;
        case'mysql_setup'; //MySQL Config schreiben
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type_1));
            $ftp = show(_step,array("text" => _link_ftp_1));
            $prepare = show(_step,array("text" => _link_prepare_1));
            $mysql = show(_step,array("text" => _link_mysql_1));
            $db = show(_step,array("text" => _link_db));

            if($_SESSION['type'] == 1)
                $update = show(_step,array("text" => _link_update_1));
            else
                $adminacc = show(_step,array("text" => _link_adminacc_1));

            $done = show(_step,array("text" => _link_done_1));
        break; //Tabellen anlegen
        case 'mysql_setup_tb';
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type_1));
            $ftp = show(_step,array("text" => _link_ftp_1));
            $prepare = show(_step,array("text" => _link_prepare_1));
            $mysql = show(_step,array("text" => _link_mysql_1));
            $db = show(_step,array("text" => _link_db));

            if($_SESSION['type'] == 1)
                $update = show(_step,array("text" => _link_update));
            else
                $adminacc = show(_step,array("text" => _link_adminacc_1));

            $done = show(_step,array("text" => _link_done_1));
        break;
        case 'mysql_setup_users'; //Administrator anlegen,Erste Konfiguration etc.
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type_1));
            $ftp = show(_step,array("text" => _link_ftp_1));
            $prepare = show(_step,array("text" => _link_prepare_1));
            $mysql = show(_step,array("text" => _link_mysql_1));
            $db = show(_step,array("text" => _link_db_1));
            $adminacc = show(_step,array("text" => _link_adminacc));
            $done = show(_step,array("text" => _link_done_1));
        break;
        case 'done';
            $lizenz = show(_step,array("text" => _link_start_1));
            $type = show(_step,array("text" => _link_type_1));
            $ftp = show(_step,array("text" => _link_ftp_1));
            $prepare = show(_step,array("text" => _link_prepare_1));
            $mysql = show(_step,array("text" => _link_mysql_1));
            $db = show(_step,array("text" => _link_db_1));

            if($_SESSION['type'] == 1)
                $update = show(_step,array("text" => _link_update_1));
            else
                $adminacc = show(_step,array("text" => _link_adminacc_1));

            $done = show(_step,array("text" => _link_done));
        break;
    endswitch;

    return $lizenz.$type.$ftp.$prepare.$mysql.$db.$update.$adminacc.$done;
}

//-> Prüft MySQL Server auf Aria Erweiterung
function check_db_aria($con) {
    $engines = $con->show("SHOW ENGINES;");
    foreach ($engines as $engine) {
        if(strtolower($engine['Engine']) == 'aria') {
            return true;
        }
    }
        
    return false;
}

//-> Nachrichten ausgeben
function writemsg($stg='',$error=false, $warn=false) {
    if($error)
        return show("/msg/msg_error",array("error" => _error, "msg" => $stg));
    else if($warn)
        return show("/msg/msg_warn",array("warn" => _warn, "msg" => $stg));
    else
        return show("/msg/msg_successful",array("successful" => _successful, "msg" => $stg));
}

//-> Schreibe Datenbank
function sql_installer($insert=false,$db_infos=array(),$newinstall=true) {
    if($newinstall) {
        require_once(basePath.'/_installer/system/sqldb/newinstall/mysql_create_tb.php');
        require_once(basePath.'/_installer/system/sqldb/newinstall/mysql_insert_db.php');
        ($insert ? install_mysql_insert($db_infos) : install_mysql_create());
    } else {
        $versions = array();
        if($files = get_files(basePath.'/_installer/system/sqldb/update/',false,true,array('php'))) {
            $updates_array=array();
            foreach($files as $update)
            { require_once(basePath.'/_installer/system/sqldb/update/'.$update); }
        }

        //-> Updates Sortieren
        foreach($versions as $res)
        $sort[] = $res['update_id'];
        array_multisort($sort, SORT_ASC, $versions);

        if($db_infos!=0) {
            foreach($versions as $version_array) {
                $result = array_search($db_infos, $version_array, true); //Suche
                if($result!='')
                break;
            }

            for($i = ($result-1); $i < count($versions); $i++) {
                if($versions[$i]['call'] != false && function_exists($func=('install_'.$versions[$i]['call'].'_update')))
                    @call_user_func($func);
            }
        }

        header('Location: index.php?action=done');
    }
}

//-> Eine Liste der Versionen anzeigen
function versions($detect=false) {
    $versions = array(); $show='';
    if($files = get_files(basePath.'/_installer/system/sqldb/update/',false,true,array('php'))) {
        foreach($files as $update)
        { require_once(basePath.'/_installer/system/sqldb/update/'.$update); }
    }

    //-> Liste ausgeben
    $count = count($versions);
    foreach($versions as $id => $version) {
        $checked = ''; $disabled = '';
        if($detect) {
            if($version['dbv'] == $detect && $version['dbv'] != false)
                $checked = 'checked="checked"';
            else {
                $count--;
                $disabled = 'disabled="disabled"';
            }
        }

        $show .= show(version_input,array("version_num" => $version[$version['update_id']], "version_num_view" => $version['version_list'], "checked" => $checked, "disabled" => $disabled));
    }


    return array('version' => $show, 'msg' => (!$count ? writemsg(no_db_update,false,true) : ''), 'disabled' => (!$count ? 'disabled="disabled"' : ''));
}

//-> Schreibe Inhalt in die "mysql.php"
function write_sql_config() {
    $stream_sql = file_get_contents(basePath.'/_installer/system/sql_vorlage.txt');
    $stream_salt = file_get_contents(basePath.'/_installer/system/sql_salt_vorlage.txt');
    $var = array("{prefix}", "{host}", "{user}" ,"{pass}" ,"{db}","{salt}","{engine}");
    $data = array($_SESSION['mysql_prefix'], $_SESSION['mysql_host'], $_SESSION['mysql_user'], $_SESSION['mysql_password'], $_SESSION['mysql_database'], $salt=mkpwd(), $_SESSION['mysql_engine']);
    $_SESSION['mysql_salt'] = str_replace($var, $data, $stream_salt);
    file_put_contents(basePath.'/inc/mysql1.php', str_replace($var, $data, $stream_sql));
    file_put_contents(basePath.'/inc/mysql_salt1.php', str_replace($var, $data, $stream_salt));
    unset($stream_sql,$stream_salt);
    return (file_exists(basePath.'/inc/mysql1.php') && file_exists(basePath.'/inc/mysql_salt1.php'));
}

//-> Prüft ob Datei existiert und ob auf ihr geschrieben werden kann
function is_writable_array($array) {
    $i=0; $data=array(); $status=true;
    foreach($array as $file) {
        $what = "Ordner:&nbsp;";

        if(is_file('../'.$file))
            $what = "Datei:&nbsp;";

        $_file = preg_replace("#\.\.#Uis", "", '../'.$file);
        $file_chmod = getFilePermission(basePath.'/'.$file);
        if(is_writable(basePath.'/'.$file) && in_array($file_chmod, array('774','666','777','770')))
            $data[$i] = "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td width=\"90\"><font color='green'>"._true."<b>".$what."</b></font></td><td><font color='green'>".$_file."</font> (CHMOD ".$file_chmod.")</td></tr></table>";
        else {
            $data[$i] = "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td width=\"90\"><font color='red'>"._false."<b>".$what."</b></font></td><td><font color='red'>".$_file."</font> (CHMOD ".$file_chmod.") <br /></td></tr></table>";
            $status=false;
        }

        $i++;
    }

    return array('return' => $data, 'status' => $status);
}

function getFilePermission($file) {
        $length = strlen(decoct(fileperms($file)))-3;
        return substr(decoct(fileperms($file)),$length);
}

function is_php($version='5.3.0') { 
    return (floatval(phpversion()) >= $version); 
}

/**
 * Funktion um eine Variable prüfung in einem Array durchzuführen
 *
 * @return boolean
 */
function array_var_exists($var,$search)
{ foreach($search as $key => $var_) { if($var_==$var) return true; } return false; }

/**
 * Funktion um notige Erweiterungen zu prufen
 * @return boolean
 **/
function fsockopen_support() {
    return ((!fsockopen_support_bypass && (disable_functions('fsockopen') || disable_functions('fopen'))) ? false : true);
}

function disable_functions($function='') {
    if (!function_exists($function)) { return true; }
    $disable_functions = ini_get('disable_functions');
    if (empty($disable_functions)) { return false; }
    $disabled_array = explode(',', $disable_functions);
    foreach ($disabled_array as $disabled) {
        if (strtolower(trim($function)) == strtolower(trim($disabled))) {
            return true;
        }
    }

    return false;
}

function allow_url_fopen_support() {
    return (ini_get('allow_url_fopen') == 1);
}

function ping_port($address='',$port=0000,$timeout=2,$udp=false) {
    if (!fsockopen_support()) {
        return false;
    }

    $errstr = NULL; $errno = NULL;
    if(!$ip = DNSToIp($address)) {
        return false;
    }

    if($fp = @fsockopen(($udp ? "udp://".$ip : $ip), $port, $errno, $errstr, $timeout)) {
        unset($ip,$port,$errno,$errstr,$timeout);
        fclose($fp);
        return true;
    }

    return false;
}

function DNSToIp($address='') {
    if (!preg_match('#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#', $address)) {
        if (!($result = gethostbyname($address))) {
            return false;
        }

        if ($result === $address) {
            $result = false;
        }
    } else {
        $result = $address;
    }

    return $result;
}

//-> Funktion um Passwoerter generieren zu lassen
function mkpwd($passwordLength=12,$specialcars=true) {
    $passwordComponents = array("ABCDEFGHIJKLMNOPQRSTUVWXYZ" , "abcdefghijklmnopqrstuvwxyz" , "0123456789" , "#$@!");
    $componentsCount = count($passwordComponents);

    if(!$specialcars && $componentsCount == 4) {
        unset($passwordComponents[3]);
        $componentsCount = count($passwordComponents);
    }

    shuffle($passwordComponents); $password = '';
    for ($pos = 0; $pos < $passwordLength; $pos++) {
        $componentIndex = ($pos % $componentsCount);
        $componentLength = strlen($passwordComponents[$componentIndex]);
        $random = rand(0, $componentLength-1);
        $password .= $passwordComponents[$componentIndex]{ $random };
    }

    unset($random,$componentLength,$componentIndex);
    return $password;
}