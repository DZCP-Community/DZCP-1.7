<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Clanwars')) {
    if(permission("clanwars")) {
        $sql->delete("DELETE FROM `{prefix_clanwar_players}` WHERE `cwid` = ?;",array(intval($_GET['id'])));
        $index = info(_cw_players_reset, '?action=details&id='.intval($_GET['id']));
    }
}