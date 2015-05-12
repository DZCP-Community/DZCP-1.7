<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/common.php");

## SETTINGS ##
$where = _site_news;
$dir = "news";
define('_News', true);

## SECTIONS ##
//RSS News Feed erzeugen
function feed() {
    global $sql,$pagetitle,$charset;
    if(!file_exists(basePath.'/rss.xml') || time() - filemtime(basePath.'/rss.xml') > feed_update_time) {
        $host = GetServerVars('HTTP_HOST');
        $pfad = preg_replace("#^(.*?)\/(.*?)#Uis","$1",dirname(GetServerVars('PHP_SELF')));
        $data = fopen("../rss.xml","w+");
        $feed = '<?xml version="1.0" encoding="'.$charset.'" ?>';
        $feed .= "\r\n";
        $feed .= '<rss version="0.91">';
        $feed .= "\r\n";
        $feed .= '<channel>';
        $feed .= "\r\n";
        $feed .= '  <title>'.convert_feed($pagetitle).'</title>';
        $feed .= "\r\n";
        $feed .= '  <link>http://'.$host.'</link>';
        $feed .= "\r\n";
        $feed .= '  <description>Clannews von '.convert_feed(settings('clanname')).'</description>';
        $feed .= "\r\n";
        $feed .= '  <language>de-de</language>';
        $feed .= "\r\n";
        $feed .= '  <copyright>'.date("Y", time()).' '.convert_feed(settings('clanname')).'</copyright>';
        $feed .= "\r\n";
        fwrite($data, $feed);
        
        $qry = $sql->select("SELECT `id`,`autor`,`datum`,`titel`,`text` FROM `{prefix_news}` WHERE `intern` = 0 AND `public` = 1 ORDER BY `datum` DESC LIMIT 15;");
        if($sql->rowCount()) {
            foreach($qry as $get) {
                $feed .= '  <item>';
                $feed .= "\r\n";
                $feed .= '    <pubDate>'.date("r", $get['datum']).'</pubDate>';
                $feed .= "\r\n";
                $feed .= '    <author>'.convert_feed(data('nick', $get['autor'])).'</author>';
                $feed .= "\r\n";
                $feed .= '    <title>'.convert_feed($get['titel']).'</title>';
                $feed .= "\r\n";
                $feed .= '    <description>';
                $feed .= convert_feed($get['text']);
                $feed .= '    </description>';
                $feed .= "\r\n";
                $feed .= '    <link>http://'.$host.$pfad.'/news/?action=show&amp;id='.$get['id'].'</link>';
                $feed .= "\r\n";
                $feed .= '  </item>';
                $feed .= "\r\n";
                fwrite($data, $feed);
            }
        }

        $feed .= '</channel>';
        $feed .= "\r\n";
        $feed .= '</rss>';
        fwrite($data, $feed);
        fclose($data);
        
        if(!file_exists(basePath.'/rss.xml') && view_error_reporting) {
            DebugConsole::insert_warning('news:feed', 'Permission denied! Can not write ./rss.xml');
        }
    }
}

if (!view_error_reporting || (feed_enable_on_debug && view_error_reporting)) { //NewsFeed
    feed();
}

$action = empty($action) ? 'default' : $action;
if (file_exists(basePath . "/news/case_" . $action . ".php")) {
    require_once(basePath . "/news/case_" . $action . ".php");
}

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);