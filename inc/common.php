<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## INCLUDES ##
require_once(basePath."/inc/debugger.php");
require_once(basePath."/inc/config.php");
require_once(basePath."/inc/database.php");

if (function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get") && use_default_timezone) {
    date_default_timezone_set(date_default_timezone_get());
} else if (!use_default_timezone) {
    date_default_timezone_set(default_timezone);
} else {
    date_default_timezone_set("Europe/Berlin");
}

if (!isset($thumbgen)) {
    $thumbgen = false;
}

if(!$thumbgen) {
    if(view_error_reporting) {
        error_reporting(E_ALL);

        if (function_exists('ini_set')) {
            ini_set('display_errors', 1);
        }

        DebugConsole::initCon();

        if (debug_dzcp_handler) {
            set_error_handler('dzcp_error_handler');
        }
    } else {
        if (function_exists('ini_set')) {
            ini_set('display_errors', 0);
        }

        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

        if (debug_dzcp_handler) {
            set_error_handler('dzcp_error_handler');
        }
    }
}

## REQUIRES ##
//DZCP-Install default variable
$database = new database(); //Load DB Class
if(!isset($installer)) { $installer = false; }

//Load SQL Config
if(file_exists(basePath."/inc/mysql.php")) {
    require_once(basePath."/inc/mysql.php");
}

$sql = $database->getInstance(); //Connect to DB * default *
if(!isset($installation)) { $installation = false; }
if(!isset($updater)) { $updater = false; }
if(!isset($global_index)) { $global_index = false; }

function show($tpl="", $array=array(), $array_lang_constant=array(), $array_block=array()) {
    global $tmpdir;
    return show_runner($tpl,"inc/_templates_/".$tmpdir."/",$array,$array_lang_constant,$array_block,false);
}

//-> Ersetzt Platzhalter im HTML Code 
function show_runner($tpl="", $dir="", $array=array(), $array_lang_constant=array(), $array_block=array(),$addon=false) {
    global $tmpdir,$chkMe,$cache,$config_cache,$installation;
    if(!empty($tpl) && $tpl != null) {
        $template = basePath."/".$dir.$tpl;

        //HTML Cache for Template Files
        if(!$installation) {
            $cacheHash = md5($template);
            if(template_cache && $config_cache['use_cache'] && dbc_index::useMem() && $cache->isExisting('tpl_'.$cacheHash)) {
                $tpl = re($cache->get('tpl_'.$cacheHash));
                if(show_dbc_debug) {
                    DebugConsole::insert_info('template::show()', 'Get Template-Cache: "' . 'tpl_' . $cacheHash . '"');
                }
            }
            else {
                if(file_exists($template.".html") && is_file($template.".html")) {
                    $tpl = file_get_contents($template.".html");
                    if (substr($tpl, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
                        $tpl = substr($tpl, 3);
                    }
                    if(template_cache && $config_cache['use_cache'] && dbc_index::useMem()) {
                        $cache->set('tpl_'.$cacheHash,up($tpl),template_cache_time);
                        if (show_dbc_debug) {
                            DebugConsole::insert_loaded('template::show()', 'Set Template-Cache: "' . 'tpl_' . $cacheHash . '"');
                        }
                    }
                }
            }
        }
        else {
            if(file_exists($template . ".html") && is_file($template.".html")) {
                $tpl = file_get_contents($template . ".html");
                if (substr($tpl, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
                    $tpl = substr($tpl, 3);
                }
            }
        }

        //put placeholders in array
        $array['dir'] = '../inc/_templates_/'.$tmpdir;
        $array['idir'] = '../inc/images'; //Image DIR [idir]
        $array['adir'] = ($addon ? '../'.$addon : '../inc/_templates_/'.$tmpdir); //Addon DIR [adir]
        $pholder = explode("^",pholderreplace($tpl));
        for($i=0;$i<=count($pholder)-1;$i++) {
            if (in_array($pholder[$i], $array_block) || array_key_exists($pholder[$i], $array) || 
               (!strstr($pholder[$i], 'lang_') && !strstr($pholder[$i], 'func_'))) {
                continue;
            }

            if (defined(substr($pholder[$i], 4))) {
                $array[$pholder[$i]] = (count($array_lang_constant) >= 1 ? show(constant(substr($pholder[$i], 4)), $array_lang_constant) : constant(substr($pholder[$i], 4)));
                continue;
            }

            if (function_exists(substr($pholder[$i], 5))) {
                $function = substr($pholder[$i], 5);
                $array[$pholder[$i]] = $function();
            }
        }
        
        unset($pholder);
        
        $tpl = (!$chkMe ? preg_replace("|<logged_in>.*?</logged_in>|is", "", $tpl) : preg_replace("|<logged_out>.*?</logged_out>|is", "", $tpl));
        $tpl = str_ireplace(array("<logged_in>","</logged_in>","<logged_out>","</logged_out>"), '', $tpl);

        if(count($array) >= 1) {
            foreach($array as $value => $code)
            { $tpl = str_replace('['.$value.']', $code, $tpl); }
        }
    }

    return $tpl;
}

if(!$installation) {
    require_once(basePath."/inc/bbcode.php");
}