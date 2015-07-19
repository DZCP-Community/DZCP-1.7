<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_LinkUS')) exit();

$qry = $sql->select("SELECT * FROM `{prefix_linkus}` ORDER BY `banner` DESC;");
if($sql->rowCount()) {
    foreach($qry as $get) {
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $banner = show(_linkus_bannerlink, array("id" => $get['id'], "banner" => re($get['text'])));
        $edit = ""; $delete = "";
        if(permission("links")) {
            $edit = show("page/button_edit", array("id" => $get['id'],
                                                   "action" => "action=admin&amp;do=edit",
                                                   "title" => _button_title_edit));

            $delete = show("page/button_delete", array("id" => $get['id'],
                                                       "action" => "action=admin&amp;do=delete",
                                                       "title" => _button_title_del));
        }

        $show .= show($dir."/linkus_show", array("class" => $class,
                                                 "beschreibung" => re($get['beschreibung']),
                                                 "cnt" => $color,
                                                 "banner" => $banner,
                                                 "besch" => re($get['beschreibung']),
                                                 "url" => re($get['url'])));
    }
}

if(empty($show))
    $show = _no_entrys_yet;

$index = show($dir."/linkus", array("show" => $show));