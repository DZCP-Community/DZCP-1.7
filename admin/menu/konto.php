<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_clankasse_head;

switch ($do) {
    case 'update':
        if(settings::changed(($key='k_inhaber'),($var=stringParser::encode($_POST['inhaber'])))) settings::set($key,$var);
        if(settings::changed(($key='k_nr'),($var=stringParser::encode($_POST['kontonr'])))) settings::set($key,$var);
        if(settings::changed(($key='k_waehrung'),($var=stringParser::encode($_POST['waehrung'])))) settings::set($key,$var);
        if(settings::changed(($key='k_bank'),($var=stringParser::encode($_POST['bank'])))) settings::set($key,$var);
        if(settings::changed(($key='k_blz'),($var=stringParser::encode($_POST['blz'])))) settings::set($key,$var);
        if(settings::changed(($key='k_vwz'),($var=stringParser::encode($_POST['vwz'])))) settings::set($key,$var);
        if(settings::changed(($key='k_iban'),($var=stringParser::encode($_POST['iban'])))) settings::set($key,$var);
        if(settings::changed(($key='k_bic'),($var=stringParser::encode($_POST['bic'])))) settings::set($key,$var);
        settings::load(true);
        $show = info(_config_set, "?admin=konto");
    break;
    case 'new':
        $show = show($dir."/form_clankasse", array("newhead" => _clankasse_new_head,
                                                   "do" => "add",
                                                   "kat" => "",
                                                   "what" => _button_value_add,
                                                   "dlkat" => _description));
    break;
    case 'add':
        if(empty($_POST['kat'])) {
            $show = error(_clankasse_empty_kat, 1);
        } else {
            $sql->insert("INSERT INTO `{prefix_clankasse_kats}` SET `kat` = ?",array(stringParser::encode($_POST['kat'])));
            $show = info(_clankasse_kat_added, "?admin=konto");
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT `kat` FROM {prefix_clankasse_kats} WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = show($dir."/form_clankasse", array("newhead" => _clankasse_edit_head,
                                                   "do" => "editkat&amp;id=".$_GET['id']."",
                                                   "kat" => stringParser::decode($get['kat']),
                                                   "what" => _button_value_edit));
    break;
    case 'editkat':
        if(empty($_POST['kat'])) {
            $show = error(_clankasse_empty_kat, 1);
        } else {
            $sql->update("UPDATE `{prefix_clankasse_kats}` SET `kat` = ? WHERE `id` = ?;",array(stringParser::encode($_POST['kat']),intval($_GET['id'])));
            $show = info(_clankasse_kat_edited, "?admin=konto");
        }
    break;
    case 'update':
        $sql->delete("DELETE FROM `{prefix_clankasse_kats}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_clankasse_kat_deleted, "?admin=konto");
    break;
    default:
        $get = settings::get_array(array('k_inhaber','k_nr','k_blz','k_bank','k_iban','k_bic','k_waehrung','k_vwz'));
        $waehrung_list = str_replace("<option value=\"".stringParser::decode($get['k_waehrung'])."\">","<option value=\"".stringParser::decode($get['k_waehrung'])."\" selected=\"selected\">", _select_field_waehrung);
        
        $konto_show = show($dir."/form_konto", array("kinhaber" => _clankasse_inhaber,
                                                     "inhaber" => stringParser::decode($get['k_inhaber']),
                                                     "kkontonr" => _clankasse_nr,
                                                     "kontonr" => intval($get['k_nr']),
                                                     "kblz" => _clankasse_blz,
                                                     "kvwz" => _clankasse_vwz,
                                                     "head_waehrung" => _head_waehrung,
                                                     "waehrung" => $waehrung_list,
                                                     "blz" => intval($get['k_blz']),
                                                     "kbank" => _clankasse_bank,
                                                     "bank" => stringParser::decode($get['k_bank']),
                                                     "vwz" => stringParser::decode($get['k_vwz']),
                                                     "iban" => stringParser::decode($get['k_iban']),
                                                     "bic" => stringParser::decode($get['k_bic'])));

        $konto = show($dir."/form", array("head" => _config_konto_head,
                                          "what" => "konto",
                                          "top" => _config_c_clankasse,
                                          "value" => _button_value_save,
                                          "show" => $konto_show));

        $qryk = $sql->select("SELECT `id`,`kat` FROM {prefix_clankasse_kats};");
        foreach($qryk as $getk) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $edit = show("page/button_edit_single", array("id" => $getk['id'],
                                                          "action" => "admin=konto&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $getk['id'],
                                                              "action" => "admin=konto&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => _confirm_del_entry));

            $show .= show($dir."/clankasse_show", array("name" => stringParser::decode($getk['kat']),
                                                         "class" => $class,
                                                         "edit" => $edit,
                                                         "delete" => $delete));
        }

        $show = show($dir."/clankasse", array("show" => $show,"konto" => $konto));
    break;
}