<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Artikel')) {
    if($do == 'edit') {
        $get = $sql->fetch("SELECT * FROM `{prefix_acomments}` WHERE `id` = ?;",array(intval($_GET['cid'])));

        $get_id = '?';
        $get_userid = $get['reg'];
        $get_date = $get['datum'];
        $regCheck = false;

        if($get['reg']) {
            $regCheck = true;
            $pUId = $get['reg'];
        }

        $editedby = show(_edited_by, array("autor" => cleanautor($userid),
                                           "time" => date("d.m.Y H:i", time())._uhr));
    } else {
        $get_id = cnt("{prefix_acomments}", " WHERE `artikel` = ?;","id",array(intval($_GET['id'])))+1;
        $get_userid = $userid;
        $get_date = time();
        $regCheck = false;
        $editedby = '';

        if(!$chkMe) {
            $regCheck = true;
            $pUId = $userid;
        }
    }

    if($regCheck) {
        $get_hp = isset($_POST['hp']) ? $_POST['hp'] : '';
        $get_email = isset($_POST['email']) ? $_POST['email'] : '';
        $get_nick = isset($_POST['nick']) ? $_POST['nick'] : '';

        $hp = $get_hp ? show(_hpicon_forum, array("hp" => links($get_hp))) : "";
        $email = $get_email ? '<br />'.CryptMailto($get_email,_emailicon_forum) : "";
        $onoff = "";
        $avatar = "";
        $nick = show(_link_mailto, array("nick" => stringParser::decode($get_nick),
                                         "email" => $get_email));
    } else {
        $hp = "";
        $email = "";
        $onoff = onlinecheck($get_userid);
        $nick = cleanautor($get_userid);
    }

    $titel = show(_eintrag_titel, array("postid" => $get_id,
                                        "datum" => date("d.m.Y", $get_date),
                                        "zeit" => date("H:i", $get_date)._uhr,
                                        "edit" => '',
                                        "delete" => ''));

    $index = show("page/comments_show", array("titel" => $titel,
                                              "comment" => bbcode::parse_html($_POST['comment'],true),
                                              "nick" => $nick,
                                              "editby" => bbcode::parse_html($editedby,true),
                                              "email" => $email,
                                              "hp" => $hp,
                                              "avatar" => useravatar($get_userid),
                                              "onoff" => $onoff,
                                              "rank" => getrank($get_userid),
                                              "ip" => $userip._only_for_admins));

    update_user_status_preview();
    header('Content-Type: text/html; charset=utf-8');
    exit(utf8_encode('<table class="mainContent" cellspacing="1">'.$index.'</table>'));
}