<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    header("Content-type: text/html; charset=utf-8");
    if($do == 'edit') {
        $get = $sql->fetch("SELECT `reg`,`datum` "
                                . "FROM `{prefix_usergb}` "
                                . "WHERE `id` = ?;",array(intval($_GET['gbid'])));
        $get_id = '?';
        $get_userid = $get['reg'];
        $get_date = $get['datum'];
        $regCheck = !$get['reg'] ? true : false;
        $editby = show(_edited_by, array("autor" => cleanautor($userid),
                                         "time" => date("d.m.Y H:i", time())._uhr));
    } else {
        $get_id = cnt('{prefix_usergb}', "WHERE `user` = ?","id",array(intval($_GET['uid'])))+1;
        $get_userid = $userid;
        $get_date = time();
        $regCheck = !$chkMe ? true : false;
    }

    if($regCheck) {
        $get_hp = $_POST['hp'];
        $get_email = $_POST['email'];
        $get_nick = $_POST['nick'];

        $onoff = ""; $avatar = "";
        $nick = CryptMailto($get_email,_link_mailto,array("nick" => re($get_nick)));
    } else {
        $get_hp = data('hp');
        $email = data('email');
        $onoff = onlinecheck($userid);
        $get_nick = autor($userid);
    }

    $gbhp =  $get_hp ? show(_hpicon, array("hp" => links($get_hp))) : "";
    $gbemail = $get_email ? CryptMailto($get_email,_emailicon) : "";
    $titel = show(_eintrag_titel, array("postid" => $get_id,
                                        "datum" => date("d.m.Y", time()),
                                        "zeit" => date("H:i", time())._uhr,
                                        "edit" => $edit,
                                        "delete" => $delete));

    $posted_ip = $chkMe == 4 || permission('ipban') ? visitorIp() : _logged;
    $index .= show("page/comments_show", array("titel" => $titel,
                                             "comment" => bbcode(re($_POST['eintrag']),1),
                                             "nick" => $get_nick,
                                             "hp" => $gbhp,
                                             "editby" => $editby,
                                             "email" => $gbemail,
                                             "avatar" => useravatar(),
                                             "onoff" => $onoff,
                                             "rank" => getrank($userid),
                                             "ip" => $posted_ip));

    update_user_status_preview();
    exit(utf8_encode('<table class="mainContent" cellspacing="1">'.$index.'</table>'));
}
