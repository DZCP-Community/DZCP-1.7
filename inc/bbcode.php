<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## INCLUDES/REQUIRES ##
require_once(basePath.'/inc/_version.php');
require_once(basePath.'/inc/pop3.php');
require_once(basePath.'/inc/smtp.php');
require_once(basePath.'/inc/phpmailer.php');
require_once(basePath."/inc/cookie.php");
require_once(basePath.'/inc/gameq.php');
require_once(basePath."/inc/teamspeak.php");
require_once(basePath."/inc/phpfastcache/phpfastcache.php");
require_once(basePath.'/inc/steamapi.php');
require_once(basePath.'/inc/sfs.php');
require_once(basePath.'/inc/securimage/securimage_color.php');
require_once(basePath.'/inc/securimage/securimage.php');
require_once(basePath.'/inc/settings.php');

## Is AjaxJob ##
$ajaxJob = (!isset($ajaxJob) ? false : $ajaxJob);

//Cache Config
$config_cache['default_chmod'] = 0777; 
$config_cache['htaccess'] = true;
$config_cache['path'] = basePath."/inc/_cache_";
$config_cache['securityKey'] = "auto";
$config_cache['fallback'] = "files";
if (!is_dir($config_cache['path'])) { //Check cache dir
    mkdir($config_cache['path'], $config_cache['default_chmod'], true);
}

//Auto Update Detect
/*
if(file_exists(basePath."/_installer/index.php") && !view_error_reporting && !$thumbgen && !$ajaxJob) {
    $sqlqry = $sql->select('SHOW TABLE STATUS'); $table_data = array();
    foreach($sqlqry as $table) { 
        $table_data[$table['Name']] = true; 
    }

    if(!array_key_exists($db['autologin'],$table_data) && !$installer)
        $global_index ? header('Location: _installer/update.php') :
        header('Location: ../_installer/update.php');
    unset($user_check);
} */

$config_cache['securityKey'] = settings::get('prev');
if(empty($config_cache['storage']) || $config_cache['storage'] == 'auto') {
    $config_cache['storage'] = settings::get('cache_engine');
    if(settings::get('cache_engine') == 'memcache') {
        $config_cache['memcache'] = array(array(stringParser::decode(settings::get('memcache_host')),
            intval(settings::get('memcache_port')),1));
    }
}


phpFastCache::setup($config_cache);
$cache = new phpFastCache();
phpFastCache::$disabled = !$config_cache['use_cache'];
$securimage = new Securimage();
dbc_index::init();
settings::load();

//-> Automatische Datenbank Optimierung
if(!$ajaxJob && auto_db_optimize && settings::get('db_optimize') < time()) {
    @ignore_user_abort(true);
    settings::set('db_optimize', (time()+auto_db_optimize_interval));
    db_optimize(); settings::load(true);
    $sql->insert("INSERT INTO `{prefix_ipcheck}` SET `ip` = ?, `user_id` = ?, `what` = 'db_optimize()', `time` = ?;",array('0.0.0.0',intval(userid()),time()));
    @ignore_user_abort(false);
}

//-> Cookie initialisierung
cookie::init('dzcp_'.settings::get('prev'));

//-> SteamAPI
SteamAPI::set('apikey', stringParser::decode(settings::get('steam_api_key')));

//-> GameQ
spl_autoload_register(array('GameQ', 'auto_load'));

//-> Language auslesen
$language = (cookie::get('language') != false ? (file_exists(basePath.'/inc/lang/languages/'.cookie::get('language').'.php') ? cookie::get('language') :stringParser::decode(settings::get('language'))) :stringParser::decode(settings::get('language')));

//-> einzelne Definitionen
$isSpider = isSpider();
$subfolder = basename(dirname(dirname(GetServerVars('PHP_SELF')).'../'));
$httphost = GetServerVars('HTTP_HOST').(empty($subfolder) ? '' : '/'.$subfolder);
$domain = str_replace('www.','',$httphost);
$pagetitle = stringParser::decode(settings::get('pagetitel'));
$sdir = stringParser::decode(settings::get('tmpdir'));
$useronline = 1800;
$reload = 3600 * 24;
$picformat = array("jpg", "gif", "png");
$userip = visitorIp();
$maxpicwidth = 90;
$maxadmincw = 10;
$maxfilesize = @ini_get('upload_max_filesize');
$UserAgent = trim(GetServerVars('HTTP_USER_AGENT'));

//JavaScript
javascript::set('AnchorMove','');
javascript::set('debug',(view_error_reporting && view_javascript_debug));

//-> Global
$action = isset($_GET['action']) ? secure_global_imput($_GET['action']) : (isset($_POST['action']) ? secure_global_imput($_POST['action']) : 'default');
$page = isset($_GET['page']) ? intval(trim($_GET['page'])) : (isset($_POST['page']) ? intval(trim($_POST['page'])) : 1);
$do = isset($_GET['do']) ? secure_global_imput($_GET['do']) : (isset($_POST['do']) ? secure_global_imput($_POST['do']) : '');
$index = ''; $show = ''; $color = 0;

//-> Neue Kernel Funktionen einbinden, sofern vorhanden
if($functions_files = get_files(basePath.'/inc/additional-kernel/',false,true,array('php'))) {
    foreach($functions_files AS $func) { 
        include(basePath.'/inc/additional-kernel/'.$func); 
    } unset($functions_files,$func);
}

/**
 * Pruft eine IP gegen eine IP-Range
 * @param ipv4 $ip
 * @param ipv4 range $range
 * @return boolean
 */
function validateIpV4Range ($ip, $range) {
    if(!is_array($range)) {
        $counter = 0;
        $tip = explode ('.', $ip);
        $rip = explode ('.', $range);
        foreach ($tip as $targetsegment) {
            $rseg = $rip[$counter];
            $rseg = preg_replace ('=(\[|\])=', '', $rseg);
            $rseg = explode ('-', $rseg);
            if (!isset($rseg[1])) {
                $rseg[1] = $rseg[0];
            }

            if ($targetsegment < $rseg[0] || $targetsegment > $rseg[1]) {
                return false;
            }
            $counter++;
        }
    } else {
        foreach ($range as $range_num) {
            $counter = 0;
            $tip = explode ('.', $ip);
            $rip = explode ('.', $range_num);
            foreach ($tip as $targetsegment) {
                $rseg = $rip[$counter];
                $rseg = preg_replace ('=(\[|\])=', '', $rseg);
                $rseg = explode ('-', $rseg);
                if (!isset($rseg[1])) {
                    $rseg[1] = $rseg[0];
                }

                if ($targetsegment < $rseg[0] || $targetsegment > $rseg[1]) {
                    return false;
                }
                $counter++;
            }
        }
    }

    return true;
}

