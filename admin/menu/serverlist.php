<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._slist_head_admin;

switch ($do) {
    case 'accept':
        $sql->update("UPDATE `{prefix_serverliste}` SET `checked` = ? WHERE `id` = ?;",
              array(intval($_POST['checked']),intval($_POST['id'])));
        if (intval($_POST['checked']) == 1) {
            $show = info(_error_server_accept, "?admin=serverlist");
        } else {
            $show = info(_error_server_dont_accept, "?admin=serverlist");
        }
        break;
    case 'delete':
      $sql->delete("DELETE FROM `{prefix_serverliste}` WHERE `id` = ?;",array(intval($_GET['id'])));
      $show = info(_slist_server_deleted, "?admin=serverlist");
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_serverliste}`;");
        foreach($qry as $get) {
            $selected = ($get['checked'] ? 'selected="selected"' : '');
            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=serverlist&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_server)));

            if(empty($get['clanurl'])) {
                $clanname = show(_slist_clanname_without_url, array("name" => re($get['clanname'])));
            } else {
                $clanname = show(_slist_clanname_with_url, array("name" => re($get['clanname']),
                                                                 "url" => re($get['clanurl'])));
            }

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/slist_show", array("id" => $get['id'],
                                                    "clanname" => $clanname,
                                                    "serverip" => re($get['ip']),
                                                    "serverpwd" => re($get['pwd']),
                                                    "class" => $class,
                                                    "delete" => $delete,
                                                    "selected" => $selected,
                                                    "serverport" => $get['port']));
        }

        if (empty($show)) {
            $show = '<tr><td colspan="3" class="contentMainSecond">' . _no_entrys . '</td></tr>';
        }

        $show = show($dir."/slist", array("show" => $show));
    break;
}