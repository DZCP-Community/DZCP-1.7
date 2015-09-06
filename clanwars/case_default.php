<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Clanwars')) {
    $sum_punkte_get = sum_multi("{prefix_clanwars}", "", array('punkte','gpunkte'));
    $anz_ges_points = show(_cw_stats_ges_points, array("ges_won" => $sum_punkte_get['sum_punkte'],
                                                       "ges_lost" => $sum_punkte_get['sum_gpunkte']));

    $anz_wo_wars = cnt("{prefix_clanwars}", " WHERE `punkte` > `gpunkte`","id");
    $anz_lo_wars = cnt("{prefix_clanwars}", " WHERE `punkte` < `gpunkte`","id");
    $anz_dr_wars = cnt("{prefix_clanwars}", " WHERE `datum` < ? && `punkte` = `gpunkte`","id",array(time()));
    $anz_ge_wars = cnt("{prefix_clanwars}", " WHERE `datum` < ?","id",array(time()));

    $wo_percent = !$anz_ge_wars ? 0 : @round($anz_wo_wars*100/$anz_ge_wars, 1);
    $lo_percent = !$anz_ge_wars ? 0 : @round($anz_lo_wars*100/$anz_ge_wars, 1);
    $dr_percent = !$anz_ge_wars ? 0 : @round($anz_dr_wars*100/$anz_ge_wars, 1);

    $wo_rawpercent = !$anz_ge_wars ? 0 : @round($anz_wo_wars*100/$anz_ge_wars, 0);
    $lo_rawpercent = !$anz_ge_wars ? 0 : @round($anz_lo_wars*100/$anz_ge_wars, 0);
    $dr_rawpercent = !$anz_ge_wars ? 0 : @round($anz_dr_wars*100/$anz_ge_wars, 0);

    $wo_balken = show(_votes_balken, array("width" => !$anz_wo_wars ? 1 : $wo_rawpercent));
    $lo_balken = show(_votes_balken, array("width" => !$anz_lo_wars ? 1 : $lo_rawpercent));
    $dr_balken = show(_votes_balken, array("width" => !$anz_dr_wars ? 1 : $dr_rawpercent));

    $anz_ges_wars = show(_cw_stats_ges_wars, array("ge_wars" => $anz_ge_wars));
    $stats_all = show($dir."/stats", array("wo_wars" => $anz_wo_wars,
                                           "lo_wars" => $anz_lo_wars,
                                           "dr_wars" => $anz_dr_wars,
                                           "dr_percent" => $dr_percent,
                                           "lo_percent" => $lo_percent,
                                           "wo_percent" => $wo_percent,
                                           "won_balken" => $wo_balken,
                                           "lost_balken" => $lo_balken,
                                           "draw_balken" => $dr_balken,
                                           "ges_wars" => $anz_ges_wars,
                                           "ges_points" => $anz_ges_points));

    $qry = $sql->select("SELECT * FROM `{prefix_squads}` WHERE `status` = 1 ORDER BY `pos`;");
    foreach($qry as $get) {
        if(isset($_GET['showsquad']) && intval($_GET['showsquad']) == $get['id'] ||
           isset($_GET['show']) && intval($_GET['show']) == $get['id']) {
            $shown = show(_klapptext_show, array("id" => $get['id']));
                $display = "";
        } else {
                $shown = show(_klapptext_dont_show, array("id" => $get['id']));
                $display = "none";
        }

        $img = show(_gameicon, array("icon" => $get['icon']));
        $qrym = $sql->select("SELECT s1.`id`,s1.`datum`,s1.`clantag`,s1.`gegner`,s1.`url`,s1.`xonx`,s1.`liga`,s1.`punkte`,s1.`gpunkte`,"
                           . "s1.`maps`,s1.`serverip`, s1.`servername`,s1.`serverpwd`,s1.`bericht`,s1.`squad_id`,"
                           . "s1.`gametype`,s1.`gcountry`,s2.`icon`,s2.`name` "
                           . "FROM `{prefix_clanwars}` AS `s1` "
                           . "LEFT JOIN `{prefix_squads}` AS `s2` "
                           . "ON s1.`squad_id` = s2.`id` "
                           . "WHERE s1.`squad_id` = ? AND s1.`datum` < ? "
                           . "ORDER BY s1.`datum` DESC LIMIT ".settings('m_clanwars').";",array($get['id'],time()));

        $wars = "";
        foreach($qrym as $getm) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $game = squad($getm['icon']);
            $flagge = flag($getm['gcountry']);
            $gegner = show(_cw_details_gegner, array("gegner" => re(cut($getm['clantag']." - ".$getm['gegner'], settings('l_clanwars'))),
                                                     "url" => '?action=details&amp;id='.$getm['id']));

            $details = show(_cw_show_details, array("id" => $getm['id']));
            $squad = show(_member_squad_squadlink, array("squad" => re($get['name']),
                                                         "id" => $get['id'],
                                                         "shown" => $shown));

            $wars .= show($dir."/clanwars_show2", array("datum" => date("d.m.Y", $getm['datum']),
                                                        "img" => $img,
                                                        "flagge" => $flagge,
                                                        "gegner" => $gegner,
                                                        "xonx" => re($getm['xonx']),
                                                        "liga" => re($getm['liga']),
                                                        "gametype" => re($getm['gametype']),
                                                        "class" => $class,
                                                        "result" => cw_result_nopic($getm['punkte'], $getm['gpunkte']),
                                                        "details" => $details));
        }

        $sum_punkte_get = sum_multi("{prefix_clanwars}", "WHERE `squad_id` = ?", array('punkte','gpunkte'), array($get['id']));
        $anz_ges_points = show(_cw_stats_ges_points, array("ges_won" => $sum_punkte_get['sum_punkte'],
                                                           "ges_lost" => $sum_punkte_get['sum_gpunkte']));
        $stats = '';
        $cnt_wars = cnt("{prefix_clanwars}", " WHERE `squad_id` = ? AND `datum` < ?", "id", array($get['id'], time()));
        if($cnt_wars) {
            $anz_wo_wars = cnt("{prefix_clanwars}", " WHERE `punkte` > `gpunkte` AND `squad_id` = ?","id",array($get['id']));
            $anz_lo_wars = cnt("{prefix_clanwars}", " WHERE `punkte` < `gpunkte` AND `squad_id` = ?","id",array($get['id']));
            $anz_dr_wars = cnt("{prefix_clanwars}", " WHERE `datum` < ? && `punkte` = `gpunkte` AND `squad_id` = ?","id",array(time(),$get['id']));
            $anz_ge_wars = cnt("{prefix_clanwars}", " WHERE `datum` < ? AND `squad_id` = ?","id",array(time(),$get['id']));

            $wo_percent = !$anz_ge_wars ? 0 : @round($anz_wo_wars*100/$anz_ge_wars, 1);
            $lo_percent = !$anz_ge_wars ? 0 : @round($anz_lo_wars*100/$anz_ge_wars, 1);
            $dr_percent = !$anz_ge_wars ? 0 : @round($anz_dr_wars*100/$anz_ge_wars, 1);

            $wo_rawpercent = !$anz_ge_wars ? 0 : @round($anz_wo_wars*100/$anz_ge_wars, 0);
            $lo_rawpercent = !$anz_ge_wars ? 0 : @round($anz_lo_wars*100/$anz_ge_wars, 0);
            $dr_rawpercent = !$anz_ge_wars ? 0 : @round($anz_dr_wars*100/$anz_ge_wars, 0);

            $wo_balken = show(_votes_balken, array("width" => !$anz_wo_wars ? 1 : $wo_rawpercent));
            $lo_balken = show(_votes_balken, array("width" => !$anz_lo_wars ? 1 : $lo_rawpercent));
            $dr_balken = show(_votes_balken, array("width" => !$anz_dr_wars ? 1 : $dr_rawpercent));

            $anz_ges_wars = show(_cw_stats_ges_wars_sq, array("ge_wars" => $anz_ge_wars));
            $stats = show($dir."/stats", array("wo_wars" => $anz_wo_wars,
                                              "lo_wars" => $anz_lo_wars,
                                              "dr_wars" => $anz_dr_wars,
                                              "dr_percent" => $dr_percent,
                                              "lo_percent" => $lo_percent,
                                              "wo_percent" => $wo_percent,
                                              "won_balken" => $wo_balken,
                                              "lost_balken" => $lo_balken,
                                              "draw_balken" => $dr_balken,
                                              "ges_wars" => $anz_ges_wars,
                                              "ges_points" => $anz_ges_points));
        }

        $more = "";
        if($cnt_wars > settings('m_clanwars')) {
            $more = show(_cw_show_all, array("id" => $get['id']));
        }

        if($cnt_wars > 0) {
            $show .= show($dir."/squads_show", array("id" => $get['id'],
                                                     "shown" => $shown,
                                                     "display" => $display,
                                                     "wars" => $wars,
                                                     "squad" => $squad." [".$cnt_wars."]",
                                                     "img" => $img,
                                                     "stats" => $stats,
                                                     "more" => $more));
        }
    }

    $qry = $sql->select("SELECT `game`,`icon` FROM `{prefix_squads}` WHERE `status` = 1 GROUP BY `game` ORDER BY `game` ASC;");
    $legende = '';
    foreach($qry as $get) {
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $img = squad($get['icon']);
        $legende .= show(_cw_legende, array("game" => re($get['game']), "img" => $img, "class" => $class));
    }

    $legende = show($dir."/legende", array("legende" => $legende));
    $index = show($dir."/squads", array("stats" => $stats,
                                        "stats_all" => $stats_all,
                                        "legende" => $legende,
                                        "show" => $show));
}