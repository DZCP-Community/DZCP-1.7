<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START #
define('basePath', dirname(dirname(__FILE__).'../'));
ob_start();

## INCLUDES ##
require(basePath."/inc/debugger.php");
require(basePath."/inc/config.php");
require(basePath."/inc/bbcode.php");

##  * Bot Trap *
if(!$sql->rows("SELECT `id` FROM `{prefix_ipban}` WHERE `ip` = ? LIMIT 1;",array($userip))) {
    $data_array = array();
    $data_array['confidence'] = ''; $data_array['frequency'] = ''; $data_array['lastseen'] = '';
    $data_array['banned_msg'] = up('SpamBot detected by System * Bot Trap *');
    $sql->insert("INSERT INTO `{prefix_ipban}` SET `time` = ?, `ip` = ?, `data` = ?, `typ` = 3;",
            array(time(),$userip,serialize($data_array)));
    check_ip(); // IP Prufung * No IPV6 Support *
}
ob_end_flush();