// -> Pruft ob die IP gesperrt und gultig ist
function check_ip() {
    global $sql,$userip,$UserAgent;
    if(!dbc_index::issetIndex('ip_check')) {
        dbc_index::setIndex('ip_check', array());
    }
    
    if(!isIP($userip, true)) {
        if((!isIP($userip) && !isIP($userip,true)) || $userip == false || empty($userip)) {
            dzcp_session_destroy();
            die('Deine IP ist ung&uuml;ltig!<p>Your IP is invalid!');
        }
        
        if(empty($UserAgent)) {
            dzcp_session_destroy();
            die("Script wird nicht ausgef&uuml;hrt, da kein User Agent &uuml;bermittelt wurde.\n");
        }
        
        //Banned IP
        if(!dbc_index::getIndexKey('ip_check', md5($userip))) {
            $ips = dbc_index::getIndex('ip_check');
            foreach($sql->select("SELECT `id`,`typ`,`data` FROM `{prefix_ipban}` WHERE `ip` = ? AND `enable` = 1;",array($userip)) as $banned_ip) {
                if($banned_ip['typ'] == 2 || $banned_ip['typ'] == 3) {
                    dzcp_session_destroy();
                    $banned_ip['data'] = unserialize($banned_ip['data']);
                    die('Deine IP ist gesperrt!<p>Your IP is banned!<p>MSG: '.$banned_ip['data']['banned_msg']);
                }
            }
            unset($banned_ip);

            if(allow_url_fopen_support() && isIP($userip) && !validateIpV4Range($userip, '[192].[168].[0-255].[0-255]') && 
            !validateIpV4Range($userip, '[127].[0].[0-255].[0-255]') && 
            !validateIpV4Range($userip, '[10].[0-255].[0-255].[0-255]') && 
            !validateIpV4Range($userip, '[172].[16-31].[0-255].[0-255]')) {
                sfs::check(); //SFS Update
                if(sfs::is_spammer()) {
                    $sql->delete("DELETE FROM `{prefix_iptodns}` WHERE `sessid` = ?;",
                            array(session_id()));
                    dzcp_session_destroy();
                    die('Deine IP-Adresse ist auf <a href="http://www.stopforumspam.com/" target="_blank">http://www.stopforumspam.com/</a> gesperrt, die IP wurde zu oft fÃ¼r Spam Angriffe auf Webseiten verwendet.<p>
                         Your IP address is known on <a href="http://www.stopforumspam.com/" target="_blank">http://www.stopforumspam.com/</a>, your IP has been used for spam attacks on websites.');
                }
            }
            
            $ips[md5($userip)] = true;
            dbc_index::setIndex('ip_check', $ips, 30);
        }
    }
}

check_ip(); // IP Prufung * No IPV6 Support *
function dzcp_session_destroy() {
    $_SESSION['id']        = '';
    $_SESSION['pwd']       = '';
    $_SESSION['ip']        = '';
    $_SESSION['lastvisit'] = '';
    $_SESSION['akl_id']    = 0;
    session_unset();
    session_destroy();
    session_regenerate_id();
    cookie::clear();
}

//-> Auslesen der Cookies und automatisch anmelden
if(cookie::get('id') != false && cookie::get('pkey') != false && empty($_SESSION['id']) && !checkme()) {
    //-> Permanent Key aus der Datenbank suchen
    $get_almgr = $sql->fetch("SELECT `id`,`uid`,`update`,`expires` FROM `{prefix_autologin}` WHERE `pkey` = ? AND `uid` = ?;",array(cookie::get('pkey'), cookie::get('id')));
    if($sql->rowCount()) {
        if((!$get_almgr['update'] || (time() < ($get_almgr['update'] + $get_almgr['expires'])))) {
            //-> User aus der Datenbank suchen
            $get = $sql->fetch("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`time` FROM `{prefix_users}` WHERE `id` = ? AND `level` != 0;",array(cookie::get('id')));
            if($sql->rowCount()) {
                //-> Generiere neuen permanent-key
                $permanent_key = md5(mkpwd(8));
                cookie::put('pkey', $permanent_key);
                cookie::save();
                
                //Update Autologin
                $sql->update("UPDATE `{prefix_autologin}` SET `ssid` = ?, `pkey` = ?, `ip` = ?, `host` = ?, `update` = ?, `expires` = ? WHERE `id` = ?;",
                array(session_id(),$permanent_key,$userip,gethostbyaddr($userip),time(),autologin_expire,$get_almgr['id']));

                //-> Schreibe Werte in die Server Sessions
                $_SESSION['id']         = $get['id'];
                $_SESSION['pwd']        = $get['pwd'];
                $_SESSION['lastvisit']  = $get['time'];
                $_SESSION['ip']         = $userip;

                if (data("ip", $get['id']) != $_SESSION['ip']) {
                    $_SESSION['lastvisit'] = data("time", $get['id']);
                }

                if (empty($_SESSION['lastvisit'])) {
                    $_SESSION['lastvisit'] = data("time", $get['id']);
                }

                //-> Aktualisiere Datenbank
                $sql->update("UPDATE `{prefix_users}` SET `online` = 1, `sessid` = ?, `ip` = ? WHERE `id` = ?;",
                array(session_id(),$userip,$get['id']));

                //-> Aktualisiere die User-Statistik
                $sql->update("UPDATE `{prefix_userstats}` SET `logins` = logins+1 WHERE `user` = ?;",array($get['id']));

                //-> Aktualisiere Ip-Count Tabelle
                foreach($sql->select("SELECT `id` FROM `{prefix_clicks_ips}` WHERE `ip` = ? AND `uid` = 0;",array($userip)) as $get_ci) {
                    $sql->update("UPDATE `{prefix_clicks_ips}` SET `uid` = ? WHERE `id` = ?;",array($get['id'],$get_ci['id']));
                }

                unset($get,$permanent_key,$get_almgr,$get_ci); //Clear Mem
            } else {
                dzcp_session_destroy();
                $_SESSION['id']        = '';
                $_SESSION['pwd']       = '';
                $_SESSION['ip']        = '';
                $_SESSION['lastvisit'] = '';
                $_SESSION['pkey']      = '';
                $_SESSION['akl_id']    = 0;
            }
        } else {
            $sql->delete("DELETE FROM `{prefix_autologin}` WHERE `id` = ?;",array($get_almgr['id']));
            dzcp_session_destroy();
        }
    }
}

//Demo Mode
if(dzcp_demo) {
    if(!empty($_SESSION['id'])) {
        $_SESSION['pwd'] = pwd_encoder(dzcp_demo_password,3);
    }
    
    if(data('pwd', 1) != pwd_encoder(dzcp_demo_password,3)) {
        $newpwd = "`pwd` = '".stringParser::encode(pwd_encoder(dzcp_demo_password,3))."',`pwd_encoder` = 3";
        $sql->update("UPDATE `{prefix_users}` SET " . $newpwd . " WHERE id = ?;", array(1));
    }
    $_SESSION['pwd_demo'] = dzcp_demo_password;
}

//-> Passwort in md5 oder sha1 bis 512 codieren
function pwd_encoder($password,$encoder=-1) {
    $encoder = ($encoder != -1 ? $encoder : 
            settings::get('default_pwd_encoder'));
    switch ($encoder) {
        case 0: return md5($password);
        case 1: return sha1($password);
        default:
        case 3: return hash('sha256', $password);
        case 2: return hash('sha512', $password);
    }
}

//-> Sprache aendern
if(isset($_GET['set_language']) && !empty($_GET['set_language'])) {
    if(file_exists(basePath."/inc/lang/languages/".$_GET['set_language'].".php")) {
        cookie::put('language', $_GET['set_language']);
        cookie::save();
    }

    header("Location: ".stringParser::decode(GetServerVars('HTTP_REFERER')));
}

lang($language); //Lade Sprache
$userid = intval(userid());
$chkMe = intval(checkme());
if(!$chkMe && (!empty($_SESSION['id']) || !empty($_SESSION['pwd']))) {
    $_SESSION['id']        = '';
    $_SESSION['pwd']       = '';
    $_SESSION['ip']        = $userip;
    $_SESSION['lastvisit'] = time();
}

//-> Prueft ob der User gebannt ist, oder die IP des Clients warend einer offenen session veraendert wurde.
if($chkMe && $userid && !empty($_SESSION['ip'])) {
    if($_SESSION['ip'] != visitorIp() || isBanned($userid,false) ) {
        dzcp_session_destroy();
        header("Location: ../news/");
    }
}

/*
 * DZCP V1.7.0
 * Aktualisiere die Client DNS & User Agent
 */
if(session_id()) {
    $userdns = DNSToIp($userip);
    if($sql->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `update` <= ? AND `sessid` = ?;",array(time(),session_id()))) {
        $bot = SearchBotDetect();
        $sql->update("UPDATE `{prefix_iptodns}` SET `time` = ?, `update` = ?, `ip` = ?, `agent` = ?, `dns` = ?, `bot` = ?, `bot_name` = ?, `bot_fullname` = ? WHERE `sessid` = ?;",
        array((time()+10*60),(time()+60),$userip,stringParser::encode($UserAgent),stringParser::encode($userdns),($bot['bot'] ? 1 : 0),stringParser::encode($bot['name']),stringParser::encode($bot['fullname']),session_id()));
        unset($bot);
    } else if(!$sql->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `sessid` = ?;",array(session_id()))) {
        $bot = SearchBotDetect();
        $sql->insert("INSERT INTO `{prefix_iptodns}` SET `sessid` = ?, `time` = ?, `ip` = ?, `agent` = ?, `dns` = ?, `bot` = ?, `bot_name` = ?, `bot_fullname` = ?;",
        array(session_id(),(time()+10*60),$userip,stringParser::encode($UserAgent),stringParser::encode($userdns),($bot['bot'] ? 1 : 0),stringParser::encode($bot['name']),stringParser::encode($bot['fullname'])));
        unset($bot);
    }
    
    //-> Cleanup DNS DB
    $qryDNS = $sql->select("SELECT `id`,`ip` FROM `{prefix_iptodns}` WHERE `time` <= ?;",array(time()));
    if($sql->rowCount()) {
        foreach($qryDNS as $getDNS) {
            $sql->delete("DELETE FROM `{prefix_iptodns}` WHERE `id` = ?;",array($getDNS['id']));
            $sql->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `ip` = ?;",array($getDNS['ip']));
        } unset($getDNS);
    } unset($qryDNS);

    /*
     * Pruft ob mehrere Session IDs von der gleichen DNS kommen, sollte der Useragent keinen Bot Tag enthalten, wird ein Spambot angenommen.
     */
    $get_sb_array = $sql->select("SELECT `id`,`ip`,`bot`,`agent` FROM `{prefix_iptodns}` WHERE `dns` LIKE ?;",array(stringParser::encode($userdns)));
    if($sql->rowCount() >= 3 && !validateIpV4Range($userip, '[192].[168].[0-255].[0-255]') && 
        !validateIpV4Range($userip, '[127].[0].[0-255].[0-255]') && 
        !validateIpV4Range($userip, '[10].[0-255].[0-255].[0-255]') && 
        !validateIpV4Range($userip, '[172].[16-31].[0-255].[0-255]')) {
		foreach ($get_sb_array as $get_sb) {
			if(!$get_sb['bot'] && !isSpider(stringParser::decode($get_sb['agent']))) {
				if(!$sql->rows("SELECT `id` FROM `{prefix_ipban}` WHERE `ip` = ? LIMIT 1;",array($userip))) {
					$data_array = array();
					$data_array['confidence'] = ''; $data_array['frequency'] = ''; $data_array['lastseen'] = '';
					$data_array['banned_msg'] = stringParser::encode('SpamBot detected by System * No BotAgent *');
					$data_array['agent'] = $get_sb['agent'];
					$sql->insert("INSERT INTO `{prefix_ipban}` SET `time` = ?, `ip` = ?, `data` = ?, `typ` = 3;",array(time(),$get_sb['ip'],serialize($data_array)));
					check_ip(); // IP Prufung * No IPV6 Support *
					unset($data_array);
				}
			}
		}
    }

    unset($get_sb,$get_sb_array);
}

/**
* DZCP V1.7.0
* Erkennt bekannte Bots am User Agenten
*/
function SearchBotDetect() { 
    global $UserAgent,$sql;
    $qry = $sql->select("SELECT * FROM `{prefix_botlist}` WHERE `enabled` = 1;");
    if($sql->rowCount()) {
        foreach($qry as $botdata) {
            switch ($botdata['type']) {
                case 1:
                    if(preg_match(stringParser::decode($botdata['regexpattern']), $UserAgent, $matches)) {
                        return array('fullname' => stringParser::decode($botdata['name'])." V".trim($matches[1]), 'name' =>stringParser::decode($botdata['name']), 'bot' => true); 
                    }
                break;
                case 2:
                    if(preg_match(stringParser::decode($botdata['regexpattern']), $UserAgent, $matches)) {
                        list($majorVer, $minorVer) = explode(".", $matches[1]);
                        return array('fullname' => stringParser::decode($botdata['name'])." V".trim($majorVer).'.'.trim($minorVer), 'name' =>stringParser::decode($botdata['name']), 'bot' => true); 
                    } 
                break;
                case 3:
                    if(preg_match(stringParser::decode($botdata['regexpattern']), $UserAgent, $matches)) {
                        list($majorVer, $minorVer, $build) = explode(".", $matches[1]);
                        return array('fullname' => stringParser::decode($botdata['name'])." V".trim($majorVer).'.'.trim($minorVer).'.'.trim($build), 'name' =>stringParser::decode($botdata['name']), 'bot' => true); 
                    } 
                break;
                default:
                     if(preg_match(stringParser::decode($botdata['regexpattern']), $UserAgent)) {
                        if(empty($botdata['name_extra'])) $botdata['name_extra'] = $botdata['name'];
                        return array('fullname' => stringParser::decode($botdata['name_extra']), 'name' => stringParser::decode($botdata['name']), 'bot' => true); 
                    }
                break;
            }
        }
    }
    
    return array('fullname'=>'',"name"=>'',"bot"=>false); 
}

/**
* DZCP V1.7.0
* Browser-Cache nicht verwenden -> Ajax
*/
function addNoCacheHeaders() {
    header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

/**
* DZCP V1.7.0
* Gibt die IP des Besuchers / Users zuruck
* Forwarded IP Support
*/
function visitorIp() {
    $SetIP = '0.0.0.0';
    $ServerVars = array('REMOTE_ADDR','HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED',
    'HTTP_FORWARDED_FOR','HTTP_FORWARDED','HTTP_VIA','HTTP_X_COMING_FROM','HTTP_COMING_FROM');
    foreach ($ServerVars as $ServerVar) {
        if($IP=detectIP($ServerVar)) {
            if (isIP($IP) && !validateIpV4Range($IP, '[192].[168].[0-255].[0-255]') &&
                    !validateIpV4Range($IP, '[127].[0].[0-255].[0-255]') &&
                    !validateIpV4Range($IP, '[10].[0-255].[0-255].[0-255]') &&
                    !validateIpV4Range($IP, '[172].[16-31].[0-255].[0-255]')) {
                return $IP;
            } else {
                $SetIP = $IP;
            }

            //IPV6
            if(isIP($IP, true)) { return $IP; }
        }
    }
    
    return $SetIP;
}

function detectIP($var) {
    if(!empty($var) && ($REMOTE_ADDR = GetServerVars($var)) && !empty($REMOTE_ADDR)) {
        $REMOTE_ADDR = trim($REMOTE_ADDR);
        if (isIP($REMOTE_ADDR) || isIP($REMOTE_ADDR, true)) {
            return $REMOTE_ADDR;
        }
    }
    
    return false;
}

/**
 * Check given ip for ipv6 or ipv4.
 * @param    string        $ip
 * @param    boolean       $v6
 * @return   boolean
 */
function isIP($ip,$v6=false) {
    if (!$v6 && $ip == "0.0.0.0") { return false; }
    if(!$v6 && substr_count($ip,":") < 1) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? true : false;
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? true : false;
}

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

/**
 * Auslesen der UserID
 * @return integer
 **/
function userid() {
    global $sql;
    if (empty($_SESSION['id']) || empty($_SESSION['pwd'])) { return 0; }
    if(!dbc_index::issetIndex('user_'.intval($_SESSION['id']))) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ? AND `pwd` = ?;",array(intval($_SESSION['id']),$_SESSION['pwd']));
        if (!$sql->rowCount()) { return 0; }
        dbc_index::setIndex('user_'.$get['id'], $get, 2);
        return $get['id'];
    }

    return dbc_index::getIndexKey('user_'.intval($_SESSION['id']), 'id');
}

//-> Templateswitch
$files = get_files(basePath.'/inc/_templates_/',true);
if(isset($_GET['tmpl_set'])) {
    foreach ($files as $templ) {
        if($templ == $_GET['tmpl_set']) {
            cookie::put('tmpdir', $templ);
            cookie::save();
            header("Location: ".GetServerVars('HTTP_REFERER'));
        }
    }
}

if(cookie::get('tmpdir') != false && cookie::get('tmpdir') != NULL) {
    if (file_exists(basePath . "/inc/_templates_/" . cookie::get('tmpdir'))) {
        $tmpdir = cookie::get('tmpdir');
    } else {
        $tmpdir = $files[0];
    }
} else {
    if (file_exists(basePath . "/inc/_templates_/" . $sdir)) {
        $tmpdir = $sdir;
    } else {
        $tmpdir = $files[0];
    }
}
unset($files);

$designpath = '../inc/_templates_/'.$tmpdir;

//-> Languagefiles einlesen
function lang($lng) {
    if(!file_exists(basePath."/inc/lang/languages/".$lng.".php")) {
        $files = get_files(basePath.'/inc/lang/languages/',false,true,array('php'));
        $lng = str_replace('.php','',$files[0]);
    }
    
    include(basePath."/inc/lang/global.php");
    include(basePath."/inc/lang/languages/".$lng.".php");
}

/**
 * Sprachdateien auflisten
 * @return string/html
 **/
function languages() {
    $lang="";
    $files = get_files(basePath.'/inc/lang/languages/',false,true,array('php'));
    for($i=0;$i<=count($files)-1;$i++) {
        $file = str_replace('.php','',$files[$i]);
        $upFile = strtoupper(substr($file,0,1)).substr($file,1);
        if(file_exists('../inc/lang/flaggen/'.$file.'.gif'))
            $lang .= '<a href="?set_language='.$file.'"><img src="../inc/lang/flaggen/'.$file.'.gif" alt="'.$upFile.'" title="'.$upFile.'" class="icon" /></a> ';
    }

    return $lang;
}

//-> User Hits und Lastvisit aktualisieren
if($userid >= 1 && $ajaxJob != true && isset($_SESSION['lastvisit'])) {
    $sql->update("UPDATE `{prefix_userstats}` SET `hits` = (hits+1), `lastvisit` = ? WHERE `user` = ?;",array(intval($_SESSION['lastvisit']),intval($userid)));
}

//-> Prueft ob der User ein Rootadmin ist
function rootAdmin($userid=0) {
    global $rootAdmins;
    $userid = (!$userid ? userid() : $userid);
    if (!count($rootAdmins)) { return false; }
    return in_array($userid, $rootAdmins);
}

function regexChars($txt) {
    $search  = array('"', '\\', '<', '>', '/',
    '.', ':', '^', '$', '|',
    '?', '*', '+', '-', '(',
    ')', '[', ']', '}', '{',
    "\r", "\n" );

    $replace = array('&quot;', '\\\\', '\<', '\>', '\/',
    '\.', '\:', '\^', '\$', '\|',
    '\?', '\*', '\+', '\-', '\(',
    '\)', '\[', '\]', '\}', '\{',
    '', '' );

    return str_replace($search,$replace,strip_tags($txt));
}

function bbcode_html($txt,$tinymce=0) {
    $txt = str_replace("&lt;","<",$txt);
    $txt = str_replace("&gt;",">",$txt);
    $txt = str_replace("&quot;","\"",$txt);
    return str_replace("&#34;","\"",$txt);
}

function bbcode_email($txt) {
    return str_replace(array("&#91;","&#93;"),
            array("[","]"),bbcode::parse_html($txt));
}

//-> Flaggen ausgeben
function flagge($txt) {
    $var = array("/\:de:/",
                 "/\:ch:/",
                 "/\:at:/",
                 "/\:au:/",
                 "/\:be:/",
                 "/\:br:/",
                 "/\:ca:/",
                 "/\:gb:/",
                 "/\:pl:/",
                 "/\:cz:/",
                 "/\:dk:/",
                 "/\:es:/",
                 "/\:en:/",
                 "/\:fi:/",
                 "/\:fr:/",
                 "/\:gr:/",
                 "/\:hr:/",
                 "/\:us:/",
                 "/\:it:/",
                 "/\:se:/",
                 "/\:eu:/",
                 "/\:nl:/",
                 "/\:na:/",
                 "/\:no:/",
                 "/\:ru:/");

    $repl = array("<img src=\"../inc/images/flaggen/de.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/ch.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/at.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/au.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/be.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/br.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/ca.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/uk.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/pl.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/cz.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/dk.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/es.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/fo.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/fi.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/fr.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/gr.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/hr.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/us.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/it.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/se.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/eu.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/nl.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/nocountry.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/no.gif\" alt=\"\" />",
                  "<img src=\"../inc/images/flaggen/ru.gif\" alt=\"\" />" );

    return preg_replace($var,$repl, $txt);
}

/**
 * DZCP V1.7.0
 * Funktion um Ausgaben zu kuerzen
 * @return string
 **/
function cut($str, $length = null, $dots = true) {
    if($length === 0)
        return '';

    $start = 0;
    $dots = ($dots == true && strlen(html_entity_decode($str)) > $length) ? '...' : '';

    if(strpos($str, '&') === false)
        return (($length === null) ? substr($str, $start) : substr($str, $start, $length)).$dots;

    $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
    $html_length = count($chars);

    if(($html_length === 0) || ($start >= $html_length) || (isset($length) && ($length <= -$html_length)))
        return '';

    if($start >= 0)
        $real_start = $chars[$start][1];
    else {
        $start = max($start,-$html_length);
        $real_start = $chars[$html_length+$start][1];
    }

    if (!isset($length))
        return substr($str, $real_start).$dots;
    else if($length > 0)
        return (($start+$length >= $html_length) ? substr($str, $real_start) : substr($str, $real_start, $chars[max($start,0)+$length][1] - $real_start)).$dots;
    else
        return substr($str, $real_start, $chars[$html_length+$length][1] - $real_start).$dots;
}

function wrap($str, $width = 75, $break = "\n", $cut = true) {
    return strtr(str_replace(htmlentities($break), $break, htmlentities(wordwrap(html_entity_decode($str), $width, $break, $cut), ENT_QUOTES)), array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT)));
}

/**
 * DZCP V1.7.0
 * Funktion um Dateien aus einem Verzeichnis auszulesen
 * @return array
 **/
