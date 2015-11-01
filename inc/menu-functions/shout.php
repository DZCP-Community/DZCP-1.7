<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Shoutbox
 */

function shout($ajax = 0) {
    global $sql,$userid,$chkMe;
    
    $qry = $sql->select("SELECT `id`,`text`,`datum`,`nick`,`email` FROM `{prefix_shoutbox}` ORDER BY `id` DESC LIMIT ".settings::get('m_shout').";");
    $i = 1; $color = 0; $show = '';
    foreach($qry as $get) {
        $delete = "";
        if(permission("shoutbox"))
            $delete = '<a href="../shout/?action=admin&amp;do=delete&amp;id='.$get['id'].'" onclick="return(DZCP.del(\''.
                _confirm_del_shout.'\'))"><img src="../inc/images/delete_small.gif" title="'._button_title_del.'" alt="'._button_title_del.'" /></a>';

        if(preg_match("#\d#", re($get['email'])) && !check_email(re($get['email'])))
            $nick = autor(re($get['email']), "navShout",'','',settings::get('l_shoutnick'));
        else
            $nick = CryptMailto(re($get['email']),_email_navShout,array('nick' => $get['nick'], 'nick_cut' => cut($get['nick'], settings::get('l_shoutnick'))));

        $class = ($color % 2) ? "navShoutContentFirst" : "navShoutContentSecond"; $color++;
        $show .= show("menu/shout_part", array("nick" => $nick,
                                               "datum" => date("j.m.Y H:i", $get['datum'])._uhr,
                                               "text" => bbcode(wrap(re($get['text']), settings::get('l_shouttext')),false,false,false,true),
                                               "class" => $class,
                                               "del" => $delete));
        $i++;
    }

    if(!$ajax) {
        $dis = ''; $dis1 = ''; $only4reg = ''; $sec = ''; $form = '';
        if(settings::get('reg_shout') && !$chkMe) {
            $dis = ' style="text-align:center;cursor:wait" disabled="disabled"';
            $dis1 = ' style="cursor:wait;color:#888" disabled="disabled"';
            $only4reg = _shout_must_reg;
        } else {
            if(!$chkMe) {
                $form = show("menu/shout_form", array("dis" => $dis));
                $sec = show("menu/shout_antispam", array("dis" => $dis));
            } else
                $form = autor($userid, "navShout",'','',settings::get('l_shoutnick'));
        }

        $add = show("menu/shout_add", array("form" => $form,
                                            "dis1" => $dis1,
                                            "dis" => $dis,
                                            "only4reg" => $only4reg,
                                            "security" => $sec,
                                            "zeichen" => settings::get('shout_max_zeichen')));

        $shout = show("menu/shout", array("shout" => $show, "add" => $add));
        return '<table class="navContent" cellspacing="0">'.$shout.'</table>';
    } else {
        return $show;
    }
}