<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    if($userid) {
        $_SESSION['lastvisit'] = time();
        $sql->update("UPDATE `{prefix_userstats}` SET `lastvisit` = ? WHERE `user` = ?;",
                array(intval($_SESSION['lastvisit']),intval($userid)));
    }

    header("Location: ?action=userlobby");
}