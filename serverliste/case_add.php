<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_SList')) exit();

if(isset($_POST['clanname'])) {
    javascript::set('AnchorMove', 'notification-box');
    if(!$chkMe && !$securimage->check($_POST['secure']))
        notification::add_error(captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode);
    elseif(empty($_POST['clanname']))
        notification::add_error(_error_empty_clanname);
    elseif(empty($_POST['ip']))
        notification::add_error(_error_empty_ip);
    elseif(empty($_POST['port']))
        notification::add_error(_error_empty_port);
    elseif(empty($_POST['slots']))
        notification::add_error(_error_empty_slots);
    else {
        $sql->insert("INSERT INTO `{prefix_messages}` SET `datum` = ?, `von` = 0, `an` = 1,`titel` = ?, `nachricht` = ?;",
            array(time(),up(_slist_title),up(_slist_added_msg)));

        $url = !empty($_POST['clanurl']) && $_POST['clanurl'] != 'http://' ? links($_POST['clanurl']) : '-';
        $sql->insert("INSERT INTO `{prefix_serverliste}` SET `datum` = ?, `clanname` = ?, `clanurl` = ?, `ip` = ?, `port` = ?, `pwd` = ?, `slots` = ?;",
            array(time(),up($_POST['clanname']),up($url),up($_POST['ip']),intval($_POST['port']),up($_POST['pwd']),intval($_POST['slots'])));

        $index = info(_successful_server_saved, "../serverliste/");
    }
}

if(empty($index)) {
    $index = show($dir."/add", array("notification_page" => notification::get(),
                                     "clanurl" => !empty($_POST['clanurl']) ? $_POST['clanurl'] : '',
                                     "clanname" => !empty($_POST['clanname']) ? $_POST['clanname'] : '',
                                     "ip" => !empty($_POST['ip']) ? $_POST['ip'] : '',
                                     "port" => !empty($_POST['port']) ? $_POST['port'] : '',
                                     "pwd" => !empty($_POST['pwd']) ? $_POST['pwd'] : '',
                                     "slots" => !empty($_POST['slots']) ? $_POST['slots'] : ''));
}