<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Artikel')) {
    $qry = $sql->select("SELECT `id`,`kat`,`titel`,`datum`,`autor`,`text` "
            . "FROM `{prefix_artikel}` "
            . "WHERE `public` = 1 ".orderby_sql(array("artikel","titel","datum","kat"), 'ORDER BY `datum` DESC')." "
            . "LIMIT ".($page - 1)*config('m_artikel').",".config('m_artikel').";");

    if($sql->rowCount()) {
        foreach($qry as $get) {
            $getk = $sql->selectSingle("SELECT `kategorie` FROM `{prefix_newskat}` WHERE `id` = ?;",array($get['kat']));
            $titel = '<a style="display:block" href="?action=show&amp;id='.$get['id'].'">'.re($get['titel']).'</a>';
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/artikel_show", array("titel" => $titel,
                                                      "kat" => re($getk['kategorie']),
                                                      "id" => $get['id'],
                                                      "display" => "none",
                                                      "class" => $class,
                                                      "text" => bbcode(re($get['text'])),
                                                      "datum" => date("d.m.Y", $get['datum']),
                                                      "autor" => autor($get['autor'])));
        }
    } else {
        $show = show(_no_entrys_yet, array("colspan" => "4"));
    }

    $seiten = nav(cnt("{prefix_artikel}"),config('m_artikel'),"?page".(isset($_GET['show']) ? $_GET['show'] : 0).orderby_nav());
    $index = show($dir."/artikel", array("show" => $show,
                                         "nav" => $seiten,
                                         "order_autor" => orderby('autor'),
                                         "order_datum" => orderby('datum'),
                                         "order_titel" => orderby('titel'),
                                         "order_kat" => orderby('kat')));
}