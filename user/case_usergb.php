<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_profil;
    $get = $sql->selectSingle("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($_GET['id'])));
    if (!$sql->rowCount()) {
        $index = error(_user_dont_exist, 1);
    } else {
        switch ($do) {
            case 'add':
                if ($userid >= 1) {
                    $toCheck = empty($_POST['eintrag']);
                } else {
                    $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['eintrag']) 
                    || !check_email($_POST['email']) || !$securimage->check($_POST['secure']);
                }

                if($toCheck) {
                    if($userid >= 1) {
                        if (empty($_POST['eintrag'])) {
                            $error = _empty_eintrag;
                        }
                        
                        $form = show("page/editor_regged", array("nick" => autor($userid), "von" => _autor));
                    } else {
                        $error = '';
                        if (!$securimage->check($_POST['secure'])) {
                            $error = captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode;
                        } elseif (empty($_POST['nick'])) {
                            $error = _empty_nick;
                        } elseif (empty($_POST['email'])) {
                            $error = _empty_email;
                        } elseif (!check_email($_POST['email'])) {
                            $error = _error_invalid_email;
                        } elseif (empty($_POST['eintrag'])) {
                            $error = _empty_eintrag;
                        }

                        $form = show("page/editor_notregged", array("postnick" => $_POST['nick'], 
                                                                    "postemail" => $_POST['email'], 
                                                                    "posthp" => $_POST['hp']));
                    }

                    $error = show("errors/errortable", array("error" => $error));
                    $index = show($dir."/usergb_add", array("ed" => "&amp;uid=".$_GET['id'],
                                                            "whaturl" => "add",
                                                            "id" => $_GET['id'],
                                                            "form" => $form,
                                                            "posteintrag" => $_POST['eintrag'],
                                                            "error" => $error));
                } else {
                    $getperm = $sql->selectSingle("SELECT `perm_gb`,`id` FROM `{prefix_users}` WHERE `id` = ?;",array(intval($_GET['id'])));
                    if ($getperm['perm_gb']) {
                        $nick = !isset($_POST['nick']) && $userid >= 1 ? data('nick',$userid) : up($_POST['nick']);
                        $email = !isset($_POST['email']) && $userid >= 1 ? data('email',$userid) : up($_POST['email']);
                        $hp = !isset($_POST['hp']) && $userid >= 1 ? data('hp',$userid) : up($_POST['hp']);
                        $uid = $userid >= 1 ? intval($userid) : 0;
                        $sql->insert("INSERT INTO `{prefix_usergb}` "
                                   . "SET `user`       = ?, "
                                       . "`datum`      = ".time().", "
                                       . "`nick`       = ?, "
                                       . "`email`      = ?, "
                                       . "`hp`         = ?, "
                                       . "`reg`        = ?, "
                                       . "`nachricht`  = ?, "
                                       . "`ip`         = ?;",
                                array(intval($_GET['id']),$nick,$email,$hp,$uid,up($_POST['eintrag']),$userip));

                        setIpcheck("mgbid(".$getperm['id'].")");
                        $index = info(_usergb_entry_successful, "?action=user&amp;id=".$_GET['id']."&show=gb");
                    }
                }
            break;
            case 'edit':
                if(intval($_POST['reg']) == $userid || permission('editusers')) {
                    $addme = ''; $params = array();
                    if(!intval($_POST['reg'])) {
                         $addme = " `nick` = ?, `email` = ?, `hp` = ?,";
                         array_push($params, up($_POST['nick']),up($_POST['email']),up(links($_POST['hp'])));
                    }

                    $editedby = show(_edited_by, array("autor" => autor($userid), "time" => date("d.m.Y H:i", time())._uhr));
                    array_push($params, up($_POST['eintrag']), intval($_POST['reg']),up($editedby), intval($_GET['gbid']));
                    $sql->update("UPDATE ".$db['usergb']." SET".$addme." `nachricht` = ?, `reg` = ?, `editby` = ? WHERE id = ?;",$params);
                    $index = info(_gb_edited, "?action=user&show=gb&id=".$_GET['id']);
                } else {
                    $index = error(_error_edit_post,1);
                }
            break;
        }
    }
}