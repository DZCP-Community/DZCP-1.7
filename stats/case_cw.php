<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$wo_p = 0; $lo_p = 0; $dr_p = 0; $won = 0; $lost = 0; $draw = 0;
if(cnt("{prefix_clanwars}", " WHERE `datum` < ".time()) != 0) {
    $won = cnt("{prefix_clanwars}", " WHERE `punkte` > `gpunkte`");
    $lost = cnt("{prefix_clanwars}", " WHERE `punkte` < `gpunkte`");
    $draw = cnt("{prefix_clanwars}", " WHERE `datum` < ".time()." && `punkte` = `gpunkte`");
    $ges = cnt("{prefix_clanwars}", " WHERE `datum` < ".time());

    $wo_p = @round($won*100/$ges, 1);
    $lo_p = @round($lost*100/$ges, 1);
    $dr_p = @round($draw*100/$ges, 1);
}

$allp = '<span class="CwWon">'.sum("{prefix_clanwars}",'',"punkte").'</span>'.' : '.
        '<span class="CwLost">'.sum("{prefix_clanwars}",'',"gpunkte").'</span>';

$stats = show($dir."/cw", array("nplayed" => $ges,
                                "nwon" => $won." (".$wo_p."%)",
                                "ndraw" => $draw." (".$dr_p."%)",
                                "nlost" => $lost." (".$lo_p."%)",
                                "npoints" => $allp));