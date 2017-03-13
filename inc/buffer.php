<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

ob_start();
ob_implicit_flush(false);
define('basePath', dirname(dirname(__FILE__).'../'));

function can_gzip() {
    if(!buffer_gzip_compress) return false;
    if(headers_sent() || connection_aborted()) return false; 
    if(!function_exists('gzcompress')) return false;
    if(strpos(GetServerVars('HTTP_ACCEPT_ENCODING'), 'x-gzip') !== false) return true;
    if(strpos(GetServerVars('HTTP_ACCEPT_ENCODING'), 'sdch')   !== false) return true;
    if(strpos(GetServerVars('HTTP_ACCEPT_ENCODING'), 'gzip')   !== false) return true;
    return false;
}

function gz_output($output='') {
    global $time_start;
    $time = round(getmicrotime() - $time_start,4);
    if(buffer_show_licence_bar) {
        switch (_edition) {
            case 'dev': $dev_info = ' - Development Edition [ Runtime: '.$time.' ]'; break;
            case 'society': $dev_info = ' - Society Edition'; break;
            default: $dev_info = ''; break;
        }

        $licence_bar = '<div class="licencebar"> <table style="width:100%;margin:auto" cellspacing="0"> <tr> <td class="licencebar" nowrap="nowrap">Powered by <a class="licencebar" href="http://www.dzcp.de" target="_blank" title="deV!L`z Clanportal">DZCP - deV!L`z&nbsp;Clanportal V'._version.'</a>'.$dev_info.'</td></tr> </table> </div>';
        if(!file_exists(basePath.'/_codeking.licence')) {
            $output = str_ireplace('</body>',$licence_bar."\r\n</body>",$output);
        }
    }

    if($encoding=can_gzip()) {
		if(function_exists('ini_set')) {
			ini_set('zlib.output_compression','Off');
		}
		$gzip_compress_level = (!defined('buffer_gzip_compress_level') ? 4 : buffer_gzip_compress_level);
        $output .= "\r\n"."<!-- [GZIP => Level ".$gzip_compress_level."] ".
		sprintf("%01.2f",((strlen(gzencode(trim(preg_replace( '/\s+/', ' ', $output ) ), $gzip_compress_level)))/1024))." kBytes | uncompressed: ".
		sprintf("%01.2f",((strlen($output))/1024 ))." kBytes -->";
        $output = preg_replace('#\<!--.*?\-->#', '', $output); //Remove <!-- --> Tags
		$output .= "\r\n<!--This CMS is powered by deV!L`z Clanportal "._version." - www.dzcp.de-->";
		$hmtl = gzencode(trim(preg_replace( '/\s+/', ' ', $output ) ), $gzip_compress_level);
        unset($output);
        header('Content-Encoding: gzip');
        header('content-type: text/html; charset: UTF-8');
        header('cache-control: must-revalidate');
        header('expires: '.gmdate("D, d M Y H:i:s", time() + 60 * 60) . " GMT" );
        header('Content-Length: '.strlen($hmtl));
        header('Vary: Accept-Encoding');
        exit($hmtl);
    } else {
		$output .= "\r\n<!--This CMS is powered by deV!L`z Clanportal "._version." - www.dzcp.de-->";
        echo($output);
        ob_end_flush();
    }
}