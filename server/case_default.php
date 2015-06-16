<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Server')) exit();

if(show_gameserver_debug) {
    require_once(basePath.'/server/case_ajax.php'); //Debug
}

if(fsockopen_support()) {
    $sql_ext = ''; $params = array();
    if(isset($_GET['showID'])) {
        $qry = $sql->select("SELECT `id` FROM `{prefix_server}` WHERE `id` = ?;",array(intval($_GET['showID'])));
        foreach($qry as $get) {
            $sql_ext = " AND `id` != ?";
            $params = array(intval($_GET['showID']));

            if(show_gameserver_debug)
                $index .= server_show($get['id'],(isset($_GET['showID']) ? $_GET['showID'] : 0)); //Debug
            else {
                $showid=(isset($_GET['showID']) ? '&showID='.$_GET['showID'] : '');
                $url = '../server/?action=ajax&sID='.$get['id'].$showid;
                $index .= '<tr><td class="contentMainTop">
                <div id="PageServer_'.$get['id'].'"><div style="width:100%; 0;text-align:center"><img src="../inc/images/ajax-loader-bar.gif" alt="" /></div>
                <script language="javascript" type="text/javascript">DZCP.initPageDynLoader(\'PageServer_'.$get['id'].'\',\''.$url.'\');</script></div></tr>';
            }
        }
    }

    $qry = $sql->select("SELECT `id` FROM `{prefix_server}` WHERE `game` != 'nope'".$sql_ext." ORDER BY `game` ASC;",$params);
    foreach($qry as $get) {
        if(show_gameserver_debug)
            $index .= server_show($get['id'],(isset($_GET['showID']) ? $_GET['showID'] : 0)); //Debug
        else {
            $showid=(isset($_GET['showID']) ? '&showID='.$_GET['showID'] : '');
            $url = '../server/?action=ajax&sID='.$get['id'].$showid;
            $index .= '<tr><td class="contentMainTop">
            <div id="PageServer_'.$get['id'].'"><div style="width:100%; 0;text-align:center"><img src="../inc/images/ajax-loader-bar.gif" alt="" /></div>
            <script language="javascript" type="text/javascript">DZCP.initPageDynLoader(\'PageServer_'.$get['id'].'\',\''.$url.'\');</script></div></tr>';
        }
    }

    $qry = $sql->select("SELECT `id` FROM `{prefix_server}` WHERE `game` = 'nope' ORDER BY `id` ASC;");
    foreach($qry as $get) {
        if(show_gameserver_debug)
            $index .= server_show($get['id'],(isset($_GET['showID']) ? $_GET['showID'] : 0)); //Debug
        else {
            $showid=(isset($_GET['showID']) ? '&showID='.$_GET['showID'] : '');
            $url = '../server/?action=ajax&sID='.$get['id'].$showid;
            $index .= '<tr><td class="contentMainTop">
            <div id="PageServer_'.$get['id'].'"><div style="width:100%; 0;text-align:center"><img src="../inc/images/ajax-loader-bar.gif" alt="" /></div>
            <script language="javascript" type="text/javascript">DZCP.initPageDynLoader(\'PageServer_'.$get['id'].'\',\''.$url.'\');</script></div></tr>';
        }
    }

    $index = show($dir."/server", array("servers" => $index));
} else
    $index = error(_fopen);