function get_files($dir=null,$only_dir=false,$only_files=false,$file_ext=array(),$preg_match=false,$blacklist=array(),$blacklist_word=false) {
    $files = array();
    if(!file_exists($dir) && !is_dir($dir)) return $files;
    if($handle = @opendir($dir)) {
        if($only_dir) {
            while(false !== ($file = readdir($handle))) {
                if($file != '.' && $file != '..' && !is_file($dir.'/'.$file)) {
                    if(!count($blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && ($preg_match ? preg_match($preg_match,$file) : true))
                        $files[] = $file;
                    else {
                        if(!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && ($preg_match ? preg_match($preg_match,$file) : true))
                            $files[] = $file;
                    }
                }
            } //while end
        } else if($only_files) {
            while(false !== ($file = readdir($handle))) {
                if($file != '.' && $file != '..' && is_file($dir.'/'.$file)) {
                    if(!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && !count($file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                        $files[] = $file;
                    else {
                        ## Extension Filter ##
                        $exp_string = array_reverse(explode(".", $file));
                        if(!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                            $files[] = $file;
                    }
                }
            } //while end
        } else {
            while(false !== ($file = readdir($handle))) {
                if($file != '.' && $file != '..' && is_file($dir.'/'.$file)) {
                    if(!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && !count($file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                        $files[] = $file;
                    else {
                        ## Extension Filter ##
                        $exp_string = array_reverse(explode(".", $file));
                        if(!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                            $files[] = $file;
                    }
                } else {
                    if(!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && $file != '.' && $file != '..' && ($preg_match ? preg_match($preg_match,$file) : true))
                        $files[] = $file;
                }
            } //while end
        }

        if(is_resource($handle))
            closedir($handle);

        if(!count($files))
            return false;

        return $files;
    }
    else
        return false;
}

/**
 * DZCP V1.7.0
 * Gibt einen Teil eines nummerischen Arrays wieder
 * @return array
 **/
function limited_array($array=array(),$begin=1,$max=10) {
    $array_exp = array();
    $range=range($begin=($begin-1), ($begin+$max-1));
    foreach($array as $key => $wert) {
        if(array_var_exists($key, $range))
            $array_exp[$key] = $wert;
    }

    return $array_exp;
}

function array_var_exists($var,$search)
{ foreach($search as $key => $var_) { if($var_==$var) return true; } return false; }

/**
 * DZCP V1.7.0
 * Funktion um eine Datei im Web auf Existenz zu prufen und abzurufen
 * @return String
 **/
function get_external_contents($url,$post=false,$nogzip=false,$timeout=file_get_contents_timeout) {
    if((!allow_url_fopen_support() && !use_curl || (use_curl && !extension_loaded('curl'))))
        return false;
    
    $url_p = @parse_url($url);
    $host = $url_p['host'];
    $port = isset($url_p['port']) ? $url_p['port'] : 80;
    $port = (($url_p['scheme'] == 'https' && $port == 80) ? 443 : $port);
    if(!ping_port($host,$port,$timeout)) return false;
    unset($host);

    if(class_exists('\\Snoopy\\Snoopy') && $url_p['scheme'] != 'https') { //Use Snoopy HTTP Client
        $snoopy = new Snoopy\Snoopy;
        if(count($post) >= 1 && $post != false) {
            $snoopy->rawheaders["Pragma"] = "no-cache";
            $snoopy->submit($url, $post);
        } else {
            $snoopy->rawheaders["Pragma"] = "no-cache";
            if (!$snoopy->fetch($url)) {
                return false;
            }
        }

        return ((string)(trim($snoopy->results)));
    }

    if(use_curl && extension_loaded('curl')) {
        if(!$curl = curl_init())
            return false;

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "DZCP-HTTP-CLIENT");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout * 2); // x 2
        
        //For POST
        if(count($post) >= 1 && $post != false) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_VERBOSE , 0 );
        }
        
        $gzip = false;
        if(function_exists('gzinflate') && !$nogzip) {
            $gzip = true;
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip,deflate'));
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        }

        if($url_p['scheme'] == 'https') { //SSL
            curl_setopt($curl, CURLOPT_PORT , $port);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $content = curl_exec($curl);
        if (empty($content) || (is_bool($content) && !$content)) {
            return false;
        }

        if($gzip) {
            $curl_info = curl_getinfo($curl,CURLINFO_HEADER_OUT);
            if(stristr($curl_info, 'accept-encoding') && stristr($curl_info, 'gzip')) {
                $content = gzinflate( substr($content,10,-8) );
           }
        }

        @curl_close($curl);
        unset($curl);
    } else {
        if($url_p['scheme'] == 'https') //HTTPS not Supported!
            $url = str_replace('https', 'http', $url);
        
        $opts = array();
        $opts['http']['method'] = "GET";
        $opts['http']['timeout'] = $timeout * 2;
                
        $gzip = false;
        if(function_exists('gzinflate') && !$nogzip) {
            $gzip = true;
            $opts['http']['header'] = 'Accept-Encoding:gzip,deflate'."\r\n";
        }
        
        $context = stream_context_create($opts);
        if(!$content = @file_get_contents($url, false, $context, -1, 40000))
            return false;

        if($gzip) {
            foreach($http_response_header as $c => $h) {
                if(stristr($h, 'content-encoding') && stristr($h, 'gzip')) {
                    $content = gzinflate( substr($content,10,-8) );
                }
            }
        }
    }
    
    return ((string)(trim($content)));
}

/**
 * Funktion um Sonderzeichen in HTML Text zu konvertieren
 * @param string $txt
 * @return string
 */
function spChars($txt) {
    $search  = array("Ä","ä","Ü","ü","Ö","ö","ß");
    $replace = array("&Auml;","&auml;","&Uuml;","&uuml;","&Ouml;","&ouml;","&szlig;");
    return str_replace($search,$replace,$txt);
}

/**
 * DZCP V1.7.0
 * Gibt Informationen uber Server und Ausfuhrungsumgebung zuruck
 *
 * @param string $var
 * @return string
 */
function GetServerVars($var) {
    if (array_key_exists($var, $_SERVER) && !empty($_SERVER[$var])) {
        return stringParser::encode($_SERVER[$var]);
    } else if (array_key_exists($var, $_ENV) && !empty($_ENV[$var])) {
        return stringParser::encode($_ENV[$var]);
    }

    if($var=='HTTP_REFERER') { //Fix for empty HTTP_REFERER
        return GetServerVars('REQUEST_SCHEME').'://'.GetServerVars('HTTP_HOST').
        GetServerVars('DOCUMENT_URI');
    }

    return false;
}

//-> Funktion um diverse Dinge aus Tabellen auszaehlen zu lassen
//-> Single & Multi Version
function cnt($db, $where = "", $what = "id", $sql_std=array()) {
    global $sql;
    $cnt = $sql->fetch("SELECT COUNT(".$what.") AS `cnt` FROM `".$db."` ".$where.";",$sql_std,'cnt');
    if($sql->rowCount() >= 1) {
        return $cnt;
    }

    return 0;
}

function cnt_multi($db, $where = "", $whats = array('id'), $sql_std=array()) {
    global $sql; $cnt_sql = "";
    foreach ($whats as $what) {
        $cnt_sql .= "COUNT(".$what.") AS `cnt_".$what."`,";
    }
    $cnt_sql = substr($cnt_sql, 0, -1);
    $cnt = $sql->fetch("SELECT ".$cnt_sql." FROM `".$db."` ".$where.";",$sql_std);
    if ($sql->rowCount()) {
        return $cnt;
    }

    return array();
}

//-> Funktion um diverse Dinge aus Tabellen zusammenzaehlen zu lassen
//-> Single & Multi Version
function sum($db, $where = "", $what = "id", $sql_std=array()) {
    global $sql;
    $sum = $sql->fetch("SELECT SUM(".$what.") AS `sum` FROM `".$db."` ".$where.";",$sql_std,'sum');
    if($sql->rowCount() >= 1) {
        return $sum;
    }

    return 0;
}

function sum_multi($db, $where = "", $whats = array('id'), $sql_std=array()) {
    global $sql; $sum_sql = "";
    foreach ($whats as $what) {
        $sum_sql .= "SUM(".$what.") AS `sum_".$what."`,";
    }
    $sum_sql = substr($sum_sql, 0, -1);
    $sum = $sql->fetch("SELECT ".$sum_sql." FROM `".$db."` ".$where.";",$sql_std);
    if ($sql->rowCount()) {
        return $sum;
    }

    return array();
}

function orderby($sort) {
    $split = explode("&",GetServerVars('QUERY_STRING'));
    $url = "?";

    foreach($split as $part) {
        if(strpos($part,"orderby") === false && strpos($part,"order") === false && !empty($part)) {
            $url .= $part;
            $url .= "&";
        }
    }

    if(isset($_GET['orderby']) && $_GET['order']) {
        if ($_GET['orderby'] == $sort && $_GET['order'] == "ASC") {
            return $url . "orderby=" . $sort . "&order=DESC";
        }
    }

    return $url."orderby=".$sort."&order=ASC";
}

function orderby_sql($order_by=array(), $default_order='',$join='', $order = array('ASC','DESC')) {
    if (!isset($_GET['order']) || empty($_GET['order']) || !in_array($_GET['order'], $order) || 
        !isset($_GET['orderby']) || empty($_GET['orderby']) || !in_array($_GET['orderby'], $order_by) || 
        empty($_GET['orderby']) || empty($_GET['order'])) {
        return $default_order;
    }
    $key = array_search($_GET['orderby'], $order_by);   // $key = 1;
    $order_by = (in_array($_GET['orderby'], $order_by) ? '`'.$order_by[$key].'` ' : '`id` ');
    $order = (in_array(strtoupper($_GET['order']), $order) ? (strtoupper($_GET['order']) == 'DESC' ? 'DESC ' : 'ASC ') : 'DESC ');
    return 'ORDER BY '.(!empty($join) ? $join.'.' : '').$order_by.$order;
}

function orderby_nav() {
    $orderby = isset($_GET['orderby']) ? "&orderby".$_GET['orderby'] : "";
    $orderby .= isset($_GET['order']) ? "&order=".$_GET['order'] : "";
    return $orderby;
}

//-> Counter updaten
function updateCounter() {
    global $sql,$reload,$userip;
    $datum = time();
    $get_agent = $sql->fetch("SELECT `id`,`agent`,`bot` FROM `{prefix_iptodns}` WHERE `ip` = ?;",array(stringParser::encode($userip)));
    if($sql->rowCount()) {
        if(!$get_agent['bot'] && !isSpider(stringParser::decode($get_agent['agent']))) {
            if($sql->rows("SELECT id FROM `{prefix_counter_ips}` WHERE datum+? <= ? OR FROM_UNIXTIME(datum,'%d.%m.%Y') != ?;",array($reload,time(),date("d.m.Y")))) {
                $sql->delete("DELETE FROM `{prefix_counter_ips}` WHERE datum+? <= ? OR FROM_UNIXTIME(datum,'%d.%m.%Y') != ?;",array($reload,time(),date("d.m.Y")));
            }

            $get = $sql->fetch("SELECT `datum` FROM `{prefix_counter_ips}` WHERE `ip` = ? AND FROM_UNIXTIME(datum,'%d.%m.%Y') = ?;",array(stringParser::encode($userip),date("d.m.Y")));
            if($sql->rowCount()) {
                $sperrzeit = $get['datum']+$reload;
                if($sperrzeit <= time()) {
                    $sql->delete("DELETE FROM `{prefix_counter_ips}` WHERE `ip` = ?;",array(stringParser::encode($userip)));
                    if ($sql->rows("SELECT `id` FROM `{prefix_counter}` WHERE `today` = '" . date("j.n.Y") . "';",array(date("j.n.Y")))) {
                        $sql->update("UPDATE `{prefix_counter}` SET `visitors` = (visitors+1) WHERE `today` = ?;",array(date("j.n.Y")));
                    } else {
                        $sql->insert("INSERT INTO `{prefix_counter}` SET `visitors` = 1 WHERE `today` = ?;",array(date("j.n.Y")));
                    }

                    $sql->insert("INSERT INTO `{prefix_counter_ips}` SET `ip` = ?, `datum` = ?;",array(stringParser::encode($userip),intval($datum)));
                }
            } else {
                if($sql->rows("SELECT `id` FROM `{prefix_counter}` WHERE `today` = ?;",array(date("j.n.Y")))) {
                    $sql->update("UPDATE `{prefix_counter}` SET `visitors` = (visitors+1) WHERE `today` = ?;",array(date("j.n.Y")));
                } else {
                    $sql->insert("INSERT INTO `{prefix_counter}` SET `visitors` = 1, `today` = ?;",array(date("j.n.Y")));
                }

                $sql->insert("INSERT INTO `{prefix_counter_ips}` SET `ip` = ?, `datum` = ?;",array(stringParser::encode($userip),intval($datum)));
            }
        }
    }
}

//-> Updatet die Maximalen User die gleichzeitig online sind
function update_maxonline() {
    global $sql;
    $maxonline = $sql->fetch("SELECT `maxonline` FROM `{prefix_counter}` WHERE `today` = ?;",array(date("j.n.Y")),'maxonline');
    if ($maxonline < ($count = cnt('{prefix_counter_whoison}'))) {
        $sql->update("UPDATE `{prefix_counter}` SET `maxonline` = ? WHERE `today` = ?;",array($count,date("j.n.Y")));
    }
}

//-> Aktualisiert die Position der Gaste & User
function update_online($where='') {
    global $sql,$useronline,$userip,$chkMe,$isSpider,$userid;
    if(!$isSpider && !empty($where) && !$sql->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `sessid` = ? AND `bot` = 1;",array(session_id()))) {
        if($sql->rows("SELECT `id` FROM `{prefix_counter_whoison}` WHERE `online` < ?;",array(time()))) { //Cleanup
            $sql->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `online` < ?;",array(time()));
        }

        $get = $sql->fetch("SELECT `id` FROM `{prefix_counter_whoison}` WHERE `ip` = ? AND `ssid` = ?;",array($userip,session_id())); //Update Move
        if($sql->rowCount()) {
            $sql->update("UPDATE `{prefix_counter_whoison}` SET `whereami` = ?, `online` = ?, `login` = ?  WHERE `id` = ?;",
            array(stringParser::encode($where),(time()+$useronline),(!$chkMe ? 0 : 1),$get['id']));
        } else {
            $sql->insert("INSERT INTO `{prefix_counter_whoison}` SET `ip` = ?, `ssid` = ?, `online` = ?, `whereami` = ?, `login` = ?;",
            array($userip, session_id(),(time()+$useronline),stringParser::encode($where),(!$chkMe ? 0 : 1)));
        }
        
        if($chkMe) {
            $sql->update("UPDATE `{prefix_users}` SET `time` = ?, `whereami` = ? WHERE `id` = ?;",array(time(),stringParser::encode($where),intval($userid)));
        }
    }
}

//-> Prueft, wieviele Besucher gerade online sind
function online_guests($where='',$like=false) {
    global $sql,$useronline,$isSpider;
    if(!$isSpider) {
        $whereami = (empty($where) ? '' : 
            ($like ? " AND `whereami` LIKE '%".$where."%'" : 
                " AND `whereami` = ".$sql->quote($where)));
        return cnt('{prefix_counter_whoison}'," WHERE (online+".$useronline.")>".time()."".$whereami." AND `login` = 0");
    }
    
    return 0;
}

//-> Prueft, wieviele registrierte User gerade online sind
function online_reg($where='',$like=false) {
    global $sql,$useronline,$isSpider;
    if(!$isSpider) {
        $whereami = (empty($where) ? '' : 
            ($like ? " AND `whereami` LIKE '%".$where."%'" : 
                " AND `whereami` = ".$sql->quote($where)));
        return cnt('{prefix_users}', " WHERE (time+".$useronline.")>".time()."".$whereami." AND `online` = 1");
    }
    
    return 0;
}

//-> Prueft, ob der User eingeloggt ist und wenn ja welches Level besitzt er
function checkme($userid_set=0) {
    global $sql;
    if (empty($_SESSION['id']) || empty($_SESSION['pwd'])) { return 0; }
    if (!$userid = ($userid_set != 0 ? intval($userid_set) : userid())) { return 0; }
    if (rootAdmin($userid)) { return 4; }
    if(!dbc_index::issetIndex('user_'.intval($userid))) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ? AND `pwd` = ? AND `ip` = ?;",array(intval($userid),$_SESSION['pwd'],$_SESSION['ip']));
        if (!$sql->rowCount()) { return 0; }
        dbc_index::setIndex('user_'.$get['id'], $get);
        return $get['level'];
    }

    return dbc_index::getIndexKey('user_'.intval($userid), 'level');
}

//-> Prueft, ob der User gesperrt ist und meldet ihn ab
function isBanned($userid_set=0,$logout=true) {
    global $sql,$userid;
    $userid_set = $userid_set ? $userid_set : $userid;
    if(checkme($userid_set) >= 1 || $userid_set) {
        $get = $sql->fetch("SELECT `banned` FROM `{prefix_users}` WHERE `id` = ? LIMIT 1;",array(intval($userid_set)));
        if($get['banned']) {
            if($logout) {
                dzcp_session_destroy();
            }

            return true;
        }
    }

    return false;
}

//-> Prueft, ob ein User diverse Rechte besitzt
function permission($check,$uid=0) {
    global $sql,$userid,$chkMe;
    if (!$uid) { $uid = $userid; }
    if(rootAdmin($uid)) 
        return true;
    
    if($chkMe == 4) {
        return true;
    } else {
        if ($uid) {
            // check rank permission
            if ($sql->rows("SELECT s1.`" . $check . "` FROM `{prefix_permissions}` AS `s1` LEFT JOIN `{prefix_userposis}` AS `s2` ON s1.`pos` = s2.`posi`"
                            . "WHERE s2.`user` = ? AND s1.`" . $check . "` = 1 AND s2.`posi` != 0;", array(intval($uid)))) {
                return true;
            }

            // check user permission
            if (!dbc_index::issetIndex('user_permission_' . intval($uid))) {
                $permissions = $sql->fetch("SELECT * FROM `{prefix_permissions}` WHERE `user` = ?;", array(intval($uid)));
                dbc_index::setIndex('user_permission_' . intval($uid), $permissions);
            }

            return dbc_index::getIndexKey('user_permission_' . intval($uid), $check) ? true : false;
        } else {
            return false;
        }
    }
}

/*
 * DZCP V1.7.0
 * Aktualisierung des Online Status *preview
 */
function update_user_status_preview() {
    global $sql,$userip;
    ## User aus der Datenbank suchen ##
    $get = $sql->fetch("SELECT `id`,`time` FROM `{prefix_users}` "
            . "WHERE `id` = ? AND `sessid` = ? AND `ip` = ? AND level != 0;",
            array(intval($_SESSION['id']),session_id(),stringParser::encode($userip)));

    if($sql->rowCount()) {
        ## Schreibe Werte in die Server Sessions ##
        $_SESSION['lastvisit']  = $get['time'];

        if(stringParser::decode(data("ip",$get['id'])) != $_SESSION['ip'])
            $_SESSION['lastvisit'] = data($get['id'], "time");

        if(empty($_SESSION['lastvisit']))
            $_SESSION['lastvisit'] = data($get['id'], "time");

        ## Aktualisiere Datenbank ##
        $sql->update("UPDATE `{prefix_users}` SET `online` = 1 WHERE `id` = ?;",array($get['id']));
    }
}

//-> Checkt, ob neue Nachrichten vorhanden sind
function check_msg() {
    global $sql;
    if($sql->rows("SELECT `id` FROM `{prefix_messages}` WHERE `an` = ? AND `page` = 0;",array(intval($_SESSION['id'])))) {
        $sql->update("UPDATE `{prefix_messages}` SET `page` = 1 WHERE `an` = ?;",array(intval($_SESSION['id'])));
        return show("user/new_msg", array("new" => _site_msg_new));
    }

    return false;
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten
function cw_result($punkte, $gpunkte) {
    if ($punkte > $gpunkte) {
        return '<span class="CwWon">' . $punkte . ':' . $gpunkte . '</span> <img src="../inc/images/won.gif" alt="" class="icon" />';
    } else if ($punkte < $gpunkte) {
        return '<span class="CwLost">' . $punkte . ':' . $gpunkte . '</span> <img src="../inc/images/lost.gif" alt="" class="icon" />';
    } else {
        return '<span class="CwDraw">' . $punkte . ':' . $gpunkte . '</span> <img src="../inc/images/draw.gif" alt="" class="icon" />';
    }
}

function cw_result_pic($punkte, $gpunkte) {
    if ($punkte > $gpunkte) {
        return '<img src="../inc/images/won.gif" alt="" class="icon" />';
    } else if ($punkte < $gpunkte)
        return '<img src="../inc/images/lost.gif" alt="" class="icon" />';
    else
        return '<img src="../inc/images/draw.gif" alt="" class="icon" />';
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten ohne bild
function cw_result_nopic($punkte, $gpunkte) {
    if ($punkte > $gpunkte) {
        return '<span class="CwWon">' . $punkte . ':' . $gpunkte . '</span>';
    } else if ($punkte < $gpunkte) {
        return '<span class="CwLost">' . $punkte . ':' . $gpunkte . '</span>';
    } else {
        return '<span class="CwDraw">' . $punkte . ':' . $gpunkte . '</span>';
    }
}

//-> Funktion um bei Clanwars Endergebnisse auszuwerten ohne bild und ohne farbe
function cw_result_nopic_nocolor($punkte, $gpunkte) {
    if ($punkte > $gpunkte) {
        return $punkte . ':' . $gpunkte;
    } else if ($punkte < $gpunkte) {
        return $punkte . ':' . $gpunkte;
    } else {
        return $punkte . ':' . $gpunkte;
    }
}

//-> Funktion um bei Clanwars Details Endergebnisse auszuwerten ohne bild
function cw_result_details($punkte, $gpunkte) {
    if ($punkte > $gpunkte) {
        return '<td class="contentMainFirst" align="center"><span class="CwWon">' . $punkte . '</span></td><td class="contentMainFirst" align="center"><span class="CwLost">' . $gpunkte . '</span></td>';
    } else if ($punkte < $gpunkte) {
        return '<td class="contentMainFirst" align="center"><span class="CwLost">' . $punkte . '</span></td><td class="contentMainFirst" align="center"><span class="CwWon">' . $gpunkte . '</span></td>';
    } else {
        return '<td class="contentMainFirst" align="center"><span class="CwDraw">' . $punkte . '</span></td><td class="contentMainFirst" align="center"><span class="CwDraw">' . $gpunkte . '</span></td>';
    }
}

//-> Flaggen ausgeben
function flag($code) {
    global $picformat;
    if (empty($code)) {
        return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';
    }

    foreach($picformat as $end) {
        if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
            break;
        }
    }

    if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
        return'<img src="../inc/images/flaggen/' . $code . '.' . $end . '" alt="" class="icon" />';
    }

    return '<img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" />';
}

function rawflag($code) {
    global $picformat;
    if (empty($code)) {
        return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';
    }

    foreach($picformat as $end) {
        if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
            break;
        }
    }

    if (file_exists(basePath . "/inc/images/flaggen/" . $code . "." . $end)) {
        return '<img src=../inc/images/flaggen/' . $code . '.' . $end . ' alt= class=icon />';
    }

    return '<img src=../inc/images/flaggen/nocountry.gif alt= class=icon />';
}

//-> Liste der Laender ausgeben
function show_countrys($i="") {
    if ($i != "") {
        $options = preg_replace('#<option value="' . $i . '">(.*?)</option>#', '<option value="' . $i . '" selected="selected"> \\1</option>', _country_list);
    } else {
        $options = preg_replace('#<option value="de"> Deutschland</option>#', '<option value="de" selected="selected"> Deutschland</option>', _country_list);
    }

    return '<select id="land" name="land" class="dropdown">'.$options.'</select>';
}

//-> Gameicon ausgeben
function squad($code) {
    global $picformat;
    if (empty($code)) {
        return '<img src="../inc/images/gameicons/custom/unknown.gif" alt="" class="icon" />';
    }

    $code = str_replace(array('.png','.gif','.jpg'),'',$code);
    foreach($picformat as $end) {
        if (file_exists(basePath . "/inc/images/gameicons/custom/" . $code . "." . $end)) {
            break;
        }
    }

    if (file_exists(basePath . "/inc/images/gameicons/custom/" . $code . "." . $end)) {
        return'<img src="../inc/images/gameicons/custom/' . $code . '.' . $end . '" alt="" class="icon" />';
    }

    return '<img src="../inc/images/gameicons/custom/unknown.gif" alt="" class="icon" />';
}

//-> Funktion um bei DB-Eintraegen URLs einem http:// zuzuweisen
function links($hp) {
    if(!empty($hp)) {
        //SSL
        $count = 0;
        $hp = str_replace("https://", "", $hp, $count);
        if ($count >= 1) {
            return 'https://' . $hp;
        }

        $count = 0;
        $hp = str_replace("http://", "", $hp, $count);
        if ($count >= 1) {
            return 'http://' . $hp;
        }
    }

    return $hp;
}

//-> Funktion um Passwoerter generieren zu lassen
function mkpwd($passwordLength=8,$specialcars=true) {
    global $passwordComponents;
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

//-> Infomeldung ausgeben
function info($msg, $url="", $timeout = 5) {
    if (settings::get('direct_refresh')) {
        return header('Location: ' . str_replace('&amp;', '&', $url));
    }

    $u = parse_url($url); $parts = '';
    $u['query'] = array_key_exists('query', $u) ? $u['query'] : '';
    $u['query'] = str_replace('&amp;', '&', $u['query']);
    foreach(explode('&', $u['query']) as $p) {
        $p = explode('=', $p);
        if (count($p) == 2) {
            $parts .= '<input type="hidden" name="' . $p[0] . '" value="' . $p[1] . '" />' . "\r\n";
        }
    }

    if (!array_key_exists('path', $u)) {
        $u['path'] = '';
    }
    return show("errors/info", array("msg" => $msg,
                                     "url" => $u['path'],
                                     "rawurl" => html_entity_decode($url),
                                     "parts" => $parts,
                                     "timeout" => $timeout,
                                     "info" => _info,
                                     "weiter" => _weiter,
                                     "backtopage" => _error_fwd));
}

//-> Errormmeldung ausgeben
function error($error, $back=1) {
    return show("errors/error", array("error" => $error, "back" => $back, "fehler" => _error, "backtopage" => _error_back));
}

//-> Errormmeldung ohne "zurueck" ausgeben
function error2($error) {
    return show("errors/error2", array("error" => $error, "fehler" => _error));
}

//-> Email wird auf korrekten Syntax & Erreichbarkeit ueberprueft
function check_email($email) {
    return (!preg_match("#^([a-zA-Z0-9\.\_\-]+)@([a-zA-Z0-9\.\-]+\.[A-Za-z][A-Za-z]+)$#", $email) ? false : true);
}

//-> Bilder verkleinern
function img_size($img) {
    return "<a href=\"../".$img."\" rel=\"lightbox[l_".intval($img)."]\"><img src=\"../thumbgen.php?img=".$img."\" alt=\"\" /></a>";
}

function img_cw($folder="", $img="") {
    return "<a href=\"../".$folder.$img."\" rel=\"lightbox[cw_".intval($folder)."]\"><img src=\"../thumbgen.php?img=".$folder.$img."\" alt=\"\" /></a>";
}

function gallery_size($img="") {
    return "<a href=\"../gallery/images/".$img."\" rel=\"lightbox[gallery_".intval($img)."]\"><img src=\"../thumbgen.php?img=gallery/images/".$img."\" alt=\"\" /></a>";
}

/**
* DZCP V1.7.0
* CSS Basierend - Blaetterfunktion
* [Previous][1][Next]
* [Previous][1][2][3][4][Next]
* [Previous][1][2][3][4][...][20][Next]
* [Previous][1][...][16][17][18][19][20][Next]
* [Previous][1][...][13][14][15][16][...][20][Next]
*/
function nav($entrys, $perpage, $urlpart='', $recall = 0) {
    global $page;
    if(!$entrys || !$perpage) { 
        $entrys = 1; 
        $perpage = 10; 
    }
    
    $total_pages  = ceil($entrys / $perpage);
    $maximum_links = ((9 - $recall) / 2);
    $no_recall = !$recall ? false : true;
    $offset_izq = ($page - $maximum_links) < 0 ? $page - $maximum_links : 0;
    $offset_der = ($total_pages - $page) < $maximum_links ? $maximum_links - ($total_pages - $page) : 0;
    $pagination =""; $urlpart_extended = empty($urlpart) ? '?' : '&amp;'; $recall = 0;

    if(!show_empty_paginator && $total_pages == 1) {
        return '';
    }
    
    if ($page == 1) {
        $pagination.= "<div class='pagination active'>"._paginator_previous."</div>";
    } else {
        $pagina_anterior = $page - 1;
        $pagination .= "<a href='".$urlpart.$urlpart_extended."page=".$pagina_anterior."' class='pagination'>"._paginator_previous."</a>";
    }
    
    $pager = array(); $pagination_f = '';
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i <= ($page - $maximum_links) - $offset_der || $i > ($page + $maximum_links) - $offset_izq) { $pager[$i] = false; continue; }
        $pagination_f .= ($i == $page ? "<div class='pagination active'>" .$i. "</div>" : "<a href='".$urlpart.$urlpart_extended."page=".$i."' class='pagination'>".$i."</a>");
        $pager[$i] = true;
    }
    
    if(!$pager[1]) {
        $pagination.= "<a href='".$urlpart.$urlpart_extended."page=1' class='pagination'>1</a>";
        $pagination.= "<div class='pagination active'>...</div>";
        $recall = ($recall+1);
    }
    
    $pagination.= $pagination_f;
    if(!$pager[$total_pages]) {
        $pagination.= "<div class='pagination active'>...</div>";
        $pagination.= "<a href='".$urlpart.$urlpart_extended."page=".$total_pages."' class='pagination'>".$total_pages."</a>";
        $recall = ($recall+1);
    }
    
    if($recall && !$no_recall) {
        return nav($entrys, $perpage, $urlpart, $recall);
    }
            
    if ($page == $total_pages) {
        $pagination.= "<div class='pagination active'>"._paginator_next."</div>";
    } else {
        $pagina_posterior = $page + 1;
        $pagination.= "<a href='".$urlpart.$urlpart_extended."page=".$pagina_posterior."' class='pagination'>"._paginator_next."</a>";
    }

    return $pagination."</div>";
}

//-> Generiert die Infobox bei Fehlern oder Erfolg etc. / neuer Ersatz fur function info() & error()
class notification {
    static private $notification_index = array();
    static private $notification_global = true;
    static private $notification_success = false;
    
    public static function add_error($msg = '', $link = false, $time = 3) {
        self::$notification_success = false;
        return self::import('error', $msg, $link, $time);
    }
    
    public static function add_success($msg = '', $link = false, $time = 3) {
        self::$notification_success = true;
        return self::import('success', $msg, $link, $time);
    }
    
    public static function add_notice($msg = '', $link = false, $time = 3) {
        return self::import('notice', $msg, $link, $time);
    }
    
    public static function add_warning($msg = '', $link = false, $time = 3) {
        return self::import('warning', $msg, $link, $time);
    }
    
    public static function add_custom($status = 'custom', $msg = '', $link = false, $time = 3) {
        return self::import($status, $msg, $link, $time);
    }

    public static function get($input=false) {
        $notification = '';
        if(!empty($input)) {
            if($input['link']) {
                $input['link'] = '<script language="javascript" type="text/javascript">window.setTimeout("DZCP.goTo(\''.$input['link'].'\');", '.($input['time']*1000).');</script>'
                . '<noscript><meta http-equiv="refresh" content="'.$input['time'].';url='.$input['link'].'"></noscript>';
            } else { $input['link'] = ''; } unset($input['time']);
            
            $input['status_msg'] = (defined('_notification_'.$input['status']) ? constant('_notification_'.$input['status']) : $input['status']);
            return show("page/notification_box",$input);
        }
        
        if(count(self::$notification_index) >= 1) {
            foreach (self::$notification_index as $data) {
                if($data['link']) {
                    $data['link'] = '<script language="javascript" type="text/javascript">window.setTimeout("DZCP.goTo(\''.$data['link'].'\');", '.($data['time']*1000).');</script>'
                    . '<noscript><meta http-equiv="refresh" content="'.$data['time'].';url='.$data['link'].'"></noscript>';
                } else { $data['link'] = ''; } unset($data['time']);
                $data['status_msg'] = (defined('_notification_'.$data['status']) ? constant('_notification_'.$data['status']) : $data['status']);
                $notification .= show("page/notification_box",$data);
            }
        }
        
        return $notification;
    }
    
    public static function has() {
        return (count(self::$notification_index) >= 1);
    }
    
    public static function is_success() {
        return self::$notification_success;
    }
    
    public static function get_tr($input=false) {
        $notification = self::get($input);
        return (!empty($notification) ? '<tr><td class="contentMainFirst" colspan="2" align="center">'.$notification.'</td></tr>' : '');
    }
    
    public static function set_global($global = true) {
        self::$notification_global = $global;
    }
    
    //Private
    private static function import($status, $msg, $link, $time) {
        $data = array('status' => strtolower($status), 'msg' => $msg, 'link' => $link, 'time' => $time);
        if(self::$notification_global) {
            self::$notification_index[] = $data;
        }
        
        return $data;
    }
}

//-> Startseite fur einen User abrufen
function startpage() {
    global $sql,$userid,$chkMe;
    $startpageID = ($userid >= 1 ? data('startpage') : 0);
    if(!$startpageID) { return 'user/?action=userlobby'; }
    $get = $sql->fetch("SELECT `url`,`level` FROM `{prefix_startpage}` WHERE `id` = ? LIMIT 1",array($startpageID));
    if(!$sql->rowCount()) {
        $sql->update("UPDATE `{prefix_users}` SET `startpage` = 0 WHERE `id` = ?;",array($userid));
        return 'user/?action=userlobby';
    }

    $page = $get['level'] <= $chkMe ? stringParser::decode($get['url']) : 'user/?action=userlobby';
    return (!empty($page) ? $page : 'news/');
}

//-> Nickausgabe mit Profillink oder Emaillink (reg/nicht reg)
function autor($uid=0, $class="", $nick="", $email="", $cut="", $add="") {
    global $sql,$userid;
    $uid = (!$uid ? $userid : $uid);
    if(!$uid) return '* No UserID! *';
    if(!dbc_index::issetIndex('user_'.intval($uid))) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($uid)));
        if($sql->rowCount()) {
            dbc_index::setIndex('user_'.$get['id'], $get);
        } else {
            $nickname = (!empty($cut)) ? cut(stringParser::decode($nick), $cut) : stringParser::decode($nick);
            return CryptMailto($email,_user_link_noreg,array("nick" => $nickname, "class" => $class));
        }
    }

    $nickname = (!empty($cut)) ? cut(stringParser::decode(dbc_index::getIndexKey('user_'.intval($uid), 'nick')), $cut) :stringParser::decode(dbc_index::getIndexKey('user_'.intval($uid), 'nick'));
    return show(_user_link, array("id" => $uid,
                                  "country" => flag(dbc_index::getIndexKey('user_'.intval($uid), 'country')),
                                  "class" => $class,
                                  "get" => $add,
                                  "nick" => $nickname));
}

