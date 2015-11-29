<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: User Counter
 */

function counter($js=false) {
    global $sql,$useronline,$where,$isSpider;
    
    if(!$js) {
        $counter = '<div style="width:100%;padding:10px 0;text-align:center"><img src="../inc/images/ajax_loading.gif" alt="" /></div>'.
                "<script language=\"javascript\" type=\"text/javascript\">DZCP.initDynLoader('navCounter','counter','',true);</script>";
    } else {
        if(!$isSpider) {
            $v_today = 0;
            $get2day = $sql->fetch("SELECT `visitors` FROM `{prefix_counter}` WHERE `today` = ?;",array(date("j.n.Y")));
            if($sql->rowCount()) {
                $v_today = $get2day['visitors'];
            }

            $gestern = time() - 86400;
            $tag   = date("j", $gestern);
            $monat = date("n", $gestern);
            $jahr  = date("Y", $gestern);
            $yesterday = $tag.".".$monat.".".$jahr;

            $yDay = 0;
            $getyday = $sql->fetch("SELECT `visitors` FROM `{prefix_counter}` WHERE `today` = ?;",array($yesterday));
            if($sql->rowCount()) {
                $yDay = $getyday['visitors'];
            }

            $getstats = $sql->fetch("SELECT SUM(visitors) AS `allvisitors`, "
                                         . "MAX(visitors) AS `maxvisitors`, "
                                         . "MAX(maxonline) AS `maxonline`, "
                                         . "AVG(visitors) AS `avgvisitors`, "
                                         . "SUM(visitors) AS `allvisitors` "
                                         . "FROM `{prefix_counter}`;");

            $info = '';
            if(abs(online_reg()) != 0) {
                $qryo = $sql->select("SELECT `id` FROM `{prefix_users}` WHERE (time+?) > ? AND `online` = 1 ORDER BY `nick`;",array($useronline,time()));
                $kats = ''; $text = '';
                if($sql->rowCount()) {
                    foreach($qryo as $geto) {
                        $kats .= fabo_autor($geto['id']).';';
                        $text .= jsconvert(getrank($geto['id'])).';';
                    }
                }

                $info = 'onmouseover="DZCP.showInfo(\''._online_head.'\', \''.$kats.'\', \''.$text.'\')" onmouseout="DZCP.hideInfo()"';
            }

            $counter = show("menu/counter", array("v_today" => $v_today,
                                                  "v_yesterday" => $yDay,
                                                  "v_all" => ($getstats['allvisitors']),
                                                  "v_perday" => round($getstats['avgvisitors'], 2),
                                                  "v_max" => $getstats['maxvisitors'],
                                                  "g_online" => abs(online_guests($where)),
                                                  "u_online" => abs(online_reg()),
                                                  "info" => $info,
                                                  "v_online" => $getstats['maxonline']));

        }
    }

    return '<div id="navCounter">'.$counter.'</div>';
}