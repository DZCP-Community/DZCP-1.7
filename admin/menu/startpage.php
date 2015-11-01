<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

switch ($do) {
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_startpage}` WHERE `id` = ?;",array(intval($_GET['id'])));
        notification::add_success(_admin_startpage_deleted, "?admin=startpage");
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_startpage}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if(isset($_POST['name']) && isset($_POST['url']) && isset($_POST['level'])) {
            if(empty($_POST['name']))
                notification::add_error(_admin_startpage_no_name);
            else if(empty($_POST['url']))
                notification::add_error(_admin_startpage_no_url);
            else {
                $sql->update("UPDATE `{prefix_startpage}` SET `name` = ?, `url` = ?, `level` = ? WHERE `id` = ?;",
                        array(up($_POST['name']),up($_POST['url']),intval($_POST['level']),intval($_GET['id'])));
                
                notification::add_success(_admin_startpage_editd, "?admin=startpage");
            }
        }

        if(!notification::is_success()) {
            if(notification::has()) {
                javascript::set('AnchorMove', 'notification-box');
            }
            
            $selu = $get['level'] == 1 ? 'selected="selected"' : '';
            $selt = $get['level'] == 2 ? 'selected="selected"' : '';
            $selm = $get['level'] == 3 ? 'selected="selected"' : '';
            $sela = $get['level'] == 4 ? 'selected="selected"' : '';
            $elevel = show(_elevel_startpage_select, array("selu" => $selu,
                                                           "selt" => $selt,
                                                           "selm" => $selm,
                                                           "sela" => $sela));
            
            $show = show($dir."/startpage_form", array("head" => _admin_startpage_edit,
                                                        "do" => "edit&amp;id=".$_GET['id'],
                                                        "name" => (isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : re($get['name'])),
                                                        "url" => (isset($_POST['url']) ? $_POST['url'] : re($get['url'])),
                                                        "level" => $elevel,
                                                        "what" => _button_value_edit,
                                                        "error" => (!empty($error) ? show("errors/errortable", array("error" => $error)) : "")));
        }
    break;
    case 'new':
        if(isset($_POST['name']) && isset($_POST['url']) && isset($_POST['level'])) {
            if(empty($_POST['name']))
                notification::add_error(_admin_startpage_no_name);
            else if(empty($_POST['url']))
                notification::add_error(_admin_startpage_no_url);
            else {
                $sql->insert("INSERT INTO `{prefix_startpage}` SET `name` = ?, `url` = ?, `level` = ?;",
                        array(up($_POST['name']),up($_POST['url']),intval($_POST['level'])));
                
                notification::add_success(_admin_startpage_added, "?admin=startpage");
            }
        }

        if(!notification::is_success()) {
            if(notification::has()) {
                javascript::set('AnchorMove', 'notification-box');
            }
            
            $elevel = show(_elevel_startpage_select, array("selu" => '',
                                                           "selt" => '',
                                                           "selm" => '',
                                                           "sela" => ''));
            
            $show = show($dir."/startpage_form", array("head" => _admin_startpage_add_head, "do" => "new", "name" => (isset($_POST['name']) ? $_POST['name'] : ''),
            "url" => (isset($_POST['url']) ? $_POST['url'] : ''), "level" => $elevel, "what" => _button_value_add, "error" => (!empty($error) ? show("errors/errortable", array("error" => $error)) : "")));
        }
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_startpage}`;"); $color = 0; $show = '';
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'], "action" => "admin=startpage&amp;do=edit", "title" => _button_title_edit));
            $delete = show("page/button_delete_single", array("id" => $get['id'], "action" => "admin=startpage&amp;do=delete", "title" => _button_title_del, "del" => _confirm_del_entry));
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/startpage_show", array("edit" => $edit, "name" => re($get['name']), "url" => re($get['url']), "class" => $class, "delete" => $delete));
        }

        if(empty($show))
            $show = show(_no_entrys_yet, array("colspan" => "4"));

        $show = show($dir."/startpage", array("show" => $show));
    break;
}