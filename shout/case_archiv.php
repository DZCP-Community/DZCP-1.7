<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Shout')) exit();

$entrys = cnt('{prefix_shoutbox}');
$i = $entrys-($page - 1)*config('maxshoutarchiv');
$qry = $sql->select("SELECT * FROM `{prefix_shoutbox}` "
        . "ORDER BY `datum` DESC LIMIT ".($page - 1)*config('maxshoutarchiv').",".config('maxshoutarchiv').";");
foreach($qry as $get) {
    $is_num = preg_match("#\d#", re($get['email']));
    if($is_num && !check_email(re($get['email']))) 
        $nick = autor(re($get['email']));
    else if($chkMe == 4 || permission('ipban'))
        $nick = '<a href="mailto:'.re($get['email']).'" title="'.re($get['nick']).'">'.cut(re($get['nick']), config('l_shoutnick')).'</a>';
    else
        $nick = cut(re($get['nick']), config('l_shoutnick'));
    
    $del = permission("shoutbox") ? "<a href='../shout/?action=admin&amp;do=delete&amp;id=".$get['id']."'>"
           . "<img src='../inc/images/delete_small.gif' border='0' alt=''></a>" : "";

    $posted_ip = ($chkMe == 4 || permission('ipban') ? re($get['ip']) : _logged);
    $email = ($chkMe == 4 || permission('ipban') ? re($get['email']) : "");
    $class = ($color % 2) ? "contentMainTop" : "contentMainFirst"; $color++;
    $show .= show($dir."/shout_part", array("nick" => $nick,
                                            "datum" => date("j.m.Y H:i", $get['datum'])._uhr,
                                            "text" => bbcode(re($get['text'])),
                                            "class" => $class,
                                            "del" => $del,
                                            "ip" => $posted_ip,
                                            "id" => $i,
                                            "email" => $email));
    $i--;
}

$nav = nav($entrys,config('maxshoutarchiv'),"?action=archiv");
$index = show($dir."/shout", array("shout_part" => $show, "nav" => $nav));