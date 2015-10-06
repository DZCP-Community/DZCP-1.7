<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._admin_dlkat;

switch ($do) {
    case 'edit':
        $get = $sql->selectSingle("SELECT `name` FROM `{prefix_download_kat}` WHERE `id` = ?;",
                array(intval($_GET['id'])));
        $show = show($dir."/dlkats_form", array("newhead" => _dl_edit_head,
                                                "do" => "editkat&amp;id=".$_GET['id']."",
                                                "kat" => re($get['name']),
                                                "what" => _button_value_edit));
    break;
    case 'editkat':
        if(empty($_POST['kat'])) {
            $show = error(_dl_empty_kat,1);
        } else {
            $sql->update("UPDATE `{prefix_download_kat}` SET `name` = ? WHERE `id` = ?;",
                    array(up($_POST['kat']),intval($_GET['id'])));
            $show = info(_dl_admin_edited, "?admin=dlkats");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_download_kat}` WHERE `id` = ?;",
                array(intval($_GET['id'])));
        $show = info(_dl_admin_deleted, "?admin=dlkats");
    break;
    case 'new':
        $show = show($dir."/dlkats_form", array("newhead" => _dl_new_head,
                                                "do" => "add",
                                                "kat" => "",
                                                "what" => _button_value_add));
    break;
    case 'add':
        if(empty($_POST['kat'])) {
            $show = error(_dl_empty_kat,1);
        } else {
            $sql->insert("INSERT INTO `{prefix_download_kat}` SET `name` = ?;",
                  array(up($_POST['kat'])));
            $show = info(_dl_admin_added, "?admin=dlkats");
        }
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_download_kat}` ORDER BY `name`;");
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=dlkats&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=dlkats&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_kat)));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/dlkats_show", array("edit" => $edit,
                                                     "name" => re($get['name']),
                                                     "class" => $class,
                                                     "delete" => $delete));
        }

        $show = show($dir."/dlkats", array("show" => $show));
    break;
}