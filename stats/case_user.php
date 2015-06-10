<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$stats = show($dir."/user", array("nmember" => cnt("{prefix_users}", " WHERE `level` != 1"),
                                  "nlogins" => sum("{prefix_userstats}","", "logins"),
                                  "nmsg" => sum("{prefix_userstats}","", "writtenmsg"),
                                  "nvotes" => sum("{prefix_userstats}","","votes"),
                                  "naktmsg" => cnt("{prefix_messages}", " WHERE `von` != 0"),
                                  "nbuddys" => cnt("{prefix_userbuddys}"),
                                  "nusers" => cnt("{prefix_users}")));