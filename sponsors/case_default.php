<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Sponsors')) exit();

$qry = $sql->select("SELECT `id`,`link`,`slink`,`beschreibung`,`hits` FROM `{prefix_sponsoren}` WHERE `site` = 1 ORDER BY `pos`;");
foreach($qry as $get) {
    if(empty($get['slink'])) {
        foreach($picformat AS $end) {
            if(file_exists(basePath.'/banner/sponsors/site_'.$get['id'].'.'.$end))
                break;
        }

        $banner = show(_sponsors_bannerlink, array("id" => $get['id'],
                                                   "title" => str_replace('http://', '', stringParser::decode($get['link'])),
                                                   "banner" => "../banner/sponsors/site_".$get['id'].".".$end));
    } else {
        $banner = show(_sponsors_bannerlink, array("id" => $get['id'],
                                                   "title" => str_replace('http://', '', stringParser::decode($get['link'])),
                                                   "banner" => $get['slink']));
    }

    $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
    $show .= show($dir."/sponsors_show", array("class" => $class,
                                               "beschreibung" => bbcode::parse_html($get['beschreibung']),
                                               "hits" => $get['hits'],
                                               "banner" => $banner));
}

if(empty($show))
    $show = '<tr><td colspan="2" class="contentMainSecond">'._no_entrys.'</td></tr>';

$index = show($dir."/sponsors", array("show" => $show));