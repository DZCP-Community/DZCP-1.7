<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Votes')) {
    $get = $sql->fetch("SELECT `id`,`intern`,`closed` FROM `{prefix_votes}` WHERE `id` = ?;",array(intval($_GET['id'])));
    if(!$get['intern'] || ($get['intern'] && $chkMe)) {
        $qryv = $sql->select("SELECT `user_id`,`time`,`created` FROM `{prefix_ipcheck}` WHERE `what` = 'vid_".$get['id']."' ORDER BY `time` DESC;");
        if($chkMe == 4 || $get['closed'] || permission('votesadmin') || $sql->rows("SELECT `id` FROM `{prefix_ipcheck}` "
                . "WHERE `user_id` = ? AND `what` = ?;",array($userid,'vid_'.$get['id']))) {
            foreach ($qryv as $getv) {
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                $show .= show($dir."/voted_show", array("user" => $getv['user_id'] ? autor($getv['user_id']) : _gast,
                                                        "date" => date("d.m.y H:i",$getv['created'])._uhr,
                                                        "class" => $class));
            }
        }

        if(empty($show))
            $show = show(_no_entrys_yet, array("colspan" => "2"));

        $index = show($dir."/voted", array("show" => $show));
    } else
        $index = error(_error_vote_show,1);
}