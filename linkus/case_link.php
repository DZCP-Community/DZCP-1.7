<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_LinkUS')) exit();

$get = $sql->fetch("SELECT `url` FROM `{prefix_linkus}` WHERE `id` = ?;",array(intval($_GET['id'])));
header("Location: ".re($get['url']));