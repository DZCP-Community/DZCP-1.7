<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Links')) exit();

$get = $sql->fetch("SELECT `url`,`id` FROM `{prefix_links}` WHERE `id` = ?;",array(intval($_GET['id'])));
if(count_clicks('link',$get['id']))
    $sql->update("UPDATE `{prefix_links}` SET `hits` = (hits+1) WHERE `id` = ?;",array($get['id']));

header("Location: ".stringParser::decode($get['url']));