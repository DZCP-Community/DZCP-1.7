<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Contact')) exit();

$squads = show(_select_field_fightus, array("id" => "0", "squad" => _contact_joinus_no_squad_aviable, "game" => "?"));
$qry = $sql->select("SELECT `id`,`name`,`game` FROM `{prefix_squads}` WHERE `status` = 1 AND `team_fightus` = 1 ORDER BY `name`;");
$squads = '';
foreach($qry as $get) {
    $squads .= show(_select_field_fightus, array("id" => $get['id'], "squad" => stringParser::decode($get['name']), "game" => stringParser::decode($get['game'])));
}

$dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",time())),
                                            "month" => dropdown("month",date("m",time())),
                                            "year" => dropdown("year",date("Y",time()))));

$dropdown_time = show(_dropdown_time, array("hour" => dropdown("hour",date("H",time())),
                                            "minute" => dropdown("minute",date("i",time())),
                                            "uhr" => _uhr));

$index = show($dir."/fightus", array("datum" => $dropdown_date,
                                     "squads" => $squads,
                                     "zeit" => $dropdown_time,
                                     "year" => date("Y", time())));