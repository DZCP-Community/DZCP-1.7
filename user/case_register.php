<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_reg;
    if(!$chkMe) {
        $regcode = "";
        if(settings::get("regcode")) {
            $regcode = show($dir."/register_regcode", array("confirm" => _register_confirm,
                                                            "confirm_add" => _register_confirm_add,));
        }

        $index = show($dir."/register", array("error" => "",
                                              "r_name" => "",
                                              "r_nick" => "",
                                              "r_email" => "",
                                              "regcode" => $regcode));
    } else
        $index = error(_error_user_already_in, 1);

    if ($do == "add" && !$chkMe && isIP(visitorIp())) {
        $check_user = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `user`= ?;",
                      array(stringParser::encode($_POST['user'])));

        $check_nick = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `nick`= ?;",
                      array(stringParser::encode($_POST['nick'])));

        $check_email = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `email`= ?;",
                       array(stringParser::encode($_POST['email'])));

        $_POST['user'] = trim($_POST['user']); $_POST['nick'] = trim($_POST['nick']);

        if(empty($_POST['user']) || empty($_POST['nick']) || empty($_POST['email']) 
                || ($_POST['pwd'] != $_POST['pwd2']) || (settings::get("regcode") && 
                !$securimage->check($_POST['secure'])) || $check_user || $check_nick || $check_email) {
            if (settings::get("regcode") && !$securimage->check($_POST['secure'])) {
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

            $regcode = (settings::get("regcode") ? show($dir."/register_regcode", array()) : '');
            $index = show($dir."/register", array("error" => $error,
                                                  "r_name" => $_POST['user'],
                                                  "r_nick" => $_POST['nick'],
                                                  "r_email" => $_POST['email'],
                                                  "regcode" => $regcode));
        } else {
            if(empty($_POST['pwd'])) {
                $mkpwd = mkpwd();
                $pwd = pwd_encoder($mkpwd);
                $msg = _info_reg_valid;
            } else {
                $mkpwd = $_POST['pwd'];
                $pwd = pwd_encoder($mkpwd);
                $msg = _info_reg_valid_pwd;
            }

            ## Neuen User in die Datenbank schreiben ##
            $sql->insert("INSERT INTO `{prefix_users}` "
               . "SET `user`     = ?, "
                   . "`nick`     = ?, "
                   . "`email`    = ?, "
                   . "`ip`       = ?, "
                   . "`pwd`      = ?, "
                   . "`pwd_encoder` = ?, "
                   . "`actkey`   = ?, "
                   . "`regdatum` = ".($time=time()).", "
                   . "`level`    = ?, "
                   . "`profile_access` = 1,"
                   . "`time`     = ".$time.", "
                   . "`status`   = ?;",
            array(stringParser::encode(trim($_POST['user'])),
                stringParser::encode(trim($_POST['nick'])),
                stringParser::encode(trim($_POST['email'])),
                visitorIp(),
                stringParser::encode($pwd),
                settings::get('default_pwd_encoder'),
                (settings::get('use_akl') ? ($guid=GenGuid()) : ''),
                (settings::get('use_akl') ? 0 : 1),
                (settings::get('use_akl') >= 1 ? 0 : 1)));

            ## Lese letzte ID aus ##
            $insert_id = $sql->lastInsertId();
            
             ## Lege User in der Permissions Tabelle an ##
            $sql->insert("INSERT INTO `{prefix_permissions}` SET `user` = ?;",array($insert_id));
            
            ## Lege User in der User-Statistik Tabelle an ##
            $sql->insert("INSERT INTO `{prefix_userstats}` SET `user` = ?, `lastvisit` = ?;",array($insert_id,$time));

            ## Ereignis in den Adminlog schreiben ##
            setIpcheck("reg(".$insert_id.")");
            
            ## E-Mail zusammenstellen und senden ##
            if(settings::get('use_akl') == 1) {
                $akl_link = 'http://'.$httphost.'/?action=akl&do=activate&key='.$guid;
                $akl_link_page = 'http://'.$httphost.'/?action=akl&do=activate';

                $message = show(bbcode_email(stringParser::decode(settings::get('eml_akl_register'))), 
                        array("nick" => $_POST['user'], "link_page" => '<a href="'.$akl_link_page.'" target="_blank">'.$akl_link_page.'</a>', "guid" => $guid, "link" => '<a href="'.$akl_link.'" target="_blank">Link</a>'));
                sendMail(trim($_POST['email']),stringParser::decode(settings::get('eml_akl_register_subj')),$message);
            }

            $message = show(bbcode_email(stringParser::decode(settings::get('eml_reg'))), array("user" => trim($_POST['user']), "pwd" => $mkpwd));
            sendMail(trim($_POST['email']),stringParser::decode(settings::get('eml_reg_subj')),$message);

            ## Nachricht anzeigen und zum  Userlogin weiterleiten ##
            $index = info(show(settings::get('use_akl') ? (settings::get('use_akl') == 2 ? _info_reg_valid_akl_ad : _info_reg_valid_akl) : _info_reg_valid, array("email" => $_POST['email'])), "?action=login",5);
        }
    }
}