//-> Nickausgabe mit Profillink (reg + position farbe)
function autorcolerd($uid, $class="", $cut="") {
    global $sql;
    if(!dbc_index::issetIndex('user_'.intval($uid))) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($uid)));
        if($sql->rowCount()) {
            dbc_index::setIndex('user_'.$get['id'], $get);
        }
    }

    $position = dbc_index::getIndexKey('user_'.intval($uid), 'position');
    $get = $sql->fetch("SELECT `id`,`color` FROM `{prefix_positions}` WHERE `id` = ?;",array($position));
    if(!$position || !$sql->rowCount()) {
        return autor($uid,$class,'','',$cut);
    }
    
    $nickname = (!empty($cut)) ? cut(stringParser::decode(dbc_index::getIndexKey('user_'.intval($uid), 'nick')), $cut) :stringParser::decode(dbc_index::getIndexKey('user_'.intval($uid), 'nick'));
    return show(_user_link_colerd, array("id" => $uid,
                                         "country" => flag(dbc_index::getIndexKey('user_'.intval($uid), 'country')),
                                         "class" => $class,
                                         "color" => stringParser::decode($get['color']),
                                         "nick" => $nickname));
}

function cleanautor($uid, $class="", $nick="", $email="") {
    global $sql;
    if(!dbc_index::issetIndex('user_'.intval($uid))) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($uid)));
        if($sql->rowCount()) {
            dbc_index::setIndex('user_' . $get['id'], $get);
        } else {
            return CryptMailto($email, _user_link_noreg, array("nick" => stringParser::decode($nick), "class" => $class));
        }
    }

    return show(_user_link_preview, array("id" => $uid, "country" => flag(dbc_index::getIndexKey('user_'.intval($uid), 'country')),
                                          "class" => $class, "nick" =>stringParser::decode(dbc_index::getIndexKey('user_'.intval($uid), 'nick'))));
}

function rawautor($uid) {
    global $sql;
    if(!dbc_index::issetIndex('user_'.intval($uid))) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($uid)));
        if($sql->rowCount()) {
            dbc_index::setIndex('user_' . $get['id'], $get);
        } else {
            return rawflag('') . " " . jsconvert(stringParser::decode($uid));
        }
    }

    return rawflag(dbc_index::getIndexKey('user_'.intval($uid), 'country'))." ".
    jsconvert(stringParser::decode(dbc_index::getIndexKey('user_'.intval($uid), 'nick')));
}

//-> Nickausgabe ohne Profillink oder Emaillink fr das ForenAbo
function fabo_autor($uid,$tpl=_user_link_fabo) {
    global $sql;
    if(!dbc_index::issetIndex('user_'.intval($uid))) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($uid)));
        if($sql->rowCount()) {
            dbc_index::setIndex('user_' . $get['id'], $get);
            return show($tpl, array("id" => $uid, "nick" => stringParser::decode($get['nick'])));
        }
    } else {
        return show($tpl, array("id" => $uid, "nick" =>stringParser::decode(dbc_index::getIndexKey('user_'.intval($uid), 'nick'))));
    }
    
    return '';
}

