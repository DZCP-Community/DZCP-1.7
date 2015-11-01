<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

$where = $where.': '._config_impressum_head;

if($do == "update") {
    $sql->update("UPDATE `{prefix_settings}` SET `i_autor` = ?, `i_domain` = ? WHERE `id` = 1;",array(up($_POST['seitenautor']),up($_POST['domain'])));
    $show = info(_config_set, "?admin=impressum");
} else {
    $show = show($dir."/form_impressum", array("domain" => re(settings::get('i_domain')),
                                               "bbcode" => bbcode("seitenautor"),
                                               "postautor" => re(settings::get('i_autor'))));

    $show = show($dir."/imp", array("what" => "impressum", "value" => _button_value_edit, "show" => $show));
}