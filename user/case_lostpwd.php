<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_lostpwd;
    if (!$chkMe) {
        if ($do == "sended") {
            $get = $sql->selectSingle("SELECT `id`,`user`,`level` FROM `{prefix_users}` WHERE `user` = ? AND `email` = ?;", array(up($_POST['user']), up($_POST['email'])));
            if ($sql->rowCount() && (isset($_POST['secure']) || $securimage->check($_POST['secure']))) {
                $pwd = mkpwd();
                $sql->update("UPDATE `{prefix_users}` SET `pwd` = ? WHERE `id` = ?;",array(md5($pwd),$get['id']));
                setIpcheck("pwd(" . $get['id'] . ")");
                $message = show(bbcode_email(re(settings('eml_pwd'))), array("user" => $get['user'], "pwd" => $pwd));
                sendMail($_POST['email'], re(settings('eml_pwd_subj')), $message);
                $index = info(_lostpwd_valid, "../user/?action=login");
            } else {
                setIpcheck("trypwd(" . $get['id'] . ")");
                if (settings('securelogin') && isset($_POST['secure']) && !$securimage->check($_POST['secure'])) {
                    $index = error(captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode, 1);
                } else {
                    $index = error(_lostpwd_failed, 1);
                }
            }
        } else {
            $index = show($dir . "/lostpwd");
        }
    } else {
        $index = error(_error_user_already_in, 1);
    }
}