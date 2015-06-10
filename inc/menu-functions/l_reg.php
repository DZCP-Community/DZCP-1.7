<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: zuletzt registrierte User
 */

function l_reg() {
    global $sql;
    
    $qry = $sql->select("SELECT `id`,`nick`,`country`,`regdatum` "
                      . "FROM `{prefix_users}` "
                      . "ORDER BY `regdatum` DESC LIMIT ".config('m_lreg').";");
    
    $lreg = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $lreg .= show("menu/last_reg", array("nick" => cut(re($get['nick']), config('l_lreg')),
                                                 "country" => flag($get['country']),
                                                 "reg" => date("d.m.", $get['regdatum']),
                                                 "id" => $get['id']));
        }
    }

    return empty($lreg) ? '<center style="margin:2px 0">'._no_entrys.'</center>' : '<table class="navContent" cellspacing="0">'.$lreg.'</table>';
}