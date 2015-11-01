<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

ob_start();
ob_implicit_flush(false);
define('basePath', dirname(dirname(__FILE__).'../'));

function can_gzip() {
    if(headers_sent() || connection_aborted()) return false; 
    if(!function_exists('gzcompress')) return false;
    if(strpos(GetServerVars('HTTP_ACCEPT_ENCODING'), 'x-gzip') !== false) return "x-gzip";
    if(strpos(GetServerVars('HTTP_ACCEPT_ENCODING'), 'sdch')   !== false) return "gzip"; 
    if(strpos(GetServerVars('HTTP_ACCEPT_ENCODING'), 'gzip')   !== false) return "gzip"; 
    return false;
}

function gz_output($output='') {
    global $time_start;
    $gzip_compress_level = (!defined('buffer_gzip_compress_level') ? 4 : buffer_gzip_compress_level);
    if(function_exists('ini_set')) {
        ini_set('zlib.output_compression_level', $gzip_compress_level);
    }

    $time = round(getmicrotime() - $time_start,4);
    if(buffer_show_licence_bar) {
        switch (_edition) {
            case 'dev': $dev_info = ' - Development Edition [ Runtime: '.$time.' ]'; break;
            case 'society': $dev_info = ' - Society Edition'; break;
            case 'phar': $dev_info = ' - PHAR Kernel [ Runtime: '.$time.' - Version: '._phar_kernel.' ]'; break;
            default: $dev_info = ''; break;
        }

        $licence_bar = '<div class="licencebar"> <table style="width:100%;margin:auto" cellspacing="0"> <tr> <td class="licencebar" nowrap="nowrap">Powered by <a class="licencebar" href="http://www.dzcp.de" target="_blank" title="deV!L`z Clanportal">DZCP - deV!L`z&nbsp;Clanportal V'._version.'</a>'.$dev_info.'</td></tr> </table> </div>';
        if(!file_exists(basePath.'/_codeking.licence')) {
            $output = str_ireplace('</body>',$licence_bar."\r\n</body>",$output);
        }
    }

    $output .= "\r\n<!--This CMS is powered by deV!L`z Clanportal V"._version." - www.dzcp.de-->";
    
    if($encoding=can_gzip()) {
        $output .= "\r\n"."<!-- [GZIP => Level ".$gzip_compress_level."] ".sprintf("%01.2f",((strlen(gzcompress($output,$gzip_compress_level)))/1024))." kBytes | uncompressed: ".sprintf("%01.2f",((strlen($output))/1024 ))." kBytes -->";
        ob_end_clean();
        ob_start('ob_gzhandler');
        header("Content-Encoding: ".$encoding);
        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $hmtl = gzcompress($output,$gzip_compress_level);
        echo substr($hmtl, 0, strlen($hmtl) - 4);
        echo pack('V',crc32($output)); 
        echo pack('V',strlen($output)); 
        exit();
        ob_end_flush();
    } else {
        ob_end_flush();
        exit($output); 
    }
}