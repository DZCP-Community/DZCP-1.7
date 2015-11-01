<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Navigation
 */

function navi($kat) {
    global $sql,$chkMe,$userid,$designpath;
    
    $navi="";
    if($k = $sql->fetch("SELECT `level` FROM `{prefix_navi_kats}` WHERE `placeholder` = ?;",array(up($kat)))) {
        $permissions = ($kat == 'nav_admin' && admin_perms($userid)) ? "" : ($chkMe >= 2 ? '' : " AND s1.`internal` = 0")." AND ".intval($chkMe)." >= ".intval($k['level']);
        $qry = $sql->select("SELECT s1.* FROM `{prefix_navi}` AS `s1` "
                . "LEFT JOIN `{prefix_navi_kats}` AS `s2` ON s1.`kat` = s2.`placeholder` "
                . "WHERE s1.`kat` = ? AND s1.`shown` = 1 ".$permissions." "
                . "ORDER BY s1.`pos`;",array(up($kat)));

        if($sql->rowCount()) {
            foreach($qry as $get) {
                $link = '';
                if($get['type'] == 1 || $get['type'] == 2 || $get['type'] == 3) {
                    $name = ($get['wichtig']) ? '<span class="fontWichtig">'.navi_name(re($get['name'])).'</span>' : navi_name(re($get['name']));
                    $target = ($get['target']) ? '_blank' : '_self';

                    if(file_exists($designpath.'/menu/'.$get['kat'].'.html')) {
                        $link = show("menu/".$get['kat']."", array("target" => $target,
                                                                   "href" => preg_replace('"( |^)(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)"i', 'http://\2', re($get['url'])),
                                                                   "title" => strip_tags($name),
                                                                   "css" => ucfirst(str_replace('nav_', '', re($get['kat']))),
                                                                   "link" => $name));
                    } else {
                        $link = show("menu/nav_link", array("target" => $target,
                                                            "href" => preg_replace('"( |^)(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)"i', 'http://\2', re($get['url'])),
                                                            "title" => strip_tags($name),
                                                            "css" => ucfirst(str_replace('nav_', '', re($get['kat']))),
                                                            "link" => $name));
                    }

                    $table = strstr($link, '<tr>') ? true : false;
                }

                $navi .= $link;
            }
        }
    }

    return empty($navi) ? '' : ($table ? '<table class="navContent" cellspacing="0">'.$navi.'</table>' : $navi);
}