<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_logout;
    if($chkMe && $userid) {
        $sql->update("UPDATE `{prefix_users}` SET `online` = 0, `sessid` = '' WHERE `id` = ?;",array($userid));
        $sql->delete("DELETE FROM `{prefix_autologin}` WHERE `ssid` = ?;",array(session_id()));
        setIpcheck("logout(".$userid.")");
        dzcp_session_destroy();
    }

    header("Location: ../news/");
}