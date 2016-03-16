<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_GB')) exit();
switch($do) {
    case 'addcomment':
        $get = $sql->fetch("SELECT * FROM `{prefix_gb}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if(($chkMe != 'unlogged' && $get['reg'] == userid()) || permission("gb")) {
            if(isset($_GET['save'])) {
                if(empty($_POST['eintrag'])) {
                    if(empty($_POST['eintrag'])) {
                        javascript::set('AnchorMove', 'comForm');
                        notification::add_error(_empty_eintrag);
                    }
                } else {
                    $sql->insert("INSERT INTO `{prefix_gbcomments}` SET `gbe` = ?, `datum` = ?, `reg` = ?,`comment` = ?,`ip` = ?;",
                        array($get['id'],time(),$userid,stringParser::encode($_POST['eintrag']),stringParser::encode(visitorIp())));
                    $index = info(_gb_comment_added, "../gb/");
                }
            }

            if(empty($index)) {
                $where = $where.': '._gb_addcomment_new;
                $gbhp = (!empty($get['hp']) ? show(_hpicon, array("hp" => stringParser::decode($get['hp']))) : '');
                $gbemail = (!empty($get['email']) ? CryptMailto(stringParser::decode($get['email'])) : '');
                $gbtitel = show(_gb_titel, array("postid" => "?",
                                                 "nick" => stringParser::decode(data($get['reg'], "nick")),
                                                 "edit" => "",
                                                 "public" => "",
                                                 "delete" => "",
                                                 "comment" => "",
                                                 "id" => $get['reg'],
                                                 "email" => $gbemail,
                                                 "datum" => date("d.m.Y", $get['datum']),
                                                 "zeit" => date("H:i", $get['datum']),
                                                 "hp" => $gbhp));

                $entry = show($dir."/gb_show", array("comments" => '', "gbtitel" => $gbtitel, "nachricht" => show(stringParser::decode($get['nachricht']),array(),array('gb_addcomment_from' => _gb_addcomment_from)), "editby" => stringParser::decode($get['editby']), "ip" => $get['ip']));
                $index = show($dir."/gb_addcomment", array("notification_page" => notification::get(), "entry" => $entry, "id" => $_GET['id'], "ed" => ""));
            }
        } else
            $index = error(_error_edit_post);
    break;
    case 'public':
        if(permission('gb')) {
            $get = $sql->fetch("SELECT `id`,`public` FROM `{prefix_gb}` WHERE `id` = ?;",array(intval($_GET['id'])));
            $sql->update("UPDATE `{prefix_gb}` SET `public` = ? WHERE `id` = ?;",array(($get['public'] ? 0 : 1),$get['id']));
            header("Location: ../gb/");
        } else {
            $index = error(_error_edit_post,1);
        }
    break;
    case 'delete':
        $get = $sql->fetch("SELECT `reg` FROM `{prefix_gb}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if($get['reg'] == userid() && checkme() != "unlogged" || permission('gb')) {
            $sql->delete("DELETE FROM `{prefix_gb}` WHERE `id` = ?;",array(intval($_GET['id'])));
            $sql->delete("DELETE FROM `{prefix_gbcomments}` WHERE `gbe` = ?;",array(intval($_GET['id'])));
            $index = info(_gb_delete_successful, "../gb/");
        } else
            $index = error(_error_edit_post);
    break;
    case 'cdelete':
        $get = $sql->fetch("SELECT `reg` FROM `{prefix_gbcomments}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if($get['reg'] == userid() && checkme() != "unlogged" || permission('gb')) {
            $sql->delete("DELETE FROM `{prefix_gbcomments}` WHERE `id` = ?;",array(intval($_GET['id'])));
            $index = info(_comment_deleted, "../gb/");
        } else
            $index = error(_error_edit_post);
    break;
    case 'cedit':
        $get = $sql->fetch("SELECT * FROM `{prefix_gbcomments}`  WHERE `id` = ?;",array(intval($_GET['id'])));
        if($get['reg'] == userid() && checkme() != "unlogged" || permission('gb')) {
            if($get['reg'] != 0) {
                $form = show("page/editor_regged", array("nick" => autor($get['reg'])));
            } else {
                $form = show("page/editor_notregged", array("postemail" => $get['email'], "posthp" => stringParser::decode($get['hp']), "postnick" => stringParser::decode($get['nick'])));
            }
            
            $where = $where.': '._gb_addcomment_edit;
            $index = show($dir."/edit_com", array("whaturl" => "editgbc&amp;id=".$get['id'],
                                                 "ed" => "&edit=".$get['id']."&postid=".$_GET['postid'],
                                                 "id" => $get['id'],
                                                 "form" => $form,
                                                 "posteintrag" => stringParser::decode($get['comment'])));
        } else
            $index = error(_error_edit_post);
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_gb}` WHERE `id` = ?;",array($_GET['id']));
        if($get['reg'] == userid() && checkme() != "unlogged" || permission('gb')) {
            if($get['reg'] != 0) {
                $form = show("page/editor_regged", array("nick" => autor($get['reg'])));
            } else {
                $form = show("page/editor_notregged", array("postemail" => stringParser::decode($get['email']), "posthp" => stringParser::decode($get['hp']), "postnick" => stringParser::decode($get['nick'])));
            }
            
            $where = $where.': '._gb_edit_head;
            $index = show($dir."/add", array("what" => _button_value_edit,
                                             "reg" => $get['reg'],
                                             "whaturl" => "action=admin&amp;do=editgb&amp;id=".$get['id'],
                                             "ed" => "&edit=".$get['id']."&id=".$_GET['postid'],
                                             "id" => $get['id'],
                                             "form" => $form,
                                             "eintraghead" => _gb_edit_head,
                                             "posteintrag" => stringParser::decode($get['nachricht']),
                                             "notification_page" => ""));
        } else
            $index = error(_error_edit_post);
    break;
    case 'editgb':
        if(intval($_POST['reg']) == userid() || permission('gb')) {
            $addme = ''; $params = array();
            if(!intval($_POST['reg'])) {
                $params = array(stringParser::encode($_POST['nick']),stringParser::encode($_POST['email']),stringParser::encode($_POST['hp']));
                $addme = "`nick` = ?, `email` = ?, `hp` = ?,";
            }

            $editedby = show(_edited_by, array("autor" => autor(), "time" => date("d.m.Y H:i", time())._uhr));
            array_merge($params,array(stringParser::encode($_POST['eintrag']),intval($_POST['reg']),stringParser::encode(addslashes($editedby)),intval($_GET['id'])));
            $sql->update("UPDATE `{prefix_gb}` SET ".$addme." `nachricht`  = ?, `reg` = ?, `editby` = ? WHERE `id` = ?;",$params);
            $index = info(_gb_edited, "../gb/");
        } else
            $index = error(_error_edit_post);
    break;
    case 'editgbc':
        $get = $sql->fetch("SELECT `reg` FROM `{prefix_gbcomments}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if($get['reg'] == userid() || permission('gb')) {
            $editedby = show(_edited_by, array("autor" => autor(), "time" => date("d.m.Y H:i", time())._uhr));
            $sql->update("UPDATE `{prefix_gbcomments}` SET `nick` = ?, `email` = ?, `hp` = ?, `comment` = ?, `editby` = ? WHERE `id` = ?;",
                array(stringParser::encode($_POST['nick']),stringParser::encode($_POST['email']),stringParser::encode($_POST['hp']),
                    stringParser::encode($_POST['eintrag']),stringParser::encode(addslashes($editedby)),intval($_GET['id'])));
            
            $index = info(_gb_comment_edited, "../gb/");
        } else
            $index = error(_error_edit_post);
    break;
}