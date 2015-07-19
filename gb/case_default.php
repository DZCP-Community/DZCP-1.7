<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

$error = '';
if($do == 'addgb') {
    if(userid() != 0)
        $toCheck = empty($_POST['eintrag']);
    else
        $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['eintrag']) || !check_email($_POST['email']) || !$securimage->check($_POST['secure']);

    if($toCheck) {
        if(userid() != 0) {
            if(empty($_POST['eintrag']))
                $error = _empty_eintrag;

            $form = show("page/editor_regged", array("nick" => autor()));
        } else {
            if (!$securimage->check($_POST['secure']))
                $error = captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode;
            elseif(empty($_POST['nick']))
                $error = _empty_nick;
            elseif(empty($_POST['email']))
                $error = _empty_email;
            elseif(!check_email($_POST['email']))
                $error = _error_invalid_email;
            elseif(empty($_POST['eintrag']))
                $error = _empty_eintrag;

            $form = show("page/editor_notregged", array("postemail" => $_POST['email'], "posthp" => $_POST['hp'], "postnick" => $_POST['nick']));
        }

        $error = show("errors/errortable", array("error" => $error));
    } else {
        $sql->insert("INSERT INTO `{prefix_gb}` SET
					 `datum`      = '".time()."',
					 `editby`     = '',
					 `public`     = 0,
					 `nick`       = '".(isset($_POST['nick']) ? string::encode($_POST['nick']) : '')."',
					 `email`      = '".(isset($_POST['email']) ? string::encode($_POST['email']) : '')."',
					 `hp`         = '".(isset($_POST['hp']) ? string::encode($_POST['hp']) : '')."',
					 `reg`        = '".userid()."',
					 `nachricht`  = '".string::encode($_POST['eintrag'])."',
					 `ip`         = '".visitorIp()."'");

        wire_ipcheck('gb');
        $index = info(_gb_entry_successful, "?index=gb");
    }
}

