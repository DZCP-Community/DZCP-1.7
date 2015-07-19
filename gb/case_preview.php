<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_GB')) exit();

$regCheck = false;
header("Content-type: application/x-www-form-urlencoded;charset=utf-8");
if(isset($_GET['view']) ? ($_GET['view'] == 'comment' ? true : false) : false) {
    if(isset($_GET['edit']) && !empty($_GET['edit'])) {
        $get = $sql->selectSingle("SELECT `reg`,`datum` FROM `{prefix_gbcomments}` WHERE `id` = ?;",array(intval($_GET['edit'])));
        $get_id = (isset($_GET['postid']) ? $_GET['postid'] : '?');
        $get_userid = $get['reg']; $get_date = $get['datum'];

        if(!$get['reg'])
            $regCheck = true;

        $editby = show(_edited_by, array("autor" => autor(), "time" => date("d.m.Y H:i", time())._uhr));
    } else {
        $get_id = cnt('{prefix_gb}')+1;
        $get_userid = $userid;
        $get_date = time();

        if($chkMe == 'unlogged')
            $regCheck = true;
    }

    if($regCheck) {
        $get_hp = $_POST['hp'];
        $get_email = $_POST['email'];
        $get_nick = show(_link_mailto, array("nick" => $_POST['nick'], "email" => eMailAddr($get_email)));
    } else
        $get_nick = autor();
} else {
    if(isset($_GET['edit']) && !empty($_GET['edit'])) {
        $get = $sql->selectSingle("SELECT `reg`,`datum` FROM `{prefix_gb}` WHERE `id` = ?;",array(intval($_GET['edit'])));
        $get_id = '?';
        $get_userid = $get['reg'];
        $get_date = $get['datum'];

        if(!$get['reg']) $regCheck = true;
            $editby = show(_edited_by, array("autor" => cleanautor($userid), "time" => date("d.m.Y H:i", time())._uhr));
        } else {
            $get_id = cnt('{prefix_gb}')+1;
            $get_userid = $userid;
            $get_date = time();
            if(!$chkMe) {
                $regCheck = true;
            }
        }

        $get_hp = $_POST['hp'];
        $get_email = $_POST['email'];
        $get_nick = $_POST['nick'];

        $gbhp = !empty($get_hp) ? show(_hpicon, array("hp" => links($get_hp))) : "";
        $gbemail = !empty($get_email) ? CryptMailto($get_email) : "";

    if($regCheck) {
        $gbtitel = show(_gb_titel_noreg, array("postid" => $get_id,
                                               "nick" => re($get_nick),
                                               "edit" => "",
                                               "delete" => "",
                                               "comment" => "",
                                               "public" => "",
                                               "email" => $gb_email,
                                               "datum" => date("d.m.Y",$get_date),
                                               "zeit" => date("H:i",$get_date),
                                               "hp" => $gbhp));
    } else {
        $gbtitel = show(_gb_titel, array("postid" => $get_id,
                                         "nick" => autor($get_userid),
                                         "edit" => "",
                                         "delete" => "",
                                         "comment" => "",
                                         "public" => "",
                                         "id" => $get_userid,
                                         "email" => $gb_email,
                                         "datum" => date("d.m.Y",$get_date),
                                         "zeit" => date("H:i",$get_date),
                                         "hp" => $gbhp));
    }
}

if(isset($_GET['view']) ? ($_GET['view'] == 'comment' ? true : false) : false)
    $index = str_replace("<br /><br />", "", show($dir."/commentlayout", array("nick" => $get_nick, "datum" => date("d.m.Y H:i", $get_date), "comment" => bbcode($_POST['eintrag']), "editby" => bbcode($editby,true), "edit" => '', "delete" => '')));
else
    $index = show($dir."/gb_show", array("gbtitel" => $gbtitel, "nachricht" => bbcode($_POST['eintrag']), "editby" => bbcode($editby,true), "ip" => $posted_ip, "comments" => ''));

update_user_status_preview();
exit(utf8_encode('<table class="mainContent" cellspacing="1">'.$index.'</table>'));