<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

$where = $where.': '._protocol;
if($do == 'deletesingle') {
    $sql->delete("DELETE FROM `{prefix_ipcheck}` WHERE `id` = ?;",array(intval($_GET['id'])));
    header("Location: ".GetServerVars('HTTP_REFERER'));  
} elseif($do == 'delete') {
    $sql->delete("DELETE FROM `{prefix_ipcheck}` WHERE `time` != 0;");
    $show = info(_protocol_deleted,'?admin=protocol');
} else {
    $params = array();
    if(!empty($_GET['sip'])) {
        $search = "WHERE `ip` = ? AND `time` != 0 AND `what` NOT REGEXP 'vid_'";
        array_push($params, up($_GET['sip']));
        $swhat = $_GET['sip'];
    } else {
        $search = "WHERE `time` != 0 AND `what` NOT REGEXP 'vid_'";
        $swhat = _info_ip;
    }

    $maxprot = 30;
    $entrys = cnt('{prefix_ipcheck}', $search, 'id', $params);
    $qry = $sql->select("SELECT * FROM `{prefix_ipcheck}` ".$search." ORDER BY `id` DESC LIMIT ".($page - 1)*$maxprot.",".$maxprot.";",$params);
    foreach($qry as $get) {
          $action = "";
          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
          $date = date("d.m.y H:i", $get['time'])._uhr;
          $delete = show("page/button_delete", array("id" => $get['id'],
                                                     "action" => "admin=protocol&amp;do=deletesingle",
                                                     "title" => _button_title_del));

        if(preg_match("#\(#",$get['what'])) {
            $a = preg_replace("#^(.*?)\((.*?)\)#is","$1",$get['what']);
            $wid = preg_replace("#^(.*?)\((.*?)\)#is","$2",$get['what']);

            if($a == 'fid')
                $action = 'wrote in <b>board</b>';
            elseif($a == 'ncid')
                $action = 'wrote <b>comment</b> in <b>news</b> with <b>ID</b> '.$wid;
            elseif($a == 'artid')
                $action = 'wrote <b>comment</b> in <b>article</b> with <b>ID</b> '.$wid;
            elseif($a == 'vid')
                $action = 'voted <b>poll</b> with <b>ID '.$wid.'</b>';
            elseif($a == 'mgbid')
                $action = autor($wid).' got a userbook entry';
            elseif($a == 'cwid')
                $action = 'wrote <b>comment</b> in <b>clanwar</b> with <b>ID</b> '.$wid;
            elseif($a == 'createuser') {
                $ids = explode("_", $wid);
                $action = '<b style="color:red">ADMIN:</b> '.autor($ids[0]).' <b>added</b> user '.autor($ids[1]);
            } elseif($a == 'upduser') {
                $ids = explode("_", $wid);
                $action = '<b style="color:red">ADMIN:</b> '.autor($ids[0]).' <b>edited</b> user '.autor($ids[1]);
            } elseif($a == 'deluser') {
                $ids = explode("_", $wid);
                $action = '<b style="color:red">ADMIN:</b> '.autor($ids[0]).' <b>deleted</b> user';
            } elseif($a == 'ident') {
                $ids = explode("_", $wid);
                $action = '<b style="color:red">ADMIN:</b> '.autor($ids[0]).' took <b>identity</b> from user '.autor($ids[1]);
            } elseif($a == 'logout')
                $action = autor($wid).' <b>logged out</b>';
            elseif($a == 'login')
                $action = autor($wid).' <b>logged in</b>';
            elseif($a == 'trypwd')
                $action = 'failed to <b>reset password</b> from '.autor($wid);
            elseif($a == 'pwd')
                $action = '<b>reseted password</b> from '.autor($wid);
            elseif($a == 'reg')
                $action = autor($wid).' <b>signed up</b>';
            elseif($a == 'trylogin')
                $action = 'failed to <b>login</b> in '.autor($wid).'`s account';
            elseif($a == 'db_optimize')
                $action = '<b style="color:blue">SYSTEM:</b> Database Optimize/Cleanup performed';
            else 
                $action = '<b style="color:red">undefined:</b> <b>'.$a.'</b>';
        } else {
            if($get['what'] == 'gb')
                $action = 'wrote in <b>guestbook</b>';
            elseif($get['what'] == 'shout')
                $action = 'wrote in <b>shoutbox</b>';
            else 
                $action = '<b style="color:red">undefined:</b> <b>'.$a.'</b>';
        }

        $show .= show($dir."/protocol_show", array("datum" => $date,
                                                   "class" => $class,
                                                   "delete" => $delete,
                                                   "user" => $get['ip'],
                                                   "action" => $action));
    }

    if(empty($show))
        $show = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';

    $sip = (isset($_GET['sip']) && !empty($_GET['sip'])) ? "&amp;sip=".$_GET['sip'] : "";
    $show = show($dir."/protocol", array("show" => $show,
                                         "search" => $swhat,
                                         "nav" => nav($entrys,$maxprot,"?admin=protocol".$sip)));
}