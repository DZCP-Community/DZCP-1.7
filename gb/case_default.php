<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_GB')) exit();
$error = ''; $notification_p = '';
if($do == 'addgb') {
    if(userid() != 0)
        $toCheck = empty($_POST['eintrag']);
    else
        $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['eintrag']) || !check_email($_POST['email']) || !$securimage->check($_POST['secure']);

    if($toCheck) {
        notification::set_global(true);
        javascript::set('AnchorMove', 'gbForm');
        if(userid() != 0) {
            if (empty($_POST['eintrag'])) {
                notification::add_error(_empty_eintrag);
            }

            $form = show("page/editor_regged", array("nick" => autor($userid)));
        } else {
            if (!$securimage->check($_POST['secure']))
                notification::add_error(captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode);
            elseif(empty($_POST['nick']))
                notification::add_error(_empty_nick);
            elseif(empty($_POST['email']))
                 notification::add_error(_empty_email);
            elseif(!check_email($_POST['email']))
                 notification::add_error(_error_invalid_email);
            elseif(empty($_POST['eintrag']))
                notification::add_error(_empty_eintrag);

            $form = show("page/editor_notregged", array("posthp" => (isset($_POST['hp']) ? $_POST['hp'] : ''),
                                                        "postemail" => (isset($_POST['email']) ? $_POST['email'] : ''),
                                                        "postnick" => (isset($_POST['nick']) ? $_POST['nick'] : '')));
        }
    } else {
        $sql->insert("INSERT INTO `{prefix_gb}` SET `datum` = ?, `editby` = '', `public` = 0, `nick` = ?, `email` = ?, `hp` = ?, `reg` = ?, `nachricht` = ?, `ip` = ?;",
                array(time(),(isset($_POST['nick']) ? stringParser::encode($_POST['nick']) : ''),(isset($_POST['email']) ? stringParser::encode($_POST['email']) : ''),
                (isset($_POST['hp']) ? stringParser::encode($_POST['hp']) : ''),userid(),stringParser::encode($_POST['eintrag']),visitorIp()));

        setIpcheck("gb");
        $index = info(_gb_entry_successful, "../gb/");
    }
}

if(empty($index)) {
    $activ = (!permission("gb") && settings::get('gb_activ')) ? " WHERE `public` = 1" : "";
    $qry = $sql->select("SELECT * FROM `{prefix_gb}`".$activ." ORDER BY `datum` DESC LIMIT ".($page - 1)*settings::get('m_gb').",".settings::get('m_gb').";");
    $entrys = cnt("{prefix_gb}"); $i = $entrys-($page - 1)*settings::get('m_gb');
    if($sql->rowCount()) { $show = '';
        foreach($qry as $get) {
            $gbhp = !empty($get['hp']) ? show(_hpicon, array("hp" => links(stringParser::decode($get['hp'])))) : "";
            $gbemail = !empty($get['email']) ? CryptMailto(stringParser::decode($get['email'])) : "";
            $delete = ""; $edit = ""; $comment = "";
            if((checkme() != 'unlogged' && $get['reg'] == userid()) || permission("gb")) {
                $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                              "action" => "action=admin&amp;do=edit&amp;postid=".$i,
                                                              "title" => _button_title_edit));
                
                $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                                  "action" => "action=admin&amp;do=delete",
                                                                  "title" => _button_title_del,
                                                                  "del" => _confirm_del_entry));

                $comment = show(_gb_commenticon, array("id" => $get['id'], "title" => _button_title_comment));
            }

            $public = "";
            if(permission("gb") && settings::get('gb_activ')) {
                $public = ($get['public'])
                ? '<a href="?action=admin&amp;do=public&amp;id='.$get['id'].'"><img src="../inc/images/public.gif" alt="" title="nicht ver&ouml;ffentlichen" align="top" style="padding-top:1px"/></a>'
                : '<a href="?action=admin&amp;do=public&amp;id='.$get['id'].'"><img src="../inc/images/nonpublic.gif" alt="" title="ver&ouml;ffentlichen" align="top" style="padding-top:1px"/></a>';
            }

            if(!$get['reg']) {
                $gbtitel = show(_gb_titel_noreg, array("postid" => $i,
                                                       "nick" => stringParser::decode($get['nick']),
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

            $qryc = $sql->select("SELECT * FROM `{prefix_gbcomments}` WHERE `gbe` = ? ORDER BY `datum` DESC;",array($get['id'])); 
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
                                                                          "del" => _confirm_del_entry));
                    }

                    $nick = (!$getc['reg'] ? CryptMailto(stringParser::decode($getc['email']),_link_mailto,array('nick' => stringParser::decode($getc['nick']))) : autor($getc['reg']));
                    $comments .= show($dir."/commentlayout", array("nick" => stringParser::decode($nick), 
                                                                   "editby" => bbcode::parse_html($getc['editby']), 
                                                                   "datum" => date("d.m.Y H:i", $getc['datum'])._uhr, 
                                                                   "comment" => bbcode::parse_html($getc['comment']), 
                                                                   "edit" => $edit, 
                                                                   "delete" => $delete));
                }
            }

            $posted_ip = ($chkMe == 4 || permission('ipban') ? $get['ip'] : _logged);
            $show .= show($dir."/gb_show", array("gbtitel" => $gbtitel, 
                                                 "nachricht" => bbcode::parse_html($get['nachricht']), 
                                                 "comments" => $comments, 
                                                 "editby" => bbcode::parse_html($get['editby']), 
                                                 "ip" => $posted_ip));
            $i--;
        }
    } else
        $show = show(_no_entrys_yet, array("colspan" => "2"));

    $entry = "";
    if(!ipcheck("gb", settings::get('f_gb'))) {
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
                                         "whaturl" => "do=addgb", 
                                         "form" => $form,
                                         "posteintrag" => (isset($_POST["eintrag"]) ? $_POST["eintrag"] : ''), 
                                         "notification_page" => notification::get($notification_p)));
    }

    $seiten = nav($entrys,settings::get('m_gb'),"?&amp;action=nav");
    $index = show($dir."/gb",array("show" => $show, 
                                   "add" => show(_gb_eintragen), 
                                   "entry" => $entry, 
                                   "seiten" => $seiten));
}