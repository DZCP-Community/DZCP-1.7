<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Ck')) {
    if(!$chkMe || $chkMe < 2)
        $index = error(_error_wrong_permissions, 1);
    else {
        $get_settings = settings::get(array('k_inhaber','k_nr','k_blz','k_bank','k_iban','k_bic','k_waehrung','k_vwz'));
        $entrys = cnt("{prefix_clankasse}");
        $qry = $sql->select("SELECT `id`,`pm`,`betrag`,`member`,`transaktion`,`datum` FROM `{prefix_clankasse}`
                  ".orderby_sql(array("betrag","transaktion","datum","member"), 'ORDER BY `datum` DESC').
                  " LIMIT ".($page - 1)*settings::get('m_clankasse').",".settings::get('m_clankasse').";");
        foreach($qry as $get) {
            $betrag = str_replace(',', '.', $get['betrag']);
            $pm = show((!$get['pm'] ? _clankasse_plus : _clankasse_minus),
                        array("betrag" => $betrag,"w" => str_replace(array('EUR','USD'), array('&euro;','$'),
                    re($get_settings['k_waehrung']))));

            $edit = ""; $delete = "";
            if(permission("clankasse")) {
                $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                              "title" => _button_title_edit,
                                                              "action" => "action=admin&amp;do=edit"));

                $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                                  "title" => _button_title_del,
                                                                  "action" => "action=admin&amp;do=delete",
                                                                  "del" => convSpace(_confirm_del_entry)));
            }

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/clankasse_show", array("betrag" => $pm,
                                                        "id" => $get['id'],
                                                        "class" => $class,
                                                        "for" => re($get['member']),
                                                        "transaktion" => re($get['transaktion']),
                                                        "delete" => $delete,
                                                        "edit" => $edit,
                                                        "datum" => date("d.m.Y",$get['datum'])));
        }

        $get_betrag = $sql->select("SELECT SUM(`betrag`) as `betrag` FROM `{prefix_clankasse}` GROUP BY `pm`;");
        $getp = $get_betrag[0]['betrag'];
        $getc = $get_betrag[1]['betrag'];
        unset($get_betrag);
        $ges = str_replace(".",",",round(($getp - $getc),2));
        $gesamt = show(($getp < $getc ? _clankasse_summe_minus : _clankasse_summe_plus),
            array("summe" => $ges, "w" => str_replace(array('EUR','USD'), array('&euro;','$'),
                    re($get_settings['k_waehrung']))));

        $qrys = $sql->select("SELECT tbl1.`id`,tbl1.`nick`,tbl2.`user`,tbl2.`payed` "
                . "FROM `{prefix_users}` AS `tbl1` "
                . "LEFT JOIN `{prefix_clankasse_payed}` AS `tbl2` "
                . "ON tbl2.`user` = tbl1.`id` "
                . "WHERE tbl1.`listck` = 1 OR tbl1.`level` = 4 ".
                orderby_sql(array("payed"), orderby_sql(array("nick"), 'ORDER BY tbl1.`nick`', 'tbl1'), 'tbl2').";");
        $showstatus = '';
        foreach($qrys as $gets) {
            if($gets['user']) {
                if($gets['payed'] >= time())
                    $status = show(_clankasse_status_payed, array("payed" => date("d.m.Y", $gets['payed'])));
                elseif(date("d.m.Y", $gets['payed']) == date("d.m.Y", time()))
                    $status = show(_clankasse_status_today, array());
                else
                    $status = show(_clankasse_status_notpayed, array("payed" => date("d.m.Y", $gets['payed'])));
            } else
                $status = show(_clankasse_status_noentry, array());

            $edit = permission("clankasse") ? show(_admin_ck_edit, array("id" => $gets['id'])) : "";
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $showstatus .= show($dir."/status", array("nick" => autor($gets['id']),
                                                      "status" => $status,
                                                      "class" => $class,
                                                      "edit" => $edit));
        }

        $seiten = nav($entrys,settings::get('m_clankasse'),"?action=nav");
        $new = permission("clankasse") ? _clankasse_new : '';
        $index = show($dir."/clankasse", array("show" => $show,
                                               "showstatus" => $showstatus,
                                               "order_nick" => orderby('nick'),
                                               "order_status" => orderby('payed'),
                                               "order_cdatum" => orderby('datum'),
                                               "order_ctransaktion" => orderby('transaktion'),
                                               "order_cfor" => orderby('member'),
                                               "order_cbetrag" => orderby('betrag'),
                                               "inhaber" => $get_settings['k_inhaber'],
                                               "kontonr" => $get_settings['k_nr'],
                                               "new" => $new,
                                               "blz" => $get_settings['k_blz'],
                                               "iban" => $get_settings['k_iban'],
                                               "bic" => $get_settings['k_bic'],
                                               "bank" => $get_settings['k_bank'],
                                               "vwz" => $get_settings['k_vwz'],
                                               "summe" => $gesamt,
                                               "seiten" => $seiten));
    }
}