if(empty($index)) {
    $activ = (!permission("gb") && settings('gb_activ')) ? " WHERE public = 1" : "";
    $qry = $sql->select("SELECT * FROM `{prefix_gb}`".$activ." ORDER BY datum DESC LIMIT ".($page - 1)*config('m_gb').",".config('m_gb').";");
    $entrys = cnt($db['gb']); $i = $entrys-($page - 1)*config('m_gb');
    if($sql->rowCount()) {
        $show = '';
        foreach($qry as $get) {
            $gbhp = !empty($get['hp']) ? show(_hpicon, array("hp" => links(re($get['hp'])))) : "";
            $gbemail = !empty($get['email']) ? CryptMailto(re($get['email'])) : "";

            $delete = ""; $edit = ""; $comment = "";
            if( ($get['reg'] == $userid && $userid >= 1) || permission("gb")) {
                $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                              "action" => "action=do&amp;what=edit",
                                                              "title" => _button_title_edit));
                $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                                  "action" => "action=do&amp;what=delete",
                                                                  "title" => _button_title_del,
                                                                  "del" => convSpace(_confirm_del_entry)));

                $comment = show(_gb_commenticon, array("id" => $get['id'], "title" => _button_title_comment));
            }

            $public = "";
            if(permission("gb") && settings('gb_activ')) {
                $public = $get['public'] ? 
                  '<a href="?action=do&amp;what=unset&amp;id='.$get['id'].'"><img src="../inc/images/public.gif" alt="" '
                        . 'title="nicht ver&ouml;ffentlichen" align="top" style="padding-top:1px"/></a>'
                : '<a href="?action=do&amp;what=set&amp;id='.$get['id'].'"><img src="../inc/images/nonpublic.gif" alt="" '
                        . 'title="ver&ouml;ffentlichen" align="top" style="padding-top:1px"/></a>';
            }

            if(!$get['reg']) {
                $gbtitel = show(_gb_titel_noreg, array("postid" => $i,
                                                       "nick" => re($get['nick']),
                                                       "edit" => $edit,
                                                       "delete" => $delete,
                                                       "comment" => $comment,
                                                       "public" => $public,
                                                       "email" => $gbemail,
                                                       "datum" => date("d.m.Y", $get['datum']),
                                                       "zeit" => date("H:i", $get['datum']),
                                                       "hp" => $gbhp));
            } else {
                $gbtitel = show(_gb_titel, array("postid" => $i,
                                                 "nick" => autor($get['reg']),
                                                 "edit" => $edit,
                                                 "delete" => $delete,
                                                 "comment" => $comment,
                                                 "public" => $public,
                                                 "id" => $get['reg'],
                                                 "email" => $gbemail,
                                                 "datum" => date("d.m.Y", $get['datum']),
                                                 "zeit" => date("H:i", $get['datum']),
                                                 "hp" => $gbhp));
            }

            $qryc = $sql->select("SELECT * FROM `{prefix_gbcomments}` WHERE `gbe` = ? ORDER BY datum DESC;",array($get['id'])); 
            $comments = '';
            if($sql->rowCount()) {
                foreach($qryc as $getc) {
                    $edit = ""; $delete = "";
                    if((checkme() != 'unlogged' && $getc['reg'] == userid()) || permission("gb")) {
                        $edit = show("page/button_edit_single", array("id" => $getc['id'],
                                                                      "action" => "action=admin&amp;do=cedit&amp;postid=".$i,
                                                                      "title" => _button_title_edit));
                        $delete = show("page/button_delete_single", array("id" => $getc['id'],
                                                                          "action" => "action=admin&amp;do=cdelete",
                                                                          "title" => _button_title_del,
                                                                          "del" => convSpace(_confirm_del_entry)));
                    }

                    $nick = (!$getc['reg'] ? CryptMailto(re($getc['email']),_link_mailto,array('nick' => re($getc['nick']))) : autor($getc['reg']));
                    $comments .= show($dir."/commentlayout", array("nick" => re($nick), 
                                                                   "editby" => bbcode(re($getc['editby'])), 
                                                                   "datum" => date("d.m.Y H:i", $getc['datum'])._uhr, 
                                                                   "comment" => bbcode(re($getc['comment'])), 
                                                                   "edit" => $edit, 
                                                                   "delete" => $delete));
                }
            }

            $posted_ip = ($chkMe == 4 || permission('ipban') ? $get['ip'] : _logged);
            $show .= show($dir."/gb_show", array("gbtitel" => $gbtitel, 
                                                 "nachricht" => bbcode(re($get['nachricht'])), 
                                                 "comments" => $comments, 
                                                 "editby" => bbcode(re($get['editby'])), 
                                                 "ip" => $posted_ip));
            $i--;
        }
    } else
        $show = show(_no_entrys_yet, array("colspan" => "2"));

    $entry = "";
    if(!ipcheck("gb", config('f_gb'))) {
        if($userid != 0) {
            $form = show("page/editor_regged", array("nick" => autor()));
        } else {
            $form = show("page/editor_notregged", array("postemail" => (isset($_POST['email']) ? $_POST['email'] : ''), 
                                                        "posthp" => (isset($_POST['hp']) ? $_POST['hp'] : ''), 
                                                        "postnick" => (isset($_POST['nick']) ? $_POST['nick'] : '')));
        }

        $entry = show($dir."/add", array("eintraghead" => _gb_add_head, 
                                         "what" => _button_value_add, 
                                         "ed" => "", 
                                         "reg" => "", 
                                         "whaturl" => 
                                         "do=addgb", 
                                         "form" => $form, 
                                         "posteintrag" => (isset($_POST["eintrag"]) ? $_POST["eintrag"] : ''), 
                                         "error" => $error));
    }

    $seiten = nav($entrys,config('m_gb'),"?index=gb&amp;action=nav");
    $index = show($dir."/gb",array("show" => $show, 
                                   "add" => show(_gb_eintragen), 
                                   "entry" => $entry, 
                                   "seiten" => $seiten));
}