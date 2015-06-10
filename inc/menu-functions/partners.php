<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Partners
 */

function partners() {
    global $sql;
    
    $qry = $sql->select("SELECT `textlink`,`link`,`banner` FROM `{prefix_partners}` ORDER BY `textlink` ASC;");
    $partners = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            if($get['textlink']) {
                $partners .= show("menu/partners_textlink", array("link" => re($get['link']),
                                                                  "name" => re($get['banner'])));
            } else {
                $partners .= show("menu/partners", array("link" => re($get['link']),
                                                         "title" => htmlspecialchars(str_replace('http://', '', re($get['link']))),
                                                         "banner" => re($get['banner'])));
            }

            $table = strstr($partners, '<tr>') ? true : false;
        }
    }

    return empty($partners) ? '<center style="margin:2px 0">'._no_entrys.'</center>' : ($table ? '<table class="navContent" cellspacing="0">'.$partners.'</table>' : $partners);
}