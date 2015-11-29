<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$allthreads = cnt('{prefix_forumthreads}');
$allposts = cnt('{prefix_forumposts}');
$pperd = 0; $ppert = 0; $topposter = '-';
if($allthreads > 0 && $allposts >= 0) {
    $ppert = round($allposts/$allthreads,2);
    $get = $sql->fetch("SELECT `id`,`forumposts` FROM `{prefix_userstats}` ORDER BY `forumposts` DESC;");
    $topposter = autor($get['id'])." (".$get['forumposts']." Posts)";

    $get = $sql->fetch("SELECT `t_date` FROM `{prefix_forumthreads}` ORDER BY `t_date` ASC;");
    $time = time()-$get['t_date'];
    $days = @round($time/86400);

    $ges = ($allposts+$allthreads);
    $pperd = @round($ges/$days,2);
}

$stats = show($dir."/forum", array("nthreads" => $allthreads,
                                   "nposts" => $allposts,
                                   "nppert" => $ppert,
                                   "npperd" => $pperd,
                                   "ntopposter" => $topposter));