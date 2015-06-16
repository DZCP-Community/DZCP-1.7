<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Shout')) exit();

if(!permission("shoutbox"))
    $index = error(_error_wrong_permissions, 1);
else {
    if($do == "delete") {
        $sql->delete("DELETE FROM `{prefix_shoutbox}` WHERE `id` = ?;",array(intval($_GET['id'])));
        header("Location: ".GetServerVars('HTTP_REFERER').'#shoutbox');
    }
}