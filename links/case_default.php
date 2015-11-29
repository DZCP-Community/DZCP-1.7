<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Links')) exit();

$qry = $sql->select("SELECT * FROM `{prefix_links}` ORDER BY banner DESC;");
if($sql->rowCount()) {
    foreach($qry as $get) {
        if($get['banner']) {
            $banner = show(_links_bannerlink, array("id" => $get['id'],
                                                    "banner" => re($get['text'])));
        } else {
            $banner = show(_links_textlink, array("id" => $get['id'],
                                                  "text" => str_replace('http://','',re($get['url']))));
        }

        $show .= show($dir."/links_show", array("beschreibung" => bbcode(re($get['beschreibung'])),
                                                "hits" => $get['hits'],
                                                "banner" => $banner));
    }
}

if(empty($show))
    $show = _no_entrys_yet;

$index = show($dir."/links", array("show" => $show));