function blank_autor($uid) {
    return fabo_autor($uid,_user_link_blank);
}

//-> Rechte abfragen
function jsconvert($txt)
{ return str_replace(array("'","&#039;","\"","\r","\n"),array("\'","\'","&quot;","",""),$txt); }

//-> interner Forencheck
function fintern($id) {
    global $sql,$userid,$chkMe;
    if(!$chkMe) {
        $fget = $sql->fetch("SELECT s1.`intern`,s2.`id` FROM `{prefix_forumkats}` AS `s1` LEFT JOIN `{prefix_forumsubkats}` AS `s2` ON s2.`sid` = s1.`id` WHERE s2.`id` = ?;",array(intval($id)));
        return (!$fget['intern']);
    } else if($chkMe == 4) {
        return true;
    } else {
        $team = $sql->rows("SELECT s1.`id` FROM `{prefix_f_access}` AS `s1` LEFT JOIN `{prefix_userposis}` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = ? AND s2.`posi` != 0 AND s1.`forum` = ?;",array(intval($userid),intval($id)));
        $user = $sql->rows("SELECT `id` FROM `{prefix_f_access}` WHERE `user` = ? AND `forum` = ?;",array(intval($userid),intval($id)));
        return ($user || $team);
    }
}

//-> Einzelne Userdaten ermitteln
function data($what='id',$tid=0) {
    global $sql,$userid;
    if (!$tid) { $tid = $userid; }
    if(!dbc_index::issetIndex('user_'.$tid)) {
        $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($tid)));
        dbc_index::setIndex('user_'.$tid, $get);
    }

    return stripslashes(dbc_index::getIndexKey('user_'.$tid, $what));
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

//-> Einzelne Userstatistiken ermitteln
function userstats($what='id',$tid=0) {
    global $sql,$userid;
    if (!$tid) { $tid = $userid; }
    if(!dbc_index::issetIndex('userstats_'.$tid)) {
        $get = $sql->fetch("SELECT * FROM `{prefix_userstats}` WHERE `user` = ?;",array(intval($tid)));
        dbc_index::setIndex('userstats_'.$tid, $get);
    }

    return stripslashes(dbc_index::getIndexKey('userstats_'.$tid, $what));
}

//- Funktion zum versenden von Emails
function sendMail($mailto,$subject,$content) {
    global $language;
    if(phpmailer_enable) {
        $mail = new PHPMailer;
        switch (settings::get('mail_extension')) {
            case 'smtp':
                $mail->isSMTP();
                $mail->Host = stringParser::decode(settings::get('smtp_hostname'));
                $mail->Port = intval(settings::get('smtp_port'));
                switch (settings::get('smtp_tls_ssl')) {
                    case 1: $mail->SMTPSecure = 'tls'; break;
                    case 2: $mail->SMTPSecure = 'ssl'; break;
                    default: $mail->SMTPSecure = ''; break;
                }
                $mail->SMTPAuth = (empty(settings::get('smtp_username')) && empty(settings::get('smtp_password')) ? false : true);
                $mail->Username = stringParser::decode(settings::get('smtp_username'));
                $mail->Password = session::decode(settings::get('smtp_password'));
            break;
            case 'sendmail':
                $mail->isSendmail();
                $mail->Sendmail = stringParser::decode(settings::get('sendmail_path'));
            break;
        }

        $mail->From = ($mailfrom =stringParser::decode(settings::get('mailfrom')));
        $mail->FromName = $mailfrom;
        $mail->AddAddress(preg_replace('/(\\n+|\\r+|%0A|%0D)/i', '',$mailto));
        $mail->Subject = $subject;
        $mail->msgHTML($content);
        $mail->setLanguage(($language=='deutsch')?'de':'en', basePath.'/inc/lang/sendmail/');
        return $mail->Send();
    } else {
        $mail_logfile = basePath.'/inc/_logs/mail.log'; $maillog = "";
        if(file_exists($mail_logfile)) {
            $maillog = file_get_contents($mail_logfile);
        }
        $mailfrom = stringParser::decode(settings::get('mailfrom'));
        $maillog .= "From: '".$mailfrom."'/n".
                "To: '".preg_replace('/(\\n+|\\r+|%0A|%0D)/i', '',$mailto)."'"."'/n".
                "'".$subject."'/n".$content;
        file_put_contents($mail_logfile, $maillog);
    }
    
    return false;
}

check_msg_emal(); //CALL
function check_msg_emal() {
    global $sql,$httphost,$ajaxJob,$isSpider;
    if(!$ajaxJob && !$isSpider && !$sql->rows("SELECT `id` FROM `{prefix_iptodns}` WHERE `sessid` = ? AND `bot` = 1;",array(session_id()))) {
        $qry = $sql->select("SELECT s1.`an`,s1.`page`,s1.`titel`,s1.`sendmail`,s1.`id` AS `mid`, "
                . "s2.`id`,s2.`nick`,s2.`email`,s2.`pnmail` FROM `{prefix_messages}` AS `s1` "
                . "LEFT JOIN `{prefix_users}` AS `s2` ON s2.`id` = s1.`an` WHERE `page` = 0 AND `sendmail` = 0;");
        if($sql->rowCount()) {
            foreach($qry as $get) {
                if($get['pnmail']) {
                    $sql->update("UPDATE `{prefix_messages}` SET `sendmail` = 1 WHERE `id` = ?;",array($get['mid']));
                    $subj = show(settings::get('eml_pn_subj'), array("domain" => $httphost));
                    $message = show(bbcode_email(settings::get('eml_pn')), array("nick" => stringParser::decode($get['nick']), "domain" => $httphost, "titel" => $get['titel'], "clan" => settings::get('clanname')));
                    sendMail(stringParser::decode($get['email']), $subj, $message);
                }
            }
        }
    }
}

//-> Checkt ob ein Ereignis neu ist
function check_new($datum = 0, $output=false, $datum2 = 0) {
    global $userid;
    if($userid) {
        $lastvisit = userstats('lastvisit', $userid);
        if ($datum >= $lastvisit || $datum2 >= $lastvisit) {
            return (!$output ? true : $output);
        }
    }
    
    return (!$output ? false : '');
}

//-> DropDown Mens Date/Time
function dropdown($what, $wert, $age = 0) {
    if($what == "day") {
        $return = ($age == 1 ? '<option value="" class="dropdownKat">'._day.'</option>'."\n" : '');
        for($i=1; $i<32; $i++) {
            if ($i == $wert) {
                $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
            } else {
                $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
            }
        }
    } else if($what == "month") {
        $return = ($age == 1 ? '<option value="" class="dropdownKat">'._month.'</option>'."\n" : '');
        for($i=1; $i<13; $i++) {
            if ($i == $wert) {
                $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
            } else {
                $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
            }
        }
    } else if($what == "year") {
        if($age == 1) {
            $return ='<option value="" class="dropdownKat">'._year.'</option>'."\n";
            for($i=date("Y",time())-80; $i<date("Y",time())-10; $i++) {
                if ($i == $wert) {
                    $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                } else {
                    $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                }
            }
        } else {
            $return = '';
            for($i=date("Y",time())-3; $i<date("Y",time())+3; $i++) {
                if ($i == $wert) {
                    $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                } else {
                    $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                }
            }
        }
    } else if($what == "hour") {
        $return = '';
        for($i=0; $i<24; $i++) {
            if ($i == $wert) {
                $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
            } else {
                $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
            }
        }
    } else if($what == "minute") {
        $return = '';
        for($i="00"; $i<60; $i++) {
            if($i == 0 || $i == 15 || $i == 30 || $i == 45) {
                if ($i == $wert) {
                    $return .= "<option value=\"" . $i . "\" selected=\"selected\">" . $i . "</option>\n";
                } else {
                    $return .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
                }
            }
        }
    }

    return $return;
}

//Profilfelder konvertieren
function conv($txt) {
    return str_replace(array("ä","ü","ö","Ä","Ü","Ö","ß"), array("ae","ue","oe","Ae","Ue","Oe","ss"), $txt);
}

//-> Geburtstag errechnen
function getAge($bday) {
    if (!empty($bday) && $bday) {
        $bday = date('d.m.Y', $bday);
        list($tiday, $iMonth, $iYear) = explode(".", $bday);
        $iCurrentDay = date('j');
        $iCurrentMonth = date('n');
        $iCurrentYear = date('Y');
        if (($iCurrentMonth > $iMonth) || (($iCurrentMonth == $iMonth) && ($iCurrentDay >= $tiday))) {
            return $iCurrentYear - $iYear;
        } else {
            return $iCurrentYear - ($iYear + 1);
        }
    }
    else {
        return '-';
    }
}

//-> Ausgabe der Position des einzelnen Members
function getrank($tid=0, $squad=0, $profil=false) {
    global $sql,$userid;
    $tid = (!$tid ? $userid : $tid);
    if(!$tid) return '* No UserID! *';
    if($squad) {
        if ($profil) {
            $qry = $sql->select("SELECT s1.`posi`,s2.`name` FROM `{prefix_userposis}` AS `s1` LEFT JOIN `{prefix_squads}` AS `s2` ON s1.`squad` = s2.`id` "
            . "WHERE s1.`user` = ? AND s1.`squad` = ? AND s1.`posi` != 0;",array(intval($tid),intval($squad)));
        } else {
            $qry = $sql->select("SELECT `posi` FROM `{prefix_userposis}` WHERE `user` = ? AND `squad` = ? AND `posi` != 0;",array(intval($tid),intval($squad)));
        }

        if($sql->rowCount()) {
            foreach($qry as $get) {
                $position = $sql->fetch("SELECT `position` FROM `{prefix_positions}` WHERE `id` = ?;",array(intval($get['posi'])),'position');
                $squadname = (!empty($get['name']) ? '<b>' . $get['name'] . ':</b> ' : '');
                return ($squadname.$position);
            }
        } else {
            $get = $sql->fetch("SELECT `level`,`banned` FROM `{prefix_users}` WHERE `id` = ?;",array(intval($tid)));
            if (!$get['level'] && !$get['banned']) {
                return _status_unregged;
            } elseif ($get['level'] == 1) {
                return _status_user;
            } elseif ($get['level'] == 2) {
                return _status_trial;
            } elseif ($get['level'] == 3) {
                return _status_member;
            } elseif ($get['level'] == 4) {
                return _status_admin;
            } elseif (!$get['level'] && $get['banned']) {
                return _status_banned;
            } else {
                return _gast;
            }
        }
    } else {
        $get = $sql->fetch("SELECT s1.*,s2.`position` FROM `{prefix_userposis}` AS `s1` LEFT JOIN `{prefix_positions}` AS `s2` "
        . "ON s1.`posi` = s2.`id` WHERE s1.`user` = ? AND s1.`posi` != 0 ORDER BY s2.pid ASC;",array(intval($tid)));
        if($sql->rowCount()) {
            return $get['position'];
        } else {
            $get = $sql->fetch("SELECT `level`,`banned` FROM `{prefix_users}` WHERE `id` = ?;",array(intval($tid)));
            if (!$get['level'] && !$get['banned']) {
                return _status_unregged;
            } elseif ($get['level'] == 1) {
                return _status_user;
            } elseif ($get['level'] == 2) {
                return _status_trial;
            } elseif ($get['level'] == 3) {
                return _status_member;
            } elseif ($get['level'] == 4) {
                return _status_admin;
            } elseif (!$get['level'] && $get['banned']) {
                return _status_banned;
            } else {
                return _gast;
            }
        }
    }
}

//-> Session fuer den letzten Besuch setzen
function set_lastvisit() {
    global $sql,$useronline,$userid;
    if($userid) {
        if(!$sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `id` = ? AND (time+?)>?;",array(intval($userid),intval($useronline),time()))) {
            $_SESSION['lastvisit'] = intval(data("time"));
        }
    }
}

//-> Checkt welcher User gerade noch online ist
function onlinecheck($tid) {
    global $sql,$useronline;
    $users_id_index = array();
    if (dbc_index::issetIndex('onlinecheck')) {
        $users_id_index = dbc_index::getIndex('onlinecheck');
    }

    if(array_key_exists($tid, $users_id_index)) {
        $row = dbc_index::getIndexKey('onlinecheck', $tid);
    } else {
        $row = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `id` = ? AND (time+?)>? AND `online` = 1;",array(intval($tid),intval($useronline),time()));
        $users_id_index[$tid] = $row;
        dbc_index::setIndex('onlinecheck', $users_id_index);
    }

    return $row ? "<img src=\"../inc/images/online.png\" alt=\"\" class=\"icon\" />" : "<img src=\"../inc/images/offline.png\" alt=\"\" class=\"icon\" />";
}

//Funktion fuer die Sprachdefinierung der Profilfelder
function pfields_name($name) {
    $pattern = array("=_city_=Uis","=_hobbys_=Uis","=_motto_=Uis","=_job_=Uis","=_exclans_=Uis","=_email2_=Uis","=_email3_=Uis","=_autor_=Uis","=_auto_=Uis","=_buch_=Uis",
    "=_drink_=Uis","=_essen_=Uis","=_favoclan_=Uis","=_film_=Uis","=_game_=Uis","=_map_=Uis","=_musik_=Uis","=_person_=Uis","=_song_=Uis","=_spieler_=Uis","=_sportler_=Uis",
    "=_sport_=Uis","=_waffe_=Uis","=_board_=Uis","=_cpu_=Uis","=_graka_=Uis","=_hdd_=Uis","=_headset_=Uis","=_inet_=Uis","=_maus_=Uis","=_mauspad_=Uis","=_monitor_=Uis",
    "=_ram_=Uis","=_system_=Uis");

    $replacement = array(_profil_city,_profil_hobbys,_profil_motto,_profil_job,_profil_exclans,_profil_email2,_profil_email3,_profil_autor,_profil_auto,
    _profil_buch,_profil_drink,_profil_essen,_profil_favoclan,_profil_film,_profil_game,_profil_map,_profil_musik,_profil_person,_profil_song,_profil_spieler,
    _profil_sportler,_profil_sport,_profil_waffe,_profil_board,_profil_cpu,_profil_graka,_profil_hdd,_profil_headset,_profil_inet,_profil_maus,_profil_mauspad,
    _profil_monitor,_profil_ram,_profil_os);

    return preg_replace($pattern, $replacement, $name);
}

//-> Checkt versch. Dinge anhand der Hostmaske eines Users
function ipcheck($what,$time = "") {
    global $sql,$userip;
    $get = $sql->fetch("SELECT `time`,`what` FROM `{prefix_ipcheck}` WHERE `what` = ? AND `ip` = ? ORDER BY `time` DESC;",array($what,$userip));
    if($sql->rowCount()) {
        if (preg_match("#vid#", $get['what'])) {
            return true;
        } else {
            if($get['time'] + $time < time()) {
                $sql->delete("DELETE FROM `{prefix_ipcheck}` WHERE `what` = ? AND `ip` = ? AND time+?<?;",array($what,$userip,$time,time()));
            }

            return ($get['time'] + $time > time() ? true : false);
        }
    }
    
    return false;
}

//-> Gibt die Tageszahl eines Monats aus
function days_in_month($month, $year)
{ return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); }

//-> Setzt bei einem Tag >10 eine 0 vorran (Kalender)
function cal($i) {
    if (preg_match("=10|20|30=Uis", $i) == FALSE) {
        $i = preg_replace("=0=", "", $i);
    }
    
    if ($i < 10) {
        $tag_nr = "0" . $i;
    } else {
        $tag_nr = $i;
    }
    
    return $tag_nr;
}

//->  Umfrageantworten selektieren
function voteanswer($what, $vid) {
    global $sql;
    if(dbc_index::issetIndex('vote_results_'.$vid)) {
        $data = dbc_index::getIndex('vote_results_'.$vid);
    } else {
        $data = $sql->select("SELECT `what`,`sel` FROM `{prefix_vote_results}` WHERE `vid` = ?;",array(intval($vid)));
        dbc_index::setIndex('vote_results_'.$vid, $data);
    }
    
    foreach ($data as $value) {
        if(strtolower($value['what']) == strtolower($what)) {
            return $value['sel'];
        }
    }
    return '';
}

//-> Entfernt fuehrende Nullen bei Monatsangaben
function nonum($i) {
    if (preg_match("=10=Uis", $i) == false) {
        return preg_replace("=0=", "", $i);
    }

    return $i;
}

//-> Konvertiert Platzhalter in die jeweiligen bersetzungen
function navi_name($name) {
    $name = trim($name);
    if(preg_match("#^_(.*?)_$#Uis",$name)) {
        $name = preg_replace("#_(.*?)_#Uis", "$1", $name);
        if (defined("_" . $name)) {
            return constant("_" . $name);
        }
    }

    return $name;
}

// Userpic ausgeben
function userpic($userid, $width=170,$height=210) {
    global $picformat;
    foreach($picformat as $endung) {
        if (file_exists(basePath . "/inc/images/uploads/userpics/" . $userid . "." . $endung)) {
            $pic = show(_userpic_link, array("id" => $userid, "endung" => $endung, "width" => $width, "height" => $height));
            break;
        } else {
            $pic = show(_no_userpic, array("width" => $width, "height" => $height));
        }
    }

    return $pic;
}

