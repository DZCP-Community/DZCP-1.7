<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$ges = cnt("{prefix_awards}");
$place_1 = cnt("{prefix_awards}", " WHERE `place` = 1 ");
$place_2 = cnt("{prefix_awards}", " WHERE `place` = 2 ");
$place_3 = cnt("{prefix_awards}", " WHERE `place` = 3 ");

$stats = show($dir."/awards", array("nawards" => $ges,
                                    "np1" => $place_1,
                                    "np2" => $place_2,
                                    "np3" => $place_3,
                                    "np" => $ges-$place_1-$place_2-$place_3));