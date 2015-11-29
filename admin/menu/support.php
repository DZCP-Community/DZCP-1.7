<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
switch (_edition) {
    case 'dev': $edition = 'Development'; break;
    case 'society': $edition = 'Society'; break;
    default: $edition = 'Stable'; break;
}

$PhpInfo = parsePHPInfo();
$support  = "#####################\r\n";
$support .= "Support Informationen\r\n";
$support .= "#####################\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "DZCP Allgemein \r\n";
$support .= "#####################\r\n";
$support .= "DZCP Version: "._version."\r\n";
$support .= "DZCP Release: "._release."\r\n";
$support .= "DZCP Build: "._build."\r\n";
$support .= "DZCP Datenbank: ".settings::get('db_version')."\r\n";
$support .= "DZCP Edition: ".$edition."\r\n";
$support .= "DZCP Template: ".$tmpdir."\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "DZCP Einstellungen \r\n";
$support .= "#####################\r\n";
$support .= "Session Backend: ".sessions_backend."\r\n";
$support .= "Session Encode: ".(sessions_encode ? 'On' : 'Off')."\r\n";
if(phpFastCache::$version >= '3.1.0') {
    $support .= "Cache Storage: ".phpFastCache::$storage."\r\n";
    $support .= "Cache Fallback Storage: ".phpFastCache::$storage_fallback."\r\n";
    $support .= "Cache Fallback Enabled: ".(phpFastCache::$use_fallback ? 'On' : 'Off')."\r\n";
} else {
    $support .= "Cache: ".($config_cache['use_cache'] ? 'On' : 'Off')."\r\n";
    $support .= "Cache Storage: ".$config_cache['storage']."\r\n";
}
$support .= "Cookie Domain: ".cookie_domain."\r\n";
$support .= "Cookie Dir: ".cookie_dir."\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Domain & User\r\n";
$support .= "#####################\r\n";
$support .= "Domain: http://".GetServerVars('HTTP_HOST').str_replace('/admin','/',dirname(GetServerVars('PHP_SELF')))."\r\n";
$support .= "System/Browser: ".GetServerVars('HTTP_USER_AGENT')."\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Server Versionen\r\n";
$support .= "#####################\r\n";
$support .= "GameQ: ".GameQ::VERSION."\r\n";
$support .= "PhpFastCache: ".phpFastCache::$version."\r\n";
$support .= "Server OS: ".@php_uname()."\r\n";
$support .= "Webserver: ".(array_key_exists('apache2handler', $PhpInfo) ? (array_key_exists('Apache Version', $PhpInfo['apache2handler']) ? $PhpInfo['apache2handler']['Apache Version'] : 'PHP l&auml;uft als CGI <Keine Info>' ) : 'PHP l&auml;uft als CGI <Keine Info>')."\r\n";
$support .= "PHP-Version: ".phpversion()." (".php_sapi_type().")"."\r\n";
$support .= "PHP-Alternative PHP Cache (APC) : ".(extension_loaded('apc') ? 'On' : 'Off')."\r\n";
$support .= "PHP-Memcache: ".(extension_loaded('memcache') ? 'On' : 'Off')."\r\n";
$support .= "MySQL-Server Version: ".$sql->fetch("SELECT VERSION() as mysql_version",array(),'mysql_version')."\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Socket-Verbindungen \r\n";
$support .= "#####################\r\n";
$support .= "PHP fsockopen bypass: ".(fsockopen_support_bypass ? 'On' : 'Off')."\r\n";
$support .= "PHP fsockopen: ".(fsockopen_support() ? 'On' : 'Off')."\r\n";
$support .= "PHP allow_url_fopen: ".(allow_url_fopen_support() ? 'On' : 'Off')."\r\n";
$support .= "PHP Sockets: ".(function_exists("socket_create") && $PhpInfo['sockets']['Sockets Support'] == "enabled" ? 'On' : 'Off')."\r\n";
$support .= "\r\n";

$support .= "#####################\r\n";
$support .= "Servereinstellungen\r\n";
$support .= "#####################\r\n";
//Removed in PHP 5.4.x +
if(!is_php('5.4.0')) {
    $support .= "register_globals: ".$PhpInfo['Core']['register_globals'][0]."\r\n";
    $support .= "Save Mode: ".$PhpInfo['Core']['safe_mode'][0]."\r\n";
    if($PhpInfo['Core']['safe_mode'][0] == 'on') {
        $support .= "safe_mode_exec_dir: ".$PhpInfo['Core']['safe_mode_exec_dir'][0]."\r\n";
        $support .= "safe_mode_gid: ".$PhpInfo['Core']['safe_mode_gid'][0]."\r\n";
        $support .= "safe_mode_include_dir: ".$PhpInfo['Core']['safe_mode_include_dir'][0]."\r\n";
    }
}

$support .= "open_basedir: ".$PhpInfo['Core']['open_basedir'][0]."\r\n";
$support .= "GD-Version: ".$PhpInfo['gd']['GD Version']."\r\n";
$support .= "PHP-Memory Limit: ".$PhpInfo['Core']['memory_limit'][0]."\r\n";
$support .= "imagettftext(): ".(function_exists('imagettftext')==true? 'existiert' : 'existiert nicht')."\r\n";
$support .= "HTTP_ACCEPT_ENCODING: ".GetServerVars('HTTP_ACCEPT_ENCODING')."\r\n";
$support .= "PHP OPcache: ".(function_exists('opcache_get_status') ? 'On' : 'Off')."\r\n";

//Removed in PHP 5.4.x +
if(!is_php('5.4.0'))
    $support .= "magic_quotes_gpc: ".$PhpInfo['Core']['magic_quotes_gpc'][0]."\r\n";

$support .= "file_uploads: ".$PhpInfo['Core']['file_uploads'][0]."\r\n";
$support .= "upload_max_filesize: ".$PhpInfo['Core']['upload_max_filesize'][0]."\r\n";
$support .= "sendmail_from: ".$PhpInfo['Core']['sendmail_from'][0]."\r\n";
$support .= "sendmail_path: ".$PhpInfo['Core']['sendmail_path'][0];
$support .= "\r\n";

$show = show($dir."/support", array("info" => _admin_support_info,"head" => _admin_support_head,"support" => $support));