<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Contact')) exit();

$qrysquads = $sql->select("SELECT `id`,`name`,`game` "
                        . "FROM `{prefix_squads}` "
                        . "WHERE `status` = 1 AND `team_joinus` = 1 "
                        . "ORDER BY `name`;");

$squads = '';
foreach($qrysquads as $getsquads) {
    $squads .= show(_select_field_fightus, array("id" => $getsquads['id'],
                                                 "squad" => re($getsquads['name']),
                                                 "game" => re($getsquads['game'])));
}

if (!$sql->rowCount()) {
    $squads = show(_select_field_fightus, array("id" => "0",
                                         "squad" => _contact_joinus_no_squad_aviable,
                                         "game" => "?"));
}

$joinus = show($dir."/joinus", array("squads" => $squads));
$index = show($dir."/contact", array("joinus" => $joinus, "what" => "joinus"));