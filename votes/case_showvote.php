<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Votes')) {
    $show = '';
    $get = $sql->fetch("SELECT `intern`,`id` FROM `{prefix_votes}` WHERE `id` = ?;",array(intval($_GET['id'])));
    if(!$get['intern'] || $chkMe >= 1) {
        $qryv = $sql->select("SELECT `user_id`,`time` FROM `{prefix_ipcheck}` WHERE `what` = 'vid_".$get['id']."' ORDER BY `time` DESC;");
        foreach($qryv as $getv) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/voted_show", array("user" => autor($getv['user_id']),
                                                    "date" => date("d.m.y H:i",$getv['time'])._uhr,
                                                    "class" => $class));
        }

        if(empty($show))
            $show = show(_no_entrys_yet, array("colspan" => "2"));

        $index = show($dir."/voted", array("show" => $show));
    }
    else
        $index = error(_error_vote_show,1);
}