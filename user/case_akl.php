<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    switch ($do) {
        case 'send':
            if (isset($_SESSION['akl_id']) && !empty($_SESSION['akl_id'])) {
                $get = $sql->fetch("SELECT `user`,`id`,`email`,`level`,`actkey` FROM `{prefix_users}` WHERE `id` = ?;",
                        array($_SESSION['akl_id']));
            } else {
                $get = $sql->fetch("SELECT `user`,`id`,`email`,`level`,`actkey` FROM `{prefix_users}` WHERE `email` = ?;",
                        array(isset($_GET['email']) ? stringParser::encode($_GET['email']) : ''));
            }

            if($sql->rowCount()) {
                if(!$get['level'] && !empty($get['actkey'])) {
                    $sql->update("UPDATE `{prefix_userstats}` SET `akl` = (akl+1) WHERE `user` = ?;",array($get['id']));
                    $sql->update("UPDATE `{prefix_users}` SET `actkey` = ? WHERE `id` = ?;",
                            array(stringParser::encode($guid = GenGuid()),$get['id']));
                    $akl_link = 'http://'.$httphost.'/user/?action=akl&do=activate&key='.$guid;
                    $akl_link_page = 'http://'.$httphost.'/user/?action=akl&do=activate';
                    $message = show(bbcode_email(settings::get('eml_akl_register')), 
                        array("nick" => stringParser::decode($get['user']), 
                              "link_page" => '<a href="'.$akl_link_page.'" target="_blank">'.$akl_link_page.'</a>', 
                              "guid" => $guid, 
                              "link" => '<a href="'.$akl_link.'" target="_blank">Link</a>'));
                    sendMail(stringParser::decode($get['email']), stringParser::decode(settings::get('eml_akl_register_subj')), $message);
                    $index = info(show(_reg_akl_sended,array('email' => stringParser::decode($get['email']))), "?action=login");
                } else if (!$get['level'] && empty($get['actkey'])) {
                    $index = info(_reg_akl_locked, "../news/");
                } else {
                    $sql->update("UPDATE `{prefix_users}` SET `actkey` = '' WHERE `id` = ?;", array($get['id']));
                    $index = info(_reg_akl_activated, "../news/");
                }
            } else
                $index = info(_reg_akl_email_nf, "../news/");
        break;
        case 'activate':
            if ((isset($_GET['key']) && !empty($_GET['key'])) || (isset($_POST['key']) && !empty($_POST['key']))) {
                $get = $sql->fetch("SELECT `id` FROM `{prefix_users}` WHERE `actkey` = ?;", array(strtoupper(trim(isset($_POST['key']) ? $_POST['key'] : $_GET['key']))));
                if ($sql->rowCount()) {
                    $sql->update("UPDATE `{prefix_users}` SET `level` = 1, `status` = 1, `actkey` = '' WHERE `id` = ?;", array($get['id']));
                    $index = info(_reg_akl_valid, "../user/?action=login");
                } else {
                    $index = info(_reg_akl_invalid, "../user/?action=akl");
                }
            } else {
                $index = show($dir . "/activate_code", array("value" => _button_value_activate));
            }
            break;
        default:
            $index = show($dir."/activate_code", array("value" => _button_value_activate));
        break;
    }
}