<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._awards_head;

switch ($do) {
    case 'new':
        $qry = $sql->select("SELECT `id`,`name`,`game`,`icon` FROM `{prefix_squads}` ORDER BY `game` ASC;"); $squads = "";
        foreach($qry as $get) {
            $squads .= show(_awards_admin_add_select_field_squads, array("name" => re($get['name']),
                                                                         "game" => re($get['game']),
                                                                         "icon" => re($get['icon']),
                                                                         "id" => $get['id']));
        }

        $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",time())),
                                                    "month" => dropdown("month",date("m",time())),
                                                    "year" => dropdown("year",date("Y",time()))));

        $show = show($dir."/form_awards", array("head" => _awards_admin_head_add,
                                                "squads" => $squads,
                                                "dropdown_date" => $dropdown_date,
                                                "do" => "add",
                                                "what" => _button_value_add,
                                                "award_event" => "",
                                                "award_url" => "",
                                                "award_place" => "",
                                                "award_prize" => ""));
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_awards}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $qrym = $sql->select("SELECT `id`,`name`,`game`,`icon` FROM `{prefix_squads}` ORDER BY game"); $squads = "";
        foreach($qrym as $gets) {
            $sel = $get['squad'] == $gets['id'] ? 'selected="selected"' : '';
            $squads .= show(_awards_admin_edit_select_field_squads, array("id" => $gets['id'],
                                                                          "name" => re($gets['name']),
                                                                          "game" => re($gets['game']),
                                                                          "icon" => re($gets['icon']),
                                                                          "sel" => $sel));
        }

        $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",$get['date'])),
                                                    "month" => dropdown("month",date("m",$get['date'])),
                                                    "year" => dropdown("year",date("Y",$get['date']))));

        $show = show($dir."/form_awards", array("head" => _awards_admin_head_edit,
                                                "do" => "editaw&amp;id=".$_GET['id']."",
                                                "what" => _button_value_edit,
                                                "squads" => $squads,
                                                "dropdown_date" => $dropdown_date,
                                                "award_event" => re($get['event']),
                                                "award_url" => re($get['url']),
                                                "award_place" => re($get['place']),
                                                "award_prize" => re($get['prize'])));
    break;
    case 'add':
        if(empty($_POST['event']) || empty($_POST['url'])) {
            if(empty($_POST['event'])) {
                $show = error(_awards_empty_event, 1);
            } elseif(empty($_POST['url'])) {
                $show = error(_awards_empty_url, 1);
            }
        } else {
            $place = (empty($_POST['place']) ? "-" : $_POST['place']);
            $prize = (empty($_POST['prize']) ? "-" : $_POST['prize']);
            $datum = mktime(0,0,0,$_POST['m'],$_POST['t'],$_POST['j']);
            $sql->insert("INSERT INTO `{prefix_awards}` SET `date` = ?,"
            . "`postdate` = ?,`squad` = ?,`event` = ?,`url` = ?,`place` = ?,`prize` = ?;",
                array(intval($datum),time(),intval($_POST['squad']),up($_POST['event']),
                    up(links($_POST['url'])),up($place),up($prize)));

            $show = info(_awards_admin_added, "?admin=awards");
        }
    break;
    case 'editaw':
        if(empty($_POST['event']) || empty($_POST['url'])) {
            if(empty($_POST['event'])) {
                $index = error(_awards_empty_event, 1);
            } elseif(empty($_POST['url'])) {
                $index = error(_awards_empty_url, 1);
            }
        } else {
            $place = (empty($_POST['place']) ? "-" : $_POST['place']);
            $prize = empty($_POST['prize']) ? "-" : $_POST['prize'];
            $datum = mktime(0,0,0,$_POST['m'],$_POST['t'],$_POST['j']);
            $sql->update("UPDATE `{prefix_awards}` SET `date` = ?, `squad` = ?, "
            . "`event` = ?, `url` = ?, `place` = ?, `prize` = ? WHERE id = ?;",
                array(intval($datum),intval($_POST['squad']),up($_POST['event']),
                    up(links($_POST['url'])),up($place),up($prize),intval($_GET['id'])));

            $show = info(_awards_admin_edited, "?admin=awards");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_awards}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_awards_admin_deleted, "?admin=awards");
    break;
    default:
        $qry = $sql->select("SELECT `id`,`date`,`event`,`squad` FROM `{prefix_awards}` ".
                orderby_sql(array("event","date"), 'ORDER BY `date` DESC;'));

        $show = '';
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=awards&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=awards&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_award)));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/awards_show", array("datum" => date("d.m.Y",$get['date']),
                                                     "award" => re($get['event']),
                                                     "id" => $get['squad'],
                                                     "class" => $class,
                                                     "edit" => $edit,
                                                     "delete" => $delete));
        }

        if(empty($show)) {
            $show = '<tr><td colspan="5" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $show = show($dir."/awards", array("show" => $show,
                                           "order_titel" => orderby('event'),
                                           "order_date" => orderby('date')));
    break;
}