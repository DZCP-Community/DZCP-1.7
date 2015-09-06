<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Forum Topics
 */

function ftopics() {
    global $sql;
    
    $qry = $sql->select("SELECT s1.*,s2.`kattopic`,s2.`id` as `subid` "
            . "FROM `{prefix_forumthreads}` as `s1`, `{prefix_forumsubkats}` as `s2`, {prefix_forumkats} as `s3` "
            . "WHERE s1.`kid` = s2.`id` AND s2.`sid` = s3.`id` ORDER BY s1.`lp` DESC LIMIT 100;");

    $f = 0; $ftopics = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            if($f == settings('m_ftopics')) { break; }
            if(fintern($get['kid'])) {
                $lp = cnt("{prefix_forumposts}", " WHERE `sid` = ?","id",array($get['id']));
                $pagenr = ceil($lp/settings('m_fposts'));
                $page = !$pagenr ? 1 : $pagenr;
                $info = !settings('allowhover') == 1 ? '' : 'onmouseover="DZCP.showInfo(\''.jsconvert(re($get['topic'])).'\', \''.
                        _forum_kat.';'._forum_posts.';'._forum_lpost.'\', \''.re($get['kattopic']).';'.++$lp.';'.
                        date("d.m.Y H:i", $get['lp'])._uhr.'\')" onmouseout="DZCP.hideInfo()"';
                
                $ftopics .= show("menu/forum_topics", array("id" => $get['id'],
                                                            "pagenr" => $page,
                                                            "p" => $lp,
                                                            "titel" => cut(re($get['topic']),settings('l_ftopics')),
                                                            "info" => $info,
                                                            "kid" => $get['kid']));
                $f++;
            }
        }
    }

    return empty($ftopics) ? '<center style="margin:2px 0">'._no_entrys.'</center>' : '<table class="navContent" cellspacing="0">'.$ftopics.'</table>';
}