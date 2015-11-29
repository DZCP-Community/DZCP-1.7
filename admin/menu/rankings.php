<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

$where = $where.': '._config_rankings;
switch ($do) {
    case 'add':
        $qry = $sql->select("SELECT `id`,`name`,`icon` FROM `{prefix_squads}` WHERE `status` = 1 ORDER BY `game` ASC;");
        $squads = "";
        foreach($qry as $get) {
            $squads .= show(_select_field_ranking_add, array("what" => re($get['name']),
                                                             "value" => $get['id'],
                                                             "icon" => re($get['icon']),
                                                             "sel" => ""));
        }
        
        $show = show($dir."/form_rankings", array("head" => _rankings_add_head,
                                                  "do" => "addranking",
                                                  "what" => _button_value_add,
                                                  "squads" => $squads,
                                                  "e_league" => "",
                                                  "e_rank" => "",
                                                  "e_url" => ""));
    break;
    case 'addranking':
        if(empty($_POST['league']) || empty($_POST['url']) || empty($_POST['rank'])) {
            if (empty($_POST['league'])) {
                $show = error(_error_empty_league, 1);
            } elseif (empty($_POST['url'])) {
                $show = error(_error_empty_url, 1);
            } elseif (empty($_POST['rank'])) {
                $show = error(_error_empty_rank, 1);
            }
        } else {
            $sql->insert("INSERT INTO `{prefix_rankings}` SET `league` = ?, "
                                                                  . "`squad` = ?, "
                                                                  . "`url` = ?, "
                                                                  . "`rank` = ?, "
                                                                  . "`postdate` = ?;",
                    array(up($_POST['league']),up($_POST['squad']),links($_POST['url']),intval($_POST['rank']),time()));
            $show = info(_ranking_added, "?admin=rankings");
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT `league`,`rank`,`url`,`squad` FROM `{prefix_rankings}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $qrys = $sql->select("SELECT * FROM `{prefix_squads}` WHERE `status` = 1 ORDER BY `game` ASC;");
        foreach($qrys as $gets) {
            $sel = ($get['squad'] == $gets['id']) ? 'selected="selected"' : '';
            $squads .= show(_select_field_ranking_add, array("what" => re($gets['name']),
                                                             "value" => $gets['id'],
                                                             "icon" => $gets['icon'],
                                                             "sel" => $sel));
        }
        
        $show = show($dir."/form_rankings", array("head" => _rankings_edit_head,
                                                  "do" => "editranking&amp;id=".$_GET['id']."",
                                                  "what" => _button_value_edit,
                                                  "squads" => $squads,
                                                  "e_league" => re($get['league']),
                                                  "e_rank" => $get['rank'],
                                                  "e_url" => re($get['url'])));
    break;
    case 'editranking':
        if(empty($_POST['league']) || empty($_POST['url']) || empty($_POST['rank'])) {
            if (empty($_POST['league'])) {
                $show = error(_error_empty_league, 1);
            } else if (empty($_POST['url'])) {
                $show = error(_error_empty_url, 1);
            } else if (empty($_POST['rank'])) {
                $show = error(_error_empty_rank, 1);
            }
        } else {
            $get = $sql->fetch("SELECT `id`,`rank` FROM `{prefix_rankings}` WHERE `id` = ?;",array(intval($_GET['id'])));
            $sql->update("UPDATE `{prefix_rankings}` SET `league` = ?,"
                                                      . "`squad` = ?,"
                                                      . "`url` = ?,"
                                                      . "`rank` = ?,"
                                                      . "`lastranking` = ?,"
                                                      . "`postdate` = ?"
                                                      . " WHERE id = ?;",
                    array(up($_POST['league']),up($_POST['squad']),up(links($_POST['url'])),
                        intval($_POST['rank']),intval($get['rank']),time(),intval($get['id'])));
            $show = info(_ranking_edited, "?admin=rankings");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_rankings}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_ranking_deleted, "?admin=rankings");
    break;
    default:
        $qry = $sql->select("SELECT s1.*,s2.`name`,s2.`id` AS `sqid` "
                . "FROM `{prefix_rankings}` AS `s1` "
                . "LEFT JOIN `{prefix_squads}` AS `s2` "
                . "ON s1.`squad` = s2.`id` ".
                orderby_sql(array('name','league'), 'ORDER BY s1.`postdate` DESC').";");
        $show = '';
        foreach($qry as $get) {
          $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                        "action" => "admin=rankings&amp;do=edit",
                                                        "title" => _button_title_edit));
          
          $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                            "action" => "admin=rankings&amp;do=delete",
                                                            "title" => _button_title_del,
                                                            "del" => convSpace(_confirm_del_ranking)));

          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
          $show .= show($dir."/rankings_show", array("squad" => re($get['name']),
                                                      "league" => re($get['league']),
                                                      "id" => $get['sqid'],
                                                      "class" => $class,
                                                      "edit" => $edit,
                                                      "delete" => $delete));
        }

        $show = show($dir."/rankings", array("show" => $show,
                                             "order_squad" => orderby('name'),
                                             "order_league" => orderby('league')));
    break;
}