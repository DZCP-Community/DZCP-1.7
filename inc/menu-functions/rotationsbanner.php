<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Rotationsbanner
 */

function rotationsbanner() {
    global $sql;
    
    $qry = $sql->select("SELECT `id`,`link`,`bend`,`blink` FROM `{prefix_sponsoren}` WHERE `banner` = 1 ORDER BY RAND() LIMIT 1;");
    $rotationbanner = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $rotationbanner .= show(_sponsors_bannerlink, array("id" => $get['id'],
                                                                "title" => htmlspecialchars(str_replace('http://', '', re($get['link']))),
                                                                "banner" => (empty($get['blink']) ? "../banner/sponsors/banner_".$get['id'].".".$get['bend'] : re($get['blink']))));
        }
    }

    return empty($rotationbanner) ? '' : $rotationbanner;
}