// Useravatar ausgeben
function useravatar($uid=0, $width=100,$height=100) {
    global $picformat,$userid;
    $uid = ($uid == 0 ? $userid : $uid);
    foreach($picformat as $endung) {
        if (file_exists(basePath . "/inc/images/uploads/useravatare/" . $uid . "." . $endung)) {
            $pic = show(_userava_link, array("id" => $uid, "endung" => $endung, "width" => $width, "height" => $height));
            break;
        } else {
            $pic = show(_no_userava, array("width" => $width, "height" => $height));
        }
    }

    return $pic;
}

// Userpic fuer Hoverinformationen ausgeben
function hoveruserpic($userid, $width=170,$height=210) {
    global $picformat;
    $pic = "../inc/images/nopic.gif', '".$width."', '".$height;
    foreach($picformat as $endung) {
        if(file_exists(basePath."/inc/images/uploads/userpics/".$userid.".".$endung)) {
            $pic = "../inc/images/uploads/userpics/".$userid.".".$endung."', '".$width."', '".$height."";
            break;
        }
    }

    return $pic;
}

// Adminberechtigungen ueberpruefen
function admin_perms($userid) {
    global $sql,$chkMe;
    if (empty($userid)) {
        return false;
    }

    if(rootAdmin($userid)) {
        return true;
    }
    
    // no need for these admin areas & check user permission
    $e = array('gb', 'shoutbox', 'editusers', 'votes', 'contact', 'joinus', 'intnews', 'forum', 
    'gs_showpw','dlintern','intforum','galleryintern');
    
    $qry = $sql->fetch("SELECT * FROM `{prefix_permissions}` WHERE `user` = ?;",array(intval($userid)));
    if($sql->rowCount()) {
        foreach($qry as $v => $k) {
            if($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                if($k == 1) {
                    return true;
                    break;
                }
            }
        }
    }

   // check rank permission
    $qry = $sql->select("SELECT s1.* FROM `{prefix_permissions}` AS `s1` LEFT JOIN `{prefix_userposis}` AS `s2` ON s1.`pos` = s2.`posi` WHERE s2.`user` = ? AND s2.`posi` != 0;",array(intval($userid)));
    foreach($qry as $get) {
        foreach($get AS $v => $k) {
            if($v != 'id' && $v != 'user' && $v != 'pos' && !in_array($v, $e)) {
                if($k == 1) {
                    return true;
                    break;
                }
            }
        }
    }

    return ($chkMe == 4) ? true : false;
}

/**
 * Erkennt Spider und Crawler um sie von der Besucherstatistik auszuschliessen.
 * @return boolean
 */
function isSpider($SetUserAgent=false) {
    $bots_basic = array('bot', 'b o t', 'spider', 'spyder', 'crawl', 'slurp', 'robo', 'yahoo', 'ask', 'google', '80legs', 'acoon',
            'altavista', 'al_viewer', 'appie', 'appengine-google', 'arachnoidea', 'archiver', 'asterias', 'ask jeeves', 'beholder',
            'bildsauger', 'bingsearch', 'bingpreview', 'bumblebee', 'bramptonmoose', 'cherrypicker', 'crescent', 'coccoc', 'cosmos',
            'docomo', 'drupact', 'emailsiphon', 'emailwolf', 'extractorpro', 'exalead ng', 'ezresult', 'feedfetcher', 'fido', 'fireball',
            'flipboardproxy', 'gazz', 'getweb', 'gigabaz', 'gulliver', 'harvester', 'hcat', 'heritrix', 'hloader', 'hoge', 'httrack',
            'incywincy', 'infoseek', 'infohelfer', 'inktomi', 'indy library', 'informant', 'internetami', 'internetseer', 'link', 'larbin',
            'jakarta', 'mata hari', 'medicalmatrix', 'mercator', 'miixpc', 'moget', 'msnptc', 'muscatferret', 'netcraftsurveyagent',
            'openxxx', 'picmole', 'piranha', 'pldi.net', 'p357x', 'quosa', 'rambler', 'rippers', 'rganalytics', 'scan', 'scooter', 'ScoutJet',
            'siclab', 'siteexplorer', 'sly', 'searchme', 'spy', 'swisssearch', 'sqworm', 'trivial', 't-h-u-n-d-e-r-s-t-o-n-e', 'teoma',
            'twiceler', 'ultraseek', 'validator', 'webbandit', 'webmastercoffee', 'webwhacker', 'wevika', 'wisewire', 'yandex', 'zyborg',
            'Teoma', 'alexa', 'froogle', 'Gigabot', 'inktomi', 'looksmart', 'URL_Spider_SQL', 'Firefly', 'NationalDirectory', 'Ask Jeeves', 'TECNOSEEK', 
            'InfoSeek', 'WebFindBot', 'girafabot', 'crawler', 'www.galaxy.com', 'Googlebot', 'Googlebot/2.1', 'Google', 'Google Webmaster', 'Scooter', 
            'James Bond', 'Slurp', 'msnbot', 'appie', 'FAST', 'WebBug', 'Spade', 'ZyBorg', 'rabaz', 'Baiduspider', 'Feedfetcher-Google',
            'TechnoratiSnoop', 'Rankivabot', 'Mediapartners-Google', 'Sogou web spider', 'WebAlta Crawler', 'MJ12bot',
            'Yandex', 'YaDirectBot', 'StackRambler','DotBot','dotbot');

    $UserAgent = ($SetUserAgent ? $SetUserAgent : trim(GetServerVars('HTTP_USER_AGENT')));
    foreach ($bots_basic as $bot) {
        if(stristr($UserAgent, $bot) !== FALSE || strpos($bot, $UserAgent)) {
            return true;
        }
    }

    //Old DZCP Spiders Text
    if(file_exists(basePath.'/inc/_spiders.txt')) {
        $ex = explode("\n", file_get_contents(basePath.'/inc/_spiders.txt'));
        for($i=0;$i<=count($ex)-1;$i++) {
            if(stristr($UserAgent, trim($ex[$i]))) {
                return true;
            }
        }
    }
    
    return false;
}

//-> Zugriffsberechtigung auf die Seite
function check_internal_url() {
    global $sql,$chkMe;
    if ($chkMe >= 1) {
        return false;
    }
    $install_pfad = explode("/",dirname(dirname(GetServerVars('SCRIPT_NAME'))."../"));
    $now_pfad = explode("/",GetServerVars('REQUEST_URI')); $pfad = '';
    foreach($now_pfad as $key => $value) {
        if(!empty($value)) {
            if(!isset($install_pfad[$key]) || $value != $install_pfad[$key]) {
                $pfad .= "/".$value;
            }
        }
    }

    list($pfad) = explode('&',$pfad);
    $pfad = "..".$pfad;

    if (strpos($pfad, "?") === false && strpos($pfad, ".php") === false) {
        $pfad .= "/";
    }

    if (strpos($pfad, "index.php") !== false) {
        $pfad = str_replace('index.php', '', $pfad);
    }

    $url = $pfad.'index.php';
    $get_navi = $sql->fetch("SELECT `internal` FROM `{prefix_navi}` WHERE `url` = ? OR `url` = ?;",array($pfad,$url));
    if($sql->rowCount()) {
        if ($get_navi['internal']) {
            return true;
        }
    }

    return false;
}

//-> Rechte abfragen
function getPermissions($checkID = 0, $pos = 0) {
    global $sql;
    //Rechte des Users oder des Teams suchen
    if(!empty($checkID)) {
        $check = empty($pos) ? 'user' : 'pos'; $checked = array();
        $qry = $sql->fetch("SELECT * FROM `{prefix_permissions}` WHERE `".$check."` = ?;",array(intval($checkID)));
        if ($sql->rowCount()) {
            foreach($qry as $k => $v) {
                if($k != 'id' && $k != 'user' && $k != 'pos' && $k != 'intforum') {
                    $checked[$k] = $v;
                }
            }
        }
    }

    //Liste der Rechte zusammenstellen
    $permission = array();
    $qry = $sql->show("SHOW COLUMNS FROM `dzcp_permissions`;");
    if($sql->rowCount()) {
        foreach($qry as $get) {
            if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                $lang = constant('_perm_'.$get['Field']);
                $chk = empty($checked[$get['Field']]) ? '' : ' checked="checked"';
                $permission[$lang] = '<input type="checkbox" class="checkbox" id="'.$get['Field'].'" name="perm[p_'.$get['Field'].']" value="1"'.$chk.' /><label for="'.$get['Field'].'"> '.$lang.'</label> ';
            }
        }
    }

    $permissions = '';
    if(count($permission)) {
        natcasesort($permission); $break = 1;
        foreach($permission AS $perm) {
            $br = ($break % 2) ? '<br />' : ''; $break++;
            $permissions .= $perm.$br;
        }
    }

    return $permissions;
}

//-> interne Foren-Rechte abfragen
function getBoardPermissions($checkID = 0, $pos = 0) {
    global $sql;
    $break = 0; $i_forum = ''; $fkats = '';
    $qry = $sql->select("SELECT `id`,`name` FROM `{prefix_forumkats}` WHERE `intern` = 1 ORDER BY `kid` ASC;");
    if($sql->rowCount()) {
        foreach($qry as $get) {
            unset($kats, $fkats, $break);
            $kats = (empty($katbreak) ? '' : '<div style="clear:both">&nbsp;</div>').'<table class="hperc" cellspacing="1"><tr><td class="contentMainTop"><b>'.stringParser::decode($get["name"]).'</b></td></tr></table>';
            $katbreak = 1; $break = 0; $fkats = '';

            $qry2 = $sql->select("SELECT `kattopic`,`id` FROM `{prefix_forumsubkats}` WHERE `sid` = ? ORDER BY `kattopic` ASC;",array($get['id'],));
            if($sql->rowCount()) {
                foreach($qry2 as $get2) {
                    $br = ($break % 2) ? '<br />' : ''; $break++;
                    $chk = ($sql->rows("SELECT `id` FROM `{prefix_f_access}` WHERE `".(empty($pos) ? 'user' : 'pos')."` = ? AND ".(empty($pos) ? 'user' : 'pos')." != 0 AND `forum` = ?;",array(intval($checkID),$get2['id'])) ? ' checked="checked"' : '');
                    $fkats .= '<input type="checkbox" class="checkbox" id="board_'.$get2['id'].'" name="board['.$get2['id'].']" value="'.$get2['id'].'"'.$chk.' /><label for="board_'.$get2['id'].'"> '.stringParser::decode($get2['kattopic']).'</label> '.$br;
                }
            }

            $i_forum .= $kats.$fkats;
        }
    }

    return $i_forum;
}

//-> schreibe in dei IPCheck Tabelle
function setIpcheck($what = '',$time = true) {
    global $sql;
    $sql->insert("INSERT INTO `{prefix_ipcheck}` SET `ip` = ?, `user_id` = ?, `what` = ?, `time` = ?, `created` = ?;",
            array(visitorIp(),intval(userid()),$what,($time ? time() : 0),time()));
}

//-> Preuft ob alle clicks nur einmal gezahlt werden *gast/user
function count_clicks($side_tag='',$clickedID=0,$update=true) {
    global $sql,$userip,$userid,$chkMe,$isSpider;
    if(!$isSpider) {
        $qry = $sql->select("SELECT `id`,`side` FROM `{prefix_clicks_ips}` WHERE `uid` = 0 AND `time` <= ?;",array(time()));
        if($sql->rowCount()) {
            foreach($qry as $get) {
                if($get['side'] != 'vote') {
                    $sql->delete("DELETE FROM `{prefix_clicks_ips}` WHERE `id` = ?;",array($get['id']));
                }
            }
        }

        if($chkMe != 'unlogged') {
            if ($sql->rows("SELECT `id` FROM `{prefix_clicks_ips}` WHERE `uid` = ? AND `ids` = ? AND `side` = ?;", array(intval($userid),intval($clickedID),$side_tag))) {
                return false;
            }

            if($sql->rows("SELECT `id` FROM `{prefix_clicks_ips}` WHERE `ip` = ? AND `ids` = ? AND `side` = ?;",array($userip,intval($clickedID),$side_tag))) {
                if($update) {
                    $sql->update("UPDATE `{prefix_clicks_ips}` SET `uid` = ?, `time` = ? WHERE `ip` = ? AND `ids` = ? AND `side` = ?;",
                    array(intval($userid),(time()+count_clicks_expires),$userip,intval($clickedID),$side_tag));
                }

                return false;
            } else {
                if($update) {
                    $sql->insert("INSERT INTO `{prefix_clicks_ips}` SET `ip` = ?, `uid` = ?, `ids` = ?, `side` = ?, `time` = ?;",
                    array($userip, intval($userid), intval($clickedID), $side_tag, (time() + count_clicks_expires)));
                }
                
                return true;
            }
        } else {
            if(!$sql->rows("SELECT id FROM `{prefix_clicks_ips}` WHERE `ip` = ? AND `ids` = ? AND `side` = ?;",array($userip,intval($clickedID),$side_tag))) {
                if($update) {
                    $sql->insert("INSERT INTO `{prefix_clicks_ips}` SET `ip` = ?, `uid` = 0, `ids` = ?, `side` = ?, `time` = ?;",
                    array($userip,intval($clickedID),$side_tag,(time()+count_clicks_expires)));
                }
                
                return true;
            }
        }
    }

    return false;
}

function is_php($version='5.3.0') { 
    return (floatval(phpversion()) >= $version); 
}

function hextobin($hexstr) {
    if (is_php('5.4.0')) {
        return hex2bin($hexstr);
    }
    // < PHP 5.4
    $n = strlen($hexstr);
    $sbin="";
    $i=0;
    while($i<$n) {
        $a =substr($hexstr,$i,2);
        $c = pack("H*",$a);
        if ($i==0){$sbin=$c;}
        else {$sbin.=$c;}
        $i+=2;
    }

    return $sbin;
}

function db_optimize() {
    global $sql,$securimage;
    $qry = $sql->select("SELECT `id`,`update`,`expires` FROM `{prefix_autologin}` LIMIT 100;");
    if($sql->rowCount()) {
        foreach($qry as $get) {
            if(($get['update'] && (($get['update'] + $get['expires']) >= time()))) {
                $sql->delete("DELETE FROM `{prefix_autologin}` WHERE `id` = ?;",array($get['id']));
            }
        }
    }

    $securimage->clearOldCodesFromDatabase();
    $sql->query("TRUNCATE `{prefix_iptodns}`;");
    if(sessions_backend == 'mysql') {
        $sql->query("TRUNCATE `{prefix_sessions}`;");
    }

    $qry = $sql->show("SHOW TABLES FROM `".$sql->getConfig('db')."`"); $qry_string = "";
    foreach($qry as $tb) {
        $qry_string .= '`'.$tb['Tables_in_'.$sql->getConfig('db')].'`, ';
    }

    $sql->optimize('OPTIMIZE TABLE '.substr($qry_string, 0, -2).';');
}

//-> Codiert Text zur Speicherung
final class stringParser {
    /**
     * Codiert Text in das UTF8 Charset.
     *
     * @param string $txt
     */
    public static function encode($txt='') { 
        return utf8_encode(stripcslashes(spChars(htmlentities($txt, ENT_COMPAT, 'UTF-8')))); 
    }

    /**
     * Decodiert UTF8 Text in das aktuelle Charset der Seite.
     *
     * @param utf8 string $txt
     */
    public static function decode($txt='') { 
        return trim(stripslashes(spChars(html_entity_decode(utf8_decode($txt), ENT_COMPAT, 'UTF-8'),true))); 
    }
}

//-> Speichert Ruckgaben der MySQL Datenbank zwischen um SQL-Queries einzusparen
final class dbc_index {
    private static $index = array();
    private static $is_mem = false;

    public static final function init() {
        global $cache,$config_cache;
        if(!$config_cache['use_cache']) { self::$is_mem = false; return; }
        if(!$config_cache['dbc']) { self::$is_mem = false; return; }
        self::$is_mem = $cache->isMemModule();
    }

    public static final function setIndex($index_key,$data,$time=2) {
        global $cache,$config_cache;
        if(self::$is_mem) {
            if (show_dbc_debug) {
                DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "' . $index_key . '" to cache');
            }

            if ($config_cache['use_cache']) {
                $cache->set('dbc_' . $index_key, serialize($data), $time);
            }
        }

        if (show_dbc_debug) {
            DebugConsole::insert_info('dbc_index::setIndex()', 'Set index: "' . $index_key . '"');
        }

        self::$index[$index_key] = $data;
    }

    public static final function getIndex($index_key) {
        if (!self::issetIndex($index_key)) {
            return false;
        }

        if (show_dbc_debug) {
            DebugConsole::insert_info('dbc_index::getIndex()', 'Get full index: "' . $index_key . '"');
        }

        return self::$index[$index_key];
    }

    public static final function getIndexKey($index_key,$key) {
        if (!self::issetIndex($index_key)) {
            return false;
        }

        $data = self::$index[$index_key];
        if (empty($data) || !array_key_exists($key, $data)) {
            return false;
        }

        if (show_dbc_debug) {
            DebugConsole::insert_info('dbc_index::getIndexKey()', 'Get from index: "' . $index_key . '" get key: "' . $key . '"');
        }

        return $data[$key];
    }

    public static final function issetIndex($index_key) {
        global $cache,$config_cache;
        if(array_key_exists($index_key, self::$index)) {
            return true;
        }
        
        if(self::$is_mem && $config_cache['use_cache'] && $cache->isExisting('dbc_'.$index_key)) {
            if (show_dbc_debug) {
                DebugConsole::insert_loaded('dbc_index::issetIndex()', 'Load index: "' . $index_key . '" from cache');
            }

            self::$index[$index_key] = unserialize($cache->get('dbc_'.$index_key));
            return true;
        }

        return false;
    }

    public static final function useMem() {
        return self::$is_mem;
    }
}

/**
 * Gibt die vergangene zeit zwischen $timestamp und $aktuell als lesbaren string zurueck.
 * bsp: 3 Wochen, 4 Tage, 5 Sekunden
 * @param int $timestamp * der timestamp der ersten zeit-marke.
 * @param int $aktuell * der timestamp der zweiten zeit-marke. * aktuelle zeit *
 * @param int $anzahl_einheiten * wie viele einheiten sollen maximal angezeigt werden
 * @param boolean $zeige_leere_einheiten * sollen einheiten, die den wert 0 haben, angezeigt werden?
 * @param array $zeige_einheiten * zeige nur angegebene einheiten. jahre werden zb in sekunden umgerechnet
 * @param string $standard * falls der timestamp 0 oder ungueltig ist, gebe diesen string zurueck
 * @return string
 */
function get_elapsed_time( $timestamp, $aktuell = null, $anzahl_einheiten = null, $zeige_leere_einheiten = null, $zeige_einheiten = null, $standard = null ) {
    if ( $aktuell === null ) $aktuell = time();
    if ( $anzahl_einheiten === null ) $anzahl_einheiten = 1;
    if ( $zeige_leere_einheiten === null ) $zeige_leere_einheiten = true;
    if ( !is_array( $zeige_einheiten ) ) $zeige_einheiten = array();
    if ( $standard === null ) $standard = "nie";
    if ( $timestamp == 0 ) return $standard;
    if ( $timestamp > $aktuell ) $timestamp = $aktuell;
    if ( $anzahl_einheiten < 1 ) $anzahl_einheiten = 10;
    $zeit = bcsub( $aktuell, $timestamp );
    if ( $zeit < 1 ) $zeit = 1; $arr = array();
    $werte = array( 63115200 => _years, 31557600 => _year.' ', 4838400 => _months, 2419200 => _month.' ',
            1209600 => _weeks, 604800 => _week.' ', 172800 => _days.' ', 86400 => _day.' ', 7200 => _hours,
            3600 => _hour.' ', 120 => _minutes, 60 => _minute.' ',  1 => _seconds );

    if ( ( is_array( $zeige_einheiten ) ) and ( count( $zeige_einheiten ) > 0 ) ) {
        $neu = array();
        foreach ( $werte as $key => $val ) {
            if ( in_array( $val, $zeige_einheiten ) )
                $neu[$key] = $val;
        }

        $werte = $neu;
    }

    foreach ( $werte as $div => $einheit ) {
        if ( $zeit < $div ) {
            if ( count( $arr ) != 0 )
                $arr[$einheit] = 0;

            continue;
        }

        $anzahl = bcdiv( $zeit, $div );
        $zeit -= bcmul( $anzahl, $div );
        $arr[$einheit] = $anzahl;
    }

    reset( $arr ); $output = 0; $ret = "";
    while ( ( count( $arr ) > 0 ) and ( $output < $anzahl_einheiten ) ) {
        $key = key( $arr );
        $cur = current( $arr );
        $einheit = ( $cur == 1 ) ? substr( $key, 0, bcsub( strlen( $key ), 1 ) ) : $key;
        if ( ( $cur != 0 ) or ( $zeige_leere_einheiten == true ) )
            $ret .= ( empty( $ret ) )
            ? ($anzahl_einheiten == 1 ? round($cur, 0, PHP_ROUND_HALF_DOWN) : $cur) . " " . $einheit
            : ", " . ($anzahl_einheiten == 1 ? round($cur, 0, PHP_ROUND_HALF_DOWN) : $cur) . " " . $einheit;
        $output++;
        unset( $arr[$key] );
    }
    return $ret;
}

/**
 * Verschlusselt eine E-Mail Adresse per Javascript
 * @param string $email
 * @param string $template
 * @return string
 */
function CryptMailto($email='',$template=_emailicon,$custom=array()) {
    if(empty($template) || empty($email) || !permission("editusers")) return '';
    $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
    $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999);
    for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
    $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
    $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
    if(!empty($custom) && count($custom) >= 1) $template = show($template,$custom);
    $script.= 'document.getElementById("'.$id.'").innerHTML="'.$template.'"';
    $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
    $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';
    return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
}

/**
 * Generiert eine XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX unique id
 *
 * @return string
 */
function GenGuid() {
    $s = strtoupper(md5(uniqid(rand(),true)));
    return substr($s,0,8) .'-'.substr($s,8,4).'-'.substr($s,12,4).'-'.substr($s,16,4).'-'. substr($s,20);
}

//-> DZCP-API Klassen einbinden
if($api_files = get_files(basePath.'/inc/api/',false,true,array('php'))) {
    foreach($api_files AS $api)
    { include(basePath.'/inc/api/'.$api); }
    unset($api_files,$api);
}

//-> Laden der Menus
$menu_index = array();
if($menu_functions_index = get_files(basePath.'/inc/menu-functions',false,true,array('php'))) {
    foreach ($menu_functions_index as $mfphp) {
        $file = str_replace('.php', '', $mfphp);
        if($file != 'navi') {
           $menu_index[$file] = file_exists(basePath.'/inc/menu-functions/'.$file.'.php');
        }
    } unset($menu_functions_index,$file,$mfphp);
}

//-> Neue Languages einbinden, sofern vorhanden
if($language_files = get_files(basePath.'/inc/additional-languages/'.$language.'/',false,true,array('php'))) {
    foreach($language_files AS $languages)
    { include(basePath.'/inc/additional-languages/'.$language.'/'.$languages); }
    unset($language_files,$languages);
}

//-> Neue Funktionen einbinden, sofern vorhanden
if($functions_files = get_files(basePath.'/inc/additional-functions/',false,true,array('php'))) {
    foreach($functions_files AS $func)
    { include(basePath.'/inc/additional-functions/'.$func); }
    unset($functions_files,$func);
}

//-> Navigation einbinden
if (file_exists(basePath . '/inc/menu-functions/navi.php')) {
    include_once(basePath . '/inc/menu-functions/navi.php');
}

//-> Verbertet wichtige Informationen zwischen JS und PHP
class javascript {
    private static $data_array = array();

    public static function set($key='',$var='') { 
        self::$data_array[$key] = utf8_encode($var);
    }

    public static function remove($key='') { 
        unset(self::$data_array[$key]);
    }

    public static function get($key='') { 
        return utf8_decode(self::$data_array[$key]);
    }

    public static function encode() { 
        return json_encode(self::$data_array);
    }
}

//-> Ausgabe des Indextemplates
function page($index='',$title='',$where='',$index_templ='index') {
    global $userid,$userip,$tmpdir,$chkMe,$dir,$view_error;
    global $designpath,$language,$menu_index,$isSpider,$cache;

    javascript::set('lng',($language=='deutsch'?'de':'en'));
    javascript::set('maxW',settings::get('maxwidth'));
    javascript::set('shoutInterval',15000);  // refresh interval of the shoutbox in ms
    javascript::set('slideshowInterval',6000);  // refresh interval of the shoutbox in ms
    javascript::set('autoRefresh',1);  // Enable Auto-Refresh for Ajax
    javascript::set('debug',view_javascript_debug);  // Enable JS Debug
    javascript::set('dir',$designpath);  // Designpath
    javascript::set('dialog_button_00',_yes); 
    javascript::set('dialog_button_01',_no); 

    // JS-Dateine einbinden * json *
    $java_vars = '<script language="javascript" type="text/javascript">var json=\''.javascript::encode().'\',dzcp_config=JSON&&JSON.parse(json)||$.parseJSON(json);</script>'."\n";

    if(settings::get("wmodus") && $chkMe != 4) {
        $demo = (dzcp_demo ? '<tr><td class="contentMainTop" colspan="2" align="center"><span class="fontBold">Login-Name: '.stringParser::decode(data('user',1)).'</span></td></tr>'
                . '<tr><td class="contentMainTop" colspan="2" align="center"><span class="fontBold">Passwort: '.$_SESSION['pwd_demo'].'</span></td></tr>' : '');
        $login = show("errors/wmodus_login", array("secure" => settings::get('securelogin') ? show("user/secure") : '', "demo" => $demo));
        cookie::save(); //Save Cookie
        echo show("errors/wmodus", array("tmpdir" => $tmpdir,
                                         "java_vars" => $java_vars,
                                         "dir" => $designpath,
                                         "sid" => (float)rand()/(float)getrandmax(),
                                         "title" => strip_tags(stringParser::decode($title)),
                                         "login" => $login));
    } else {
        if(!$isSpider) {
            updateCounter();
            update_maxonline();
        }

        //check permissions
        if(!$chkMe) {
            $secure = settings::get('securelogin') ? show("menu/secure") : '';
            $login = show("menu/login", array("secure" => $secure));
            $check_msg = '';
        } else {
            $check_msg = check_msg(); 
            set_lastvisit();
            $login = "";
        }

        //init templateswitch
        $tmpldir=""; $tmps = get_files(basePath.'/inc/_templates_/',true);
        foreach ($tmps as $tmp) {
            $selt = ($tmpdir == $tmp ? 'selected="selected"' : '');
            $tmpldir .= show(_select_field, array("value" => "?tmpl_set=".$tmp,  "what" => $tmp,  "sel" => $selt));
        }

        //misc vars
        $lang = $language;
        $template_switch = show("menu/tmp_switch", array("templates" => $tmpldir));
        $clanname =stringParser::decode(settings::get("clanname"));
        $headtitle = show(_index_headtitle, array("clanname" => $clanname));
        $rss = $clanname;
        $title =stringParser::decode(strip_tags($title));
        $notification = notification::get();
        $notification_tr = notification::get_tr();

        if (check_internal_url()) {
            $index = error(_error_have_to_be_logged, 1);
        }

        $where = preg_replace_callback("#autor_(.*?)$#",create_function('$id', 'return stringParser::decode(data("nick","$id[1]"));'),$where);
        $index = empty($index) ? '' : (!$check_msg ? '' : $check_msg).'<table class="mainContent" cellspacing="1">'.$index.'</table>';
        update_online($where); //Update Stats

        //template index autodetect
        $index_templ = ($index_templ == 'index' && file_exists($designpath.'/index_'.$dir.'.html') ? 'index_'.$dir : $index_templ);
        //check if placeholders are given
        $pholder_hash = md5($designpath."/".$index_templ.".html");
        if(!$cache->isExisting($pholder_hash) || !template_cache || !$cache->isMemModule()) {
            $pholder = file_get_contents($designpath."/".$index_templ.".html");
            if(template_cache && $cache->isMemModule()) {
                $cache->set($pholder_hash, $pholder, template_cache_time);
            }
        } else {
            $pholder = $cache->get($pholder_hash);
        }

        //filter placeholders
        $dir = $designpath; //after template index autodetect!!!
        $blArr = array("[clanname]","[title]","[java_vars]","[template_switch]","[headtitle]","[login]",
        "[index]","[rss]","[dir]","[where]","[lang]","[notification]","[notification_tr]");
        $pholdervars = '';
        for($i=0;$i<=count($blArr)-1;$i++) {
            if (preg_match("#" . $blArr[$i] . "#", $pholder)) {
                $pholdervars .= $blArr[$i];
            }
        }

        for ($i = 0; $i <= count($blArr) - 1; $i++) {
            $pholder = str_replace($blArr[$i], "", $pholder);
        }

        $pholder = pholderreplace($pholder);
        $pholdervars = pholderreplace($pholdervars);

        //put placeholders in array
        $arr = array();
        $pholder = explode("^",$pholder);
        for($i=0;$i<=count($pholder)-1;$i++) {
            if (strstr($pholder[$i], 'nav_')) {
                $arr[$pholder[$i]] = navi($pholder[$i]);
            } else {
                if (array_key_exists($pholder[$i], $menu_index) && $menu_index[$pholder[$i]]) {
                    require_once(basePath . '/inc/menu-functions/' . $pholder[$i] . '.php');
                }

                if (function_exists($pholder[$i])) {
                    $arr[$pholder[$i]] = $pholder[$i]();
                }
            }
        }
        
        
        $pholdervars = explode("^",$pholdervars);
        foreach ($pholdervars as $pholdervar) {
            $arr[$pholdervar] = $$pholdervar;
        }

        $arr['sid'] = (float)rand()/(float)getrandmax(); //Math.random() like

        //index output
        $index = (file_exists(basePath."/inc/_templates_/".$tmpdir."/".$index_templ.".html") ? show($index_templ, $arr) : show("index", $arr));
        cookie::save(); //Save Cookie
        if (debug_save_to_file) {
            DebugConsole::save_log();
        } //Debug save to file
        $output = view_error_reporting || DebugConsole::get_warning_enable() ? DebugConsole::show_logs().$index : $index; //Debug Console + Index Out
        gz_output($output); // OUTPUT BUFFER END
    }
}

//###################################
//BBCodeParser Class
//###################################
class bbcode {
    private static $string = '';
    private static $vid_count = 0;
    private static $gl_words = array();
    private static $gl_desc = array();
    private static $use_glossar = true;
    private static $simple_search = array(
      '/\[b\](.*?)\[\/b\]/is',
      '/\[i\](.*?)\[\/i\]/is',
      '/\[u\](.*?)\[\/u\]/is',
      '/\[s\](.*?)\[\/s\]/is',
      '/\[size\=(.*?)\](.*?)\[\/size\]/is',
      '/\[color\=(.*?)\](.*?)\[\/color\]/is',
      '/\[center\](.*?)\[\/center\]/is',
      '/\[font\=(.*?)\](.*?)\[\/font\]/is',
      '/\[align\=(left|center|right)\](.*?)\[\/align\]/is',

      '/\[left\](.*?)\[\/left\]/is',
      '/\[right\](.*?)\[\/right\]/is',

      '/\[url\](.*?)\[\/url\]/is',
      '/\[url\=(.*?)\](.*?)\[\/url\]/is',
      '/\[mail\=(.*?)\](.*?)\[\/mail\]/is',
      '/\[mail\](.*?)\[\/mail\]/is',
      '/\[img\](.*?)\[\/img\]/is',
      '/\[img\=(\d*?)x(\d*?)\](.*?)\[\/img\]/is',
      '/\[img (.*?)\](.*?)\[\/img\]/is',

      '/\[quote\](.*?)\[\/quote\]/is',
      '/\[quote\=(.*?)\](.*?)\[\/quote\]/is',

      '/\[sub\](.*?)\[\/sub\]/is',
      '/\[sup\](.*?)\[\/sup\]/is',
      '/\[p\](.*?)\[\/p\]/is',

      '/\[bull \/\]/i',
      '/\[copyright \/\]/i',
      '/\[registered \/\]/i',
      '/\[tm \/\]/i');

    private static $simple_replace = array(
      '<strong>$1</strong>',
      '<em>$1</em>',
      '<u>$1</u>',
      '<del>$1</del>',
      '<span style="font-size: $1;">$2</span>',
      '<span style="color: $1;">$2</span>',
      '<div style="text-align: center;">$1</div>',
      '<span style="font-family: $1;">$2</span>',
      '<div style="text-align: $1;">$2</div>',

      '<div style="text-align: left;">$2</div>',
      '<div style="text-align: right;">$2</div>',

      '<a href="$1">$1</a>',
      '<a href="$1">$2</a>',
      '<a href="mailto:$1">$2</a>',
      '<a href="mailto:$1">$1</a>',
      '<img src="$1" alt="" />',
      '<img height="$2" width="$1" alt="" src="$3" />',
      '"<img " . str_replace("&#039;", "\"",str_replace("&quot;", "\"", "$1")) . " src=\"$2\" />"',

      '<blockquote>$1</blockquote>',
      '<blockquote><strong>$1 wrote:</strong> $2</blockquote>',

      '<sub>$1</sub>',
      '<sup>$1</sup>',
      '<p>$1</p>',

      '&bull;',
      '&copy;',
      '&reg;',
      '&trade;');

    private static $lineBreaks_search = array(
      '/\[list(.*?)\](.+?)\[\/list\]/si',
      '/\[\/list\]\s*\<br \/\>/i',
      '/\[\/code\]\s*\<br \/\>/i',
      '/\[\/quote\]\s*\<br \/\>/i',
      '/\[\/p\]\s*\<br \/\>/i',
      '/\[\/center\]\s*\<br \/\>/i',
      '/\[\/align\]\s*\<br \/\>/i');

    private static $lineBreaks_replace = array(
      "'[list$1]'.str_replace('<br />', '', '$2').'[/list]'",
      "[/list]",
      "[/code]",
      "[/quote]",
      "[/p]",
      "[/center]",
      "[/align]");

    private static $vid_search = array(
            "/\[googlevideo\](.*?)\[\/googlevideo\]/",
            "/\[myvideo\](.*?)\[\/myvideo\]/",
            "/\[youtube\](?:http?s?:\/\/)?(?:www\.)?youtu(?:\.be\/|be\.com\/watch\?v=)([A-Z0-9\-_]+)(?:&(.*?))?\[\/youtube\]/i",
            "/\[divx\](.*?)\[\/divx\]/",
            "/\[vimeo\]([0-9]{0,})\[\/vimeo\]/",
            "/\[golem\](.*?)\[\/golem\]/");

    private static $vid_replace = array(
            "<embed id=VideoPlayback src=http://video.google.de/googleplayer.swf?docid=-$1&hl=de&fs=true style=width:425px;height:344px allowFullScreen=true allowScriptAccess=always type=application/x-shockwave-flash> </embed>",

            "<object wmode=\"opaque\" style=\"width: 425px; height: 344px;\" type=\"application/x-shockwave-flash\" data=\"http://www.myvideo.de/movie/$1\"> "
        . "</param><param name=\"wmode\" value=\"opaque\"><param name=\"movie\" value=\"http://www.myvideo.de/movie/$1\"><param name=\"AllowFullscreen\" value=\"true\"></object>",

            "<object width=\"425\" height=\"344\" wmode=\"opaque\"><param name=\"movie\" value=\"http://www.youtube.com/v/$1&hl=de_DE&fs=1&color1=0x3a3a3a&color2=0x999999&border=0\">"
        . "</param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><param name=\"wmode\" value=\"opaque\">"
        . "<embed src=\"http://www.youtube.com/v/$1&hl=de_DE&fs=1&color1=0x3a3a3a&color2=0x999999&border=0\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" "
        . "allowfullscreen=\"true\" width=\"425\" height=\"344\"></embed></object>",

            "<object width=\"425\" height=\"344\" wmode=\"opaque\" codebase=\"http://go.divx.com/plugin/DivXBrowserPlugin.cab\">"
        . "<param name=\"custommode\" value=\"none\" /><param name=\"autoPlay\" value=\"false\" /><param name=\"src\" value=\"$1\" />"
        . "<embed type=\"video/divx\" src=\"$1\" custommode=\"none\" width=\"425\" height=\"344\" autoPlay=\"false\" pluginspage=\"http://go.divx.com/plugin/download/\"></embed></object>",

            "<object width=\"425\" height=\"344\" wmode=\"opaque\"><param name=\"allowfullscreen\" value=\"true\" />"
        . "</param><param name=\"wmode\" value=\"opaque\"><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"movie\" value=\"http://www.vimeo.com/moogaloop.swf?clip_id=$1&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1\" /><embed src=\"http://www.vimeo.com/moogaloop.swf?clip_id=\\1&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" allowscriptaccess=\"always\" width=\"425\" height=\"344\"></embed></object>",

            "<object width=\"480\" height=\"270\" wmode=\"opaque\"></param><param name=\"wmode\" value=\"opaque\">"
        . "<param name=\"movie\" value=\"http://video.golem.de/player/videoplayer.swf?id=$1&autoPl=false\"></param><param name=\"allowFullScreen\" value=\"true\">"
        . "</param><param name=\"AllowScriptAccess\" value=\"always\"><embed src=\"http://video.golem.de/player/videoplayer.swf?id=$1&autoPl=false\" "
        . "type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" AllowScriptAccess=\"always\" width=\"480\" height=\"270\"></embed></object>");
    
    private static function glossar_load_index() {
        global $sql;
        foreach($sql->select("SELECT `word`,`glossar` FROM `{prefix_glossar}`;") as $getglossar) {
            self::$gl_words[] = stringParser::decode($getglossar['word']);
            self::$gl_desc[]  = stringParser::decode($getglossar['glossar']);
        }

        dbc_index::setIndex('glossar', array('gl_words' => self::$gl_words, 'gl_desc' => self::$gl_desc));
    }

    private static function process_list_items($list_items) {
        $result_list_items = array();

        // Check for [li][/li] tags
        preg_match_all("/\[li\](.*?)\[\/li\]/is", $list_items, $li_array);
        $li_array = $li_array[1];
        if (empty($li_array)) {
            // we didn't find any [li] tags
            $list_items_array = explode("[*]", $list_items);
            foreach ($list_items_array as $li_text) {
                $li_text = trim($li_text);
                if (empty($li_text)) {
                    continue;
                }

                $li_text = nl2br($li_text);
                $result_list_items[] = '<li>'.$li_text.'</li>';
            }
        } else {
            // we found [li] tags!
            foreach ($li_array as $li_text) {
                $li_text = nl2br($li_text);
                $result_list_items[] = '<li>'.$li_text.'</li>';
            }
        }

        return implode("\n", $result_list_items);
    }

    //Badword Filter
    private static function badword_filter() {
        $words = trim(stringParser::decode(settings::get('badwords')));
        if(empty($words)) return;
        $words = explode(",",$words);
        if(count($words) >= 1) {
            foreach($words as $word) { 
                self::$string = preg_replace_callback("#".$word."#i",create_function('$matches','return str_repeat("*", strlen($matches[0]));'),self::$string);
            }
        }
    }

    private static function make_url_clickable($matches) {
        $ret = '';
        $url = $matches[2];

        if ( empty($url) )
            return $matches[0];
        // removed trailing [.,;:] from URL
        if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true )
        {
            $ret = substr($url, -1);
            $url = substr($url, 0, strlen($url)-1);
        }

        return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
    }

    private static function make_web_ftp_clickable($matches) {
        $ret = '';
        $dest = $matches[2];
        $dest = 'http://' . $dest;

        if ( empty($dest) )
            return $matches[0];
        // removed trailing [,;:] from URL
        if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true )
        {
            $ret = substr($dest, -1);
            $dest = substr($dest, 0, strlen($dest)-1);
        }

        return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
    }

    private static function make_email_clickable($matches) {
        $email = $matches[2] . '@' . $matches[3];
        return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
    }

    private static function make_clickable() {
        self::$string = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'self::make_url_clickable', self::$string);
        self::$string = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'self::make_web_ftp_clickable', self::$string);
        self::$string = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'self::make_email_clickable', self::$string);
        self::$string = trim(preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", self::$string));
    }

    //Smileys
    private static function make_smileys() {
        if(!dbc_index::issetIndex('smileys')) {
            $smileys = get_files(basePath.'/inc/images/smileys',false,true);
            dbc_index::setIndex('smileys', $smileys);
        } else $smileys = dbc_index::getIndex('smileys');
        
        foreach($smileys as $smiley) {
            $bbc = preg_replace("=.gif=Uis","",$smiley);
            if(preg_match("=:".$bbc.":=Uis",self::$string)!=FALSE)
                self::$string = preg_replace("=:".$bbc.":=Uis","<img src=\"../inc/images/smileys/".$bbc.".gif\" alt=\"\" />", self::$string);
        }

        $var = array("/\ :D/", "/\ :P/","/\ ;\)/", "/\ :\)/", "/\ :-\)/", "/\ :\(/", "/\ :-\(/","/\ ;-\)/","/\ ^^/");
        $repl = array(' <img src="../inc/images/smileys/grin.gif" alt=":D" />',
                      ' <img src="../inc/images/smileys/zunge.gif" alt=":P" />',
                      ' <img src="../inc/images/smileys/zwinker.gif" alt="" />',
                      ' <img src="../inc/images/smileys/smile.gif" alt="" />',
                      ' <img src="../inc/images/smileys/smile.gif" alt="" />',
                      ' <img src="../inc/images/smileys/traurig.gif" alt="" />',
                      ' <img src="../inc/images/smileys/traurig.gif" alt="" />',
                      ' <img src="../inc/images/smileys/zwinker.gif" alt="" />',
                      ' <img src="../inc/images/smileys/^^.gif" alt="^^" />');
        self::$string = preg_replace($var,$repl, self::$string);
    }

    private static function make_glossar() {
        global $ajaxJob;
        if (!self::$use_glossar || $ajaxJob) {
            return;
        }
        
        if (!dbc_index::issetIndex('glossar')) {
            self::glossar_load_index();
        }
        
        self::$gl_words = dbc_index::getIndexKey('glossar', 'gl_words');
        self::$gl_desc = dbc_index::getIndexKey('glossar', 'gl_desc'); 
        $txt = str_replace(array('&#93;','&#91;'),array(']','['),self::$string);

        // mark words
        if(count(self::$gl_words) >= 1) {
            foreach(self::$gl_words as $gl_word) {
                $w = addslashes(regexChars(html_entity_decode($gl_word)));
                $search  = array(' '.$w.' ', '>'.$w.'<', '>'.$w.' ',' '.$w.'<');
                $replace = array(' <tmp|'.$w.'|tmp> ','> <tmp|'.$w.'|tmp> <','> <tmp|'.$w.'|tmp> ',' <tmp|'.$w.'|tmp> <');
                $txt = str_ireplace($search, $replace, $txt);
            }

            // replace words
            for($g=0;$g<=count(self::$gl_words)-1;$g++) {
                $desc = regexChars(self::$gl_desc[$g]);
                $info = 'onmouseover="DZCP.showInfo(\''.jsconvert($desc).'\')" onmouseout="DZCP.hideInfo()"';
                $w = regexChars(html_entity_decode(self::$gl_words[$g]));
                $r = "<a class=\"glossar\" href=\"../glossar/?word=".self::$gl_words[$g]."\" ".$info.">".self::$gl_words[$g]."</a>";
                $txt = str_ireplace('<tmp|'.$w.'|tmp>', $r, $txt);
            }

            unset($w,$r,$info,$desc,$gl_word);
        }

        self::$string = str_replace(array(']','['),array('&#93;','&#91;'),$txt);
    }

    private static function bbcodetolow($founds)
    { return "[".strtolower($founds[1])."]".trim($founds[2])."[/".strtolower($founds[3])."]"; }

    public static function search_vid() {
        self::$vid_count = 0;
        foreach (self::$vid_search as $search) {
            self::$string = preg_replace_callback($search,"self::callback_vid",self::$string);
            self::$vid_count++;
        }
    }

    private static function callback_vid($matches) {
        $htmlCode = self::$vid_replace[self::$vid_count];
        return str_replace('$1', $matches[1], $htmlCode);
    }

    /**
     * Führt den allgemeinen BBCode aus.
     *
     * @param string $string
     * @param boolean $htmlentities
     * @param boolean $nolinks
     * @return string
     */
    public static function parse_html($string='',$htmlentities=false, $nolinks=false) {
        self::$string = stringParser::decode($string);
        if(empty(self::$string)) return self::$string;
        self::$string = $htmlentities ? htmlentities(self::$string) : self::$string;
        self::$string = spChars(self::$string);

        self::$string = preg_replace_callback("/\[(.*?)\](.*?)\[\/(.*?)\]/","self::bbcodetolow",self::$string);
        self::$string = preg_replace_callback("#\<img(.*?)\>#", 
                create_function('$img','if(preg_match("#class#i",$img[1])) return '
                        . '"<img".$img[1].">"; else return "<img class=\"content\"".$img[1].">";'), 
                self::$string);
        
        //Hide Tag
        self::$string = (checkme() >= 1 ? str_replace(array('[hide]','[/hide]'),'',self::$string) : preg_replace("/\[hide\](.*?)\[\/hide\]/", "",self::$string));

        // Badword Filter
        self::badword_filter();

        // Preappend http:// to url address if not present
        if(settings::get('urls_linked') && !$nolinks) {
            self::make_clickable();
        }

        self::$string = preg_replace_callback('/\[url\=([^(http)].+?)\](.*?)\[\/url\]/i',create_function('$matches','return \'[url=http://\'.$matches[1].\']\'.$matches[2].\'[/url]\';'),self::$string);
        self::$string = preg_replace_callback('/\[url\]([^(http)].+?)\[\/url\]/i',create_function('$matches','return \'[url=http://\'.$matches[1].\']\'.$matches[1].\'[/url]\';'),self::$string);

        // Remove \n line breaks
        self::$string = str_replace( "\n", "", self::$string ); 

        // Remove the trash made by previous
        self::$string = preg_replace(self::$lineBreaks_search, self::$lineBreaks_replace, self::$string);

        // Parse bbcode
        self::$string = preg_replace(self::$simple_search, self::$simple_replace, self::$string);
        
        // Parse CodeTag
        self::$string = preg_replace_callback('/\[code\](.*?)\[\/code\]/i', create_function('$matches','return \'<pre><code>\'.$matches[1].\'</code></pre>\';'), self::$string);

        // Parse Movie Players
        self::search_vid();

        // Parse Smileys
        self::make_smileys();

        // Parse Glossar
        if(self::$use_glossar) {
            self::make_glossar();
        }

        // Parse [list] tags
        self::$string = preg_replace('/\[list\](.*?)\[\/list\]/si', '"<ul>\n".self::process_list_items("$1")."\n</ul>"', self::$string);
        
        return preg_replace('/\[list\=(disc|circle|square|decimal|decimal-leading-zero|lower-roman|upper-roman|lower-greek|lower-alpha|'
                . 'lower-latin|upper-alpha|upper-latin|hebrew|armenian|georgian|cjk-ideographic|hiragana|katakana|hiragana-iroha|katakana-iroha|none)\](.*?)\[\/list\]/si',
                '"<ol style=\"list-style-type: $1;\">\n".self::process_list_items("$2")."\n</ol>"', self::$string);
    }

    public static function search_lineBreaks() {
        self::$lineBreaks_count = 0;
        foreach (self::$lineBreaks_search as $search) {
            self::$string = preg_replace_callback($search,"self::callback_lineBreaks",self::$string);
            self::$lineBreaks_count++;
        }
    }

    private static function callback_lineBreaks($matches) {
        $htmlCode = self::$lineBreaks_replace[self::$lineBreaks_count];
        return str_replace('$1', $matches[1], $htmlCode);
    }

    /**
     * Führt den BBCode des TS3 Servers aus.
     *
     * @param string $string
     * @return string
     */
    public static function parse_ts3($string='') {
        if(empty(self::$string)) return self::$string;

        // Badword Filter
        self::badword_filter();

        self::$string = preg_replace('/\[url\=([^(http)].+?)\](.*?)\[\/url\]/i', '[url=http://$1]$2[/url]', self::$string);
        self::$string = preg_replace('/\[url\]([^(http)].+?)\[\/url\]/i', '[url=http://$1]$1[/url]', self::$string);

        // Remove the trash made by previous
        self::$string = preg_replace(self::$lineBreaks_search, self::$lineBreaks_replace, self::$string);

        // Parse bbcode
        self::$string = preg_replace(self::$simple_search, self::$simple_replace, self::$string);

        // Parse [list] tags
        self::$string = preg_replace('/\[list\](.*?)\[\/list\]/sie', '"<ul>\n".self::process_list_items("$1")."\n</ul>"', self::$string);
        return preg_replace('/\[list\=(disc|circle|square|decimal|decimal-leading-zero|lower-roman|upper-roman|lower-greek|lower-alpha|lower-latin|upper-alpha|upper-latin|hebrew|armenian|georgian|cjk-ideographic|hiragana|katakana|hiragana-iroha|katakana-iroha|none)\](.*?)\[\/list\]/sie',
                '"<ol style=\"list-style-type: $1;\">\n".self::process_list_items("$2")."\n</ol>"', self::$string);
    }

    /**
     * Textteil in ein Zitat setzen * blockquote *
     * @param string $nick,string $zitat,
     * @return string (html-code)
     */
    public static function zitat($nick,$zitat) {
        $search  = array(chr(145),chr(146),"'",chr(147),chr(148),chr(10),chr(13));
        $replace = array(chr(39),chr(39),"&#39;",chr(34),chr(34)," "," ");
        $zitat = preg_replace("#[\n\r]+#", "<br />", str_replace($search, $replace, $zitat));
        return '<br /><br /><br /><blockquote><b>'.$nick.' '._wrote.':</b><br />'.stringParser::decode($zitat).'</blockquote>';
    }

    public static function nletter($txt)
    { return '<style type="text/css">p { margin: 0px; padding: 0px; }</style>'.$txt; }

    public static function use_glossar($var=true)
    { self::$use_glossar = $var; }
    
    /** 
     * Generates version 1: MAC address 
     */  
    public static function uuid() {  
      if (!function_exists('uuid_create'))  
        return false;  

      $context = $uuid = null;  
      uuid_create($context);  
      uuid_make($context, UUID_MAKE_V1);  
      uuid_export($context, UUID_FMT_STR, $uuid);  
      return trim($uuid);  
    }
}