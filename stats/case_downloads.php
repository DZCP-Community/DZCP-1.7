<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$qry = $sql->select("SELECT `url`,`download`,`hits` FROM `{prefix_downloads}` ORDER BY `id` DESC;");
$allhits = 0; $allsize = 0;
foreach($qry as $get) {
    $file = preg_replace("#added...#Uis", "../downloads/files/", $get['url']);
    $size = 0;
    
    if(file_exists($file))
      $size = filesize($file);

    $hits = $get['hits'];
    $allhits += $hits;
    $allsize += $size;
}

if(strlen(@round(($allsize/1048576)*$allhits,0)) >= 4)
    $alltraffic = @round(($allsize/1073741824)*$allhits,2).' GB';
else
    $alltraffic = @round(($allsize/1048576)*$allhits,2).' MB';

if(strlen(@round(($allsize/1048576),0)) >= 4)
    $allsize = @round(($allsize/1073741824),2).' GB';
else
    $allsize = @round(($allsize/1048576),2).' MB';

$stats = show($dir."/downloads", array("nfiles" => cnt("{prefix_downloads}"),
                                       "allsize" => $allsize,
                                       "ntraffic" => $alltraffic,
                                       "nhits" => $allhits));