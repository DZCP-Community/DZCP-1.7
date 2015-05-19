<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_reg;
    if(!$chkMe) {
        $regcode = "";
        if(settings("regcode")) {
            $regcode = show($dir."/register_regcode", array("confirm" => _register_confirm,
                                                            "confirm_add" => _register_confirm_add,));
        }

        $index = show($dir."/register", array("error" => "",
                                              "r_name" => "",
                                              "r_nick" => "",
                                              "r_email" => "",
                                              "regcode" => $regcode));
    }
    else
        $index = error(_error_user_already_in, 1);

    if ($do == "add" && !$chkMe && isIP(visitorIp())) {
        $check_user = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `user`= ?;",
                      array(up($_POST['user'])));

        $check_nick = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `nick`= ?;",
                      array(up($_POST['nick'])));

        $check_email = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `email`= ?;",
                       array(up($_POST['email'])));

        $_POST['user'] = trim($_POST['user']); $_POST['nick'] = trim($_POST['nick']);

        if(empty($_POST['user']) || empty($_POST['nick']) || empty($_POST['email']) || ($_POST['pwd'] != $_POST['pwd2']) || (settings("regcode") && !$securimage->check($_POST['secure'])) || $check_user || $check_nick || $check_email) {
            if (settings("regcode") && !$securimage->check($_POST['secure'])) {
                $error = show("errors/errortable", array("error" => _error_invalid_regcode));
            }

            if ($_POST['pwd2'] != $_POST['pwd']) {
                $error = show("errors/errortable", array("error" => _wrong_pwd));
            }

            if (!check_email($_POST['email'])) {
                $error = show("errors/errortable", array("error" => _error_invalid_email));
            }

            if (empty($_POST['email'])) {
                $error = show("errors/errortable", array("error" => _empty_email));
            }

            if ($check_email) {
                $error = show("errors/errortable", array("error" => _error_email_exists));
            }

            if (empty($_POST['nick'])) {
                $error = show("errors/errortable", array("error" => _empty_nick));
            }

            if ($check_nick) {
                $error = show("errors/errortable", array("error" => _error_nick_exists));
            }

            if (empty($_POST['user'])) {
                $error = show("errors/errortable", array("error" => _empty_user));
            }

            if ($check_user) {
                $error = show("errors/errortable", array("error" => _error_user_exists));
            }

            $regcode = (settings("regcode") ? show($dir."/register_regcode", array()) : '');
            $index = show($dir."/register", array("error" => $error,
                                                  "r_name" => $_POST['user'],
                                                  "r_nick" => $_POST['nick'],
                                                  "r_email" => $_POST['email'],
                                                  "regcode" => $regcode));
        } else {
            if(empty($_POST['pwd'])) {
                $mkpwd = mkpwd();
                $pwd = md5($mkpwd);
                $msg = _info_reg_valid;
            } else {
                $mkpwd = $_POST['pwd'];
                $pwd = md5($mkpwd);
                $msg = _info_reg_valid_pwd;
            }

            $sql->insert("INSERT INTO `{prefix_users}`
                SET `user`     = ?,
                    `nick`     = ?,
                    `email`    = ?,
                    `ip`       = ?,
                    `pwd`      = ?,
                    `regdatum` = ".time().",
                    `level`    = 1,
                    `time`     = ".time().",
                    `status`   = 1;",
            array(up(trim($_POST['user'])),up(trim($_POST['nick'])),up(trim($_POST['email'])),visitorIp(),$pwd));

            $insert_id = $sql->lastInsertId();
            $sql->insert("INSERT INTO `{prefix_permissions}` SET `user` = ?;",array($insert_id));
            $sql->insert("INSERT INTO `{prefix_userstats}` SET `user` = ?, `lastvisit` = ".time().";",array($insert_id));

            setIpcheck("reg(".$insert_id.")");
            $message = show(bbcode_email(re(settings('eml_reg'))), array("user" => trim($_POST['user']), "pwd" => $mkpwd));
            sendMail(trim($_POST['email']),re(settings('eml_reg_subj')),$message);
            $index = info(show($msg, array("email" => $_POST['email'])), "../user/?action=login");
        }
    }
}