<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$allcomments = cnt("{prefix_newscomments}");
$allnews = cnt("{prefix_news}");
$allkats = cnt("{prefix_newskat}");

$qry = $sql->select("SELECT `kategorie` FROM `{prefix_newskat}` ORDER BY `id` ASC;");
$kats = '';
foreach($qry as $get) {
    $kats .= re($get['kategorie']).", ";
}
$kats = substr($kats, 0, -2);

$get = $sql->fetch("SELECT `datum` FROM `{prefix_news}` ORDER BY `datum` ASC;");
$time = (time()-$get['datum']);
$days = @round($time/86400);
$cpern = @round($allcomments/$allnews,2);
$npert = @round($allnews/$days,2);

$stats = show($dir."/news", array("nkats" => $kats,
                                  "nnpert" => $npert,
                                  "ncpern" => $cpern,
                                  "ncomments" => $allcomments,
                                  "nnews" => $allnews,
                                  "cnt" => $allkats));