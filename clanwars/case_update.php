<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Clanwars')) {
    if(!$chkMe) {
        $index = error(_error_have_to_be_logged, 1);
    } else {
        if($sql->rows("SELECT `id` FROM `{prefix_clanwar_players}` WHERE `cwid` = ? AND `member` = ?;",array(intval($_GET['id']),intval($userid)))) {
            $sql->update("UPDATE `{prefix_clanwar_players}` SET `status` = ? WHERE cwid = ? AND member = ?;",array(intval($_POST['status']),intval($_GET['id']),$userid));
        } else {
            $sql->insert("INSERT INTO `{prefix_clanwar_players}` SET `cwid` = ?, `member` = ?, `status` = ?;",array(intval($_GET['id']),intval($userid)));
        }

        $index = info(_cw_status_set, "?action=details&amp;id=".$_GET['id']."");
    }
}
