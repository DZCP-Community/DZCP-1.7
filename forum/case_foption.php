<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
    if($do == "fabo") {
        if(isset($_POST['f_abo'])) {
            $sql->insert("INSERT INTO `{prefix_f_abo}` SET `user` = ?, `fid` = ?, `datum` = ?",array(intval($userid),intval($_GET['id']),time()));
        } else {
            $sql->delete("DELETE FROM `{prefix_f_abo}` WHERE `user` = ? AND `fid` = ?",array(intval($userid),intval($_GET['id'])));
        }
        
        $index = info(_forum_fabo_do, "?action=showthread&amp;id=".$_GET['id']."");
    }
}