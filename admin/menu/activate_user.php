<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_activate_user;

switch ($do) {
    case 'activate':
        $sql->update("UPDATE `{prefix_users}` SET `level` = 1, `status` = 1, `actkey` = '' WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_actived, "?admin=activate_user", 2);
    break;
    case 'delete':
        if(($id = isset($_GET['id']) ? $_GET['id'] : false) != false) {
            $sql->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;",array(intval($id)));
            $sql->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;",array(intval($id)));
            $sql->delete("DELETE FROM `{prefix_userstats}` WHERE `user` = ?;",array(intval($id)));
            $show = info(_user_deleted, "?admin=activate_user", 3);
        }
    break;
    case 'delete-all':
        if(isset($_POST['userid']) && count($_POST['userid']) >= 1) {
            foreach($_POST['userid'] as $id) {
                $sql->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;",array(intval($id)));
                $sql->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;",array(intval($id)));
                $sql->delete("DELETE FROM `{prefix_userstats}` WHERE `user` = ?;",array(intval($id)));
            }

            $show = info(_users_deleted, "?admin=activate_user", 4);
        }
    break;
    case 'enable-all':
        if(isset($_POST['userid']) && count($_POST['userid']) >= 1) {
            foreach ($_POST['userid'] as $id) {
                $sql->update("UPDATE `{prefix_users}` SET `level` = 1, `status` = 1, `actkey` = '' WHERE `id` = ?;",array(intval($id)));
            }

            $show = info(_actived_all, "?admin=activate_user", 3);
        }
    break;
    case 'resend':
        if(($id = isset($_GET['id']) ? $_GET['id'] : false) != false) {
            $get = $sql->fetch("SELECT `user`,`id`,`email` FROM `{prefix_users}` WHERE `id` = ?;",array($id));
            $sql->update("UPDATE `{prefix_userstats}` SET `akl` = (akl+1) WHERE `user` = ?;",array($get['id']));
            $sql->update("UPDATE `{prefix_users}` SET `actkey` = ? WHERE `id` = ?;",array(($guid=GenGuid()),$get['id']));
            $akl_link = 'http://'.$httphost.'/user/?action=akl&do=activate&key='.$guid;
            $akl_link_page = 'http://'.$httphost.'/user/?action=akl&do=activate';
            $message = show(bbcode_email(settings::get('eml_akl_register')), 
                        array("nick" => stringParser::decode($get['user']), 
                              "link_page" => '<a href="'.$akl_link_page.'" target="_blank">'.$akl_link_page.'</a>', 
                              "guid" => $guid, 
                              "link" => '<a href="'.$akl_link.'" target="_blank">Link</a>'));
            sendMail(stringParser::decode($get['email']), stringParser::decode(settings::get('eml_akl_register_subj')), $message);
            $show = info(show(_admin_akl_resend,array('email' => $get['email'])), "?admin=activate_user", 4);
        }
    break;
    case 'send-all':
        if(isset($_POST['userid']) && count($_POST['userid']) >= 1) {
            $emails = ''; $i = 0;
            foreach($_POST['userid'] as $id) {
                $get = $sql->fetch("SELECT `user`,`id`,`email` FROM `{prefix_users}` WHERE `id` = ?;",array($id));
                $sql->update("UPDATE `{prefix_userstats}` SET `akl` = (akl+1) WHERE `user` = ?;",array($get['id']));
                $sql->update("UPDATE `{prefix_users}` SET `actkey` = '".($guid=GenGuid())."' WHERE `id` = ?;",array($get['id']));
                $akl_link = 'http://'.$httphost.'/user/?action=akl&do=activate&key='.$guid;
                $akl_link_page = 'http://'.$httphost.'/user/?action=akl&do=activate';
                $message = show(bbcode_email(settings::get('eml_akl_register')), 
                            array("nick" => stringParser::decode($get['user']), 
                                  "link_page" => '<a href="'.$akl_link_page.'" target="_blank">'.$akl_link_page.'</a>', 
                                  "guid" => $guid, 
                                  "link" => '<a href="'.$akl_link.'" target="_blank">Link</a>'));
                sendMail(stringParser::decode($get['email']), stringParser::decode(settings::get('eml_akl_register_subj')), $message);
                $emails .= (!$i ? $get['email'] : ', '.$get['email']); $i++;
            }

            $show = info(show(_admin_akl_resend,array('email' => $emails)), "?admin=activate_user", 8);
        }
    break;
    default:
        $qry = $sql->select("SELECT `id`,`bday` FROM `{prefix_users}` WHERE `level` = 0 AND `actkey` IS NOT NULL ORDER BY nick;"); 
        $activate = ''; $color = 1;
        foreach($qry as $get) {
            $resend = show(_emailicon_non_mailto, array("email" => '?admin=activate_user&amp;do=resend&amp;id='.$get['id']));
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $edit = str_replace("&amp;id=","",show("page/button_edit_akl", array("id" => $get['id'], "action" => "../user/?action=admin&edit=", "title" => _button_title_edit)));
            $akl = show("page/button_akl", array("id" => $get['id'], "action" => "admin=activate_user&amp;do=activate&amp;id=", "title" => _button_title_akl));
            $delete = show("page/button_delete", array("id" => $get['id'], "action" => "admin=activate_user&amp;do=delete", "title" => _button_title_del));
            $activate .= show($dir."/activate_user_show", array("nick" => autor($get['id'],'', 0, '',25),
                                                                "akt" => $akl,
                                                                "resend" => $resend,
                                                                "age" => getAge($get['bday']),
                                                                "sended" => userstats('akl',$get['id']),
                                                                "edit" => $edit,
                                                                "delete" => $delete,
                                                                "class" => $class,
                                                                "id" => $get['id'],
                                                                "onoff" => onlinecheck($get['id'])));
        }

        if(empty($activate)) {
            $activate = '<tr><td colspan="9" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $show = show($dir."/activate_user", array("value" => _button_value_search, "show" => $activate));
    break;
}