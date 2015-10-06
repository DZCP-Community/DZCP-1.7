<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

$where = $where.': '._clear_head;
if(isset($_POST['submit'])) {
    if(empty($_POST['days'])) {
        $show = error(_clear_error_days,1);
    } else {
        $deleted = 0;
        $time = time()-($_POST['days']*24*60*60);
        if(isset($_POST['news'])) {
            $sql->delete("DELETE FROM `{prefix_news}` WHERE `datum` <= ".intval($time).";");
            $deleted = ($deleted+$sql->rowCount());
            $sql->delete("DELETE FROM `{prefix_newscomments}` WHERE `datum` <= ".intval($time).";");
            $deleted = ($deleted+$sql->rowCount());
        }

        if(isset($_POST['away'])) {
            $sql->delete("DELETE FROM `{prefix_away}` WHERE `date` <= ".intval($time).";");
            $deleted = ($deleted+$sql->rowCount());
        }

        if(isset($_POST['forum'])) {
            $qry = $sql->select("SELECT id FROM `{prefix_forumthreads}` WHERE `t_date` <= '".intval($time)."' AND sticky != 1;");
            foreach($qry as $get) {
                $sql->delete("DELETE FROM `{prefix_forumthreads}` WHERE `id` = ".intval($get['id']).";");
                $deleted = ($deleted+$sql->rowCount());
                $sql->delete("DELETE FROM `{prefix_forumposts}` WHERE `sid` = ".intval($get['id']).";");
                $deleted = ($deleted+$sql->rowCount());
            }
        }

        $show = info(show(_clear_deleted,array('deleted'=>$deleted)), "../admin/");
    }
} else {
    $show = show($dir."/clear", array("c_days" => (isset($_POST['days']) ? $_POST['days'] : 365)));
}