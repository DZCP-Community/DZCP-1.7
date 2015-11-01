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
$where = _site_awards;
$dir = "awards";

## SECTIONS ##
switch ($action):
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_squads}` ORDER BY `pos`;");
        foreach($qry as $get) {
            if(!empty($_GET['showsquad']) || !empty($_GET['show'])) {
                if($_GET['showsquad'] == $get['id'] || $_GET['show'] == $get['id']) {
                    $shown = show(_klapptext_show, array("id" => $get['id']));
                    $display = "";
                } else {
                    $shown = show(_klapptext_dont_show, array("id" => $get['id']));
                    $display = "none";
                }
            } else {
                $shown = show(_klapptext_dont_show, array("id" => $get['id']));
                $display = "none";
            }

            $squad = show(_member_squad_squadlink, array("squad" => re($get['name']),
                                                         "id" => $get['id'],
                                                         "shown" => $shown));

            $img = show(_gameicon, array("icon" => $get['icon']));
            $qrym = $sql->select("SELECT s1.`id`,s1.`squad`,s1.`date`,s1.`place`,s1.`prize`,s1.`url`,s1.`event`,s2.`icon`,s2.`name` "
                               . "FROM `{prefix_awards}` AS `s1` "
                               . "LEFT JOIN `{prefix_squads}` AS `s2` "
                               . "ON s1.`squad` = s2.`id` "
                               . "WHERE s1.`squad` = ? "
                               . "ORDER BY s1.`date` DESC LIMIT ".settings::get('m_awards').";",array($get['id']));

            $entrys = cnt('{prefix_awards}', " WHERE `squad` = ?",'id',array($get['id']));
            $i = $entrys-($page - 1)*settings::get('m_awards'); $awards = "";
            foreach($qrym as $getm) {
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                if($getm['place'] == "1")
                    $replace = _awards_erster_img;
                elseif($getm['place'] == "2")
                    $replace = _awards_zweiter_img;
                elseif($getm['place'] == "3")
                    $replace = _awards_dritter_img;
                else
                    $replace = $getm['place'];

                $event = show(_awards_event, array("event" => re($getm['event']), "url" => $getm['url']));
                $awards .= show($dir."/awards_show", array("class" => $class,
                                                           "date" => date("d.m.Y", $getm['date']),
                                                           "place" => $replace,
                                                           "prize" => re($getm['prize']),
                                                           "event" => $event));
            }

            $show_all = "";
            if ($entrys > settings::get('m_awards')) {
                $show_all = show(_list_all_link, array("id" => $get['id']));
            }
            $showawards = show($dir."/awards", array("awards" => $awards, "show_all" => $show_all));
            if($entrys != 0) {
                $show .= show($dir."/squads_show", array("id" => $get['id'],
                                                         "shown" => $shown,
                                                         "display" => $display,
                                                         "awards" => $showawards,
                                                         "squad" => $squad." (".$entrys.")",
                                                         "img" => $img));
            }
        } //loop end

        $qry = $sql->select("SELECT `game`,`icon` FROM `{prefix_squads}` GROUP BY `game` ORDER BY `game` ASC;"); 
        $legende = '';
        foreach($qry as $get) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $img = squad($get['icon']);
            $legende .= show(_awards_legende, array("game" => re($get['game']), "img" => $img, "class" => $class));
        }

        $legende = show($dir."/legende", array("legende" => $legende));

        $anz_awards = cnt('{prefix_awards}');
        $anz_place_1 = cnt('{prefix_awards}', "WHERE `place` = 1");
        $anz_place_2 = cnt('{prefix_awards}', "WHERE `place` = 2");
        $anz_place_3 = cnt('{prefix_awards}', "WHERE `place` = 3");

        $place1_percent = !$anz_awards ? 0 : @round($anz_place_1*100/$anz_awards, 1);
        $place2_percent = !$anz_awards ? 0 : @round($anz_place_2*100/$anz_awards, 1);
        $place3_percent = !$anz_awards ? 0 : @round($anz_place_3*100/$anz_awards, 1);
        $place1_rawpercent = !$anz_awards ? 0 : @round($anz_place_1*100/$anz_awards, 0);
        $place2_rawpercent = !$anz_awards ? 0 : @round($anz_place_2*100/$anz_awards, 0);
        $place3_rawpercent = !$anz_awards ? 0 : @round($anz_place_3*100/$anz_awards, 0);

        $place1_balken = show(_votes_balken, array("width" => ($anz_place_1 != "0" ? $place1_rawpercent : 1)));
        $place2_balken = show(_votes_balken, array("width" => ($anz_place_2 != "0" ? $place2_rawpercent : 1)));
        $place3_balken = show(_votes_balken, array("width" => ($anz_place_3 != "0" ? $place3_rawpercent : 1)));

        $anz_awards_out = show(_awards_stats, array("anz" => $anz_awards));
        $anz_place_1_out = show(_awards_stats_1, array("anz" => $anz_place_1));
        $anz_place_2_out = show(_awards_stats_2, array("anz" => $anz_place_2));
        $anz_place_3_out = show(_awards_stats_3, array("anz" => $anz_place_3));

        $stats = show($dir."/stats", array("stats" => $anz_awards_out,
                                           "stats1" => $anz_place_1_out,
                                           "stats2" => $anz_place_2_out,
                                           "stats3" => $anz_place_3_out,
                                           "1_perc" => $place1_percent,
                                           "2_perc" => $place2_percent,
                                           "3_perc" => $place3_percent,
                                           "1_balken" => $place1_balken,
                                           "2_balken" => $place2_balken,
                                           "3_balken" => $place3_balken));

        $show_ = cnt($db['awards']) != 0 ? $show : show(_no_entrys_yet, array("colspan" => "10"));
        $index = show($dir."/main", array("stats" => $stats, "legende" => $legende, "show" => $show_));
    break;
    case 'showall';
        $qry = $sql->select("SELECT `id`,`shown`,`name`,`icon` FROM `{prefix_squads}` WHERE `id` = ?;",array(intval($_GET['id'])));
        foreach($qry as $get) {
            if(isset($_GET['showsquad'])) {
                if($_GET['showsquad'] == $get['id']) {
                    $shown = show(_klapptext_show, array("id" => $get['id']));
                    $display = "";
                } else {
                    $shown = show(_klapptext_dont_show, array("id" => $get['id']));
                    $display = "none";
                }
            } else {
                if($get['shown'] == "1" || $get['shown'] == "0") {
                    $shown = show(_klapptext_show, array("id" => $get['id']));
                    $display = "";
                } else {
                    $shown = show(_klapptext_dont_show, array("id" => $get['id']));
                    $display = "none";
                }
            }

            $squad = show(_member_squad_squadlink, array("squad" => re($get['name']), "id" => $get['id']));
            $img = show(_gameicon, array("icon" => re($get['icon'])));
            $qrym = $sql->select("SELECT s1.`id`,s1.`squad`,s1.`date`,s1.`place`,s1.`prize`,s1.`url`,s1.`event`,s2.`icon`,s2.`name` "
                               . "FROM `{prefix_awards}` AS `s1` "
                               . "LEFT JOIN `{prefix_squads}` AS `s2` "
                               . "ON s1.`squad` = s2.`id` "
                               . "WHERE s1.`squad` = ? "
                               . "ORDER BY s1.`date` DESC LIMIT ".($page - 1)*settings::get('m_awards').",".settings::get('m_awards').";",array($get['id']));

            $entrys = cnt('{prefix_awards}', "WHERE `squad` = ?","id",array($get['id']));
            $i = $entrys-($page - 1)*settings::get('m_awards'); $awards = "";
            foreach($qrym as $getm) {
                if ($getm['place'] == 1) {
                    $replace = _awards_erster_img;
                } elseif ($getm['place'] == 2) {
                    $replace = _awards_zweiter_img;
                } elseif ($getm['place'] == 3) {
                    $replace = _awards_dritter_img;
                } else {
                    $replace = $getm['place'];
                }

                $event = show(_awards_event, array("event" => $getm['event'], "url" => $getm['url']));
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $awards .= show($dir."/awards_show", array("class" => $class,
                                                           "date" => date("d.m.Y", $getm['date']),
                                                           "place" => $replace,
                                                           "prize" => $getm['prize'],
                                                           "event" => $event));

            }

            $nav = nav($entrys,settings::get('m_awards'),"?action=showall&amp;id=".$get['id']);
            $showawards = show($dir."/awards_show_all", array("squad" => _awards_head_squad,
                                                              "date" => _awards_head_date,
                                                              "place" => _awards_head_place,
                                                              "prize" => _awards_head_prize,
                                                              "url" => _awards_head_link,
                                                              "nav" => $nav,
                                                              "awards" => $awards));

            if($entrys != 0) {
                $show .= show($dir."/squads_show_all", array("id" => $get['id'],
                                                             "shown" => $shown,
                                                             "display" => $display,
                                                             "awards" => $showawards,
                                                             "squad" => $squad." (".$entrys.")",
                                                             "img" => $img));
            }
        }

        $qry = $sql->select("SELECT `game`,`icon` FROM `{prefix_squads}` GROUP BY `game` ORDER BY `game` ASC;"); 
        $legende = '';
        foreach($qry as $get) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $img = squad($get['icon']);
            $legende .= show(_awards_legende, array("game" => re($get['game']),
                                                    "img" => $img,
                                                    "class" => $class));
        }

        $legende = show($dir."/legende", array("legende" => $legende));
        $stats = show(_awards_stats, array("anz" => cnt($db['awards'])));
        $index = show($dir."/main", array("stats" => $stats, "legende" => $legende, "show" => $show));
    break;
endswitch;

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);