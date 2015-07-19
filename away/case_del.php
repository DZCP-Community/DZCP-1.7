<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Away')) {
    if(!$chkMe || $chkMe < 2) {
        $index = error(_error_wrong_permissions, 1);
    } else {
        $sql->delete("DELETE FROM `{prefix_away}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $index = info(_away_successful_del, "../away/");
    }
}