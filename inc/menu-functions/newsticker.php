<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Newsticker
 */

function newsticker() {
    global $sql;
    
    $qry = $sql->select("SELECT `id`,`titel`,`autor`,`datum`,`kat` "
            . "FROM `{prefix_news}` "
            . "WHERE `public` = 1 AND `datum` <= ? ".(permission("intnews") ? "" : "AND `intern` = 0")." "
            . "ORDER BY `id` DESC LIMIT 20;",array(time()));
    $news = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            if(settings('allowhover') == 1) {
                $getkat = $sql->selectSingle("SELECT `kategorie` FROM `{prefix_newskat}` WHERE `id` = ?;",array($get['kat']));
                $info = 'onmouseover="DZCP.showInfo(\''.jsconvert(re($get['titel'])).'\', \''._datum.';'._autor.';'._news_admin_kat.';'._comments_head.'\', \''.
                        date("d.m.Y H:i", $get['datum'])._uhr.';'.fabo_autor($get['autor']).';'.jsconvert(re($getkat['kategorie'])).';'.
                        cnt("{prefix_newscomments}","WHERE `news` = ?","id",array($get['id'])).'\')" onmouseout="DZCP.hideInfo()"';
            }

            $news .= '<a href="../news/?action=show&amp;id='.$get['id'].'" '.$info.'>'.re($get['titel']).'</a> | ';
        }
    }

    return show("menu/newsticker", array("news" => $news));
}