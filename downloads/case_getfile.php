<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Downloads')) exit();

if(settings("reg_dl") && !$chkMe)
    $index = error(_error_unregistered,1);
else {
    $get = $sql->selectSingle("SELECT `url`,`id` FROM `{prefix_downloads}` WHERE `id` = ?;",array(intval($_GET['id'])));
    $file = preg_replace("#added...#Uis", "", re($get['url']));
    if(preg_match("=added...=Uis",re($get['url'])) != FALSE)
        $dlFile = "files/".$file;
    else
        $dlFile = re($get['url']);

    if(count_clicks('download',$get['id']))
        $sql->update("UPDATE `{prefix_downloads}` SET `hits` = (hits+1), `last_dl` = ? WHERE `id` = ?;",array(time(),$get['id']));

    ## download file ##
    header("Location: ".$dlFile);
}