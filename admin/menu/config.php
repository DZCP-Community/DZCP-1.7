<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_global_head;

if($_POST) {
    $sql->update("UPDATE `{prefix_config}` SET `upicsize` = ?,`m_gallerypics` = ?,`m_usergb` = ?,`m_artikel` = ?,`m_adminartikel` = ?,`m_clanwars` = ?,`m_awards` = ?,"
                ."`allowhover` = ?,`securelogin` = ?,`m_clankasse` = ?,`m_userlist` = ?,`m_banned` = ?,`m_adminnews` = ?,`l_servernavi` = ?,`l_shoutnick`= ?,"
                ."`m_gb` = ?,`m_fthreads` = ?,`m_fposts` = ?,`gallery` = ?,`m_news` = ?,`m_shout` = ?,`m_comments` = ?,`m_archivnews` = ?,`maxwidth` = ?,`f_forum` = ?,"
                ."`f_cwcom` = ?,`f_gb` = ?,`f_artikelcom` = ?,`f_membergb` = ?,`f_shout` = ?,`f_newscom` = ?,`l_newsadmin` = ?,`l_shouttext` = ?,`l_newsarchiv` = ?,"
                ."`l_forumtopic` = ?,`l_forumsubtopic` = ?,`l_clanwars` = ?,`m_lnews` = ?,`m_lartikel` = ?,`m_events` = ?,`m_topdl` = ?,`m_ftopics` = ?,`m_cwcomments` = ?,"
                ."`m_lwars` = ?,`m_lreg` = ?,`m_nwars` = ?,`l_topdl` = ?,`l_ftopics` = ?,`l_lreg` = ?,`l_lnews` = ?,`l_lartikel` = ?,`l_lwars` = ?,`teamrow` = ?,"
                ."`shout_max_zeichen` = ?,`maxshoutarchiv` = ?,`m_away` = ?,`direct_refresh` = ?,`cache_teamspeak` = ?,`cache_server` = ?,`l_nwars` = ?,`m_membermap` = ? WHERE `id` = 1;",
        array(intval($_POST['m_upicsize']),intval($_POST['m_gallerypics']),intval($_POST['m_usergb']),intval($_POST['m_artikel']),intval($_POST['m_adminartikel']),
        intval($_POST['m_clanwars']),intval($_POST['m_awards']),intval($_POST['ahover']),intval($_POST['securelogin']),intval($_POST['m_clankasse']),intval($_POST['m_userlist']),
        intval($_POST['m_banned']),intval($_POST['m_adminnews']),intval($_POST['l_servernavi']),intval($_POST['l_shoutnick']),intval($_POST['m_gb']),intval($_POST['m_fthreads']),
        intval($_POST['m_fposts']),intval($_POST['m_gallery']),intval($_POST['m_news']),intval($_POST['m_shout']),intval($_POST['m_comments']),intval($_POST['m_archivnews']),
        intval($_POST['maxwidth']),intval($_POST['f_forum']),intval($_POST['f_cwcom']),intval($_POST['f_gb']),intval($_POST['f_artikelcom']),intval($_POST['f_membergb']),
        intval($_POST['f_shout']),intval($_POST['f_newscom']),intval($_POST['l_newsadmin']),intval($_POST['l_shouttext']),intval($_POST['l_newsarchiv']),intval($_POST['l_forumtopic']),
        intval($_POST['l_forumsubtopic']),intval($_POST['l_clanwars']),intval($_POST['m_lnews']),intval($_POST['m_lartikel']),intval($_POST['m_events']),intval($_POST['m_topdl']),
        intval($_POST['m_ftopics']),intval($_POST['m_cwcomments']),intval($_POST['m_lwars']),intval($_POST['m_lreg']),intval($_POST['m_nwars']),intval($_POST['l_topdl']),intval($_POST['l_ftopics']),
        intval($_POST['l_lreg']),intval($_POST['l_lnews']),intval($_POST['l_lartikel']),intval($_POST['l_lwars']),intval($_POST['teamrow']),intval($_POST['zeichen']),intval($_POST['m_shouta']),
        intval($_POST['m_away']),intval($_POST['direct_refresh']),intval($_POST['cache_teamspeak']),intval($_POST['cache_server']),intval($_POST['l_nwars']),intval($_POST['m_membermap'])));

    $sql->update("UPDATE `{prefix_settings}` SET `clanname` = ?,`pagetitel` = ?,`badwords` = ?,`gmaps_who` = ?,`language` = ?,`regcode` = ?, `forum_vote` = ?, `reg_forum` = ?,"
    . "`reg_artikel` = ?, `reg_shout` = ?, `reg_cwcomments` = ?, `counter_start` = ?,`reg_newscomments` = ?,`reg_dl` = ?,`eml_reg_subj` = ?, `eml_pwd_subj` = ?,"
    . "`eml_nletter_subj` = ?,`eml_pn_subj` = ?,`double_post` = ?,`gb_activ` = ?,`eml_fabo_npost_subj` = ?,`eml_reg` = ?, "
    . "`eml_pwd` = ?,`eml_nletter` = ?,`eml_pn` = ?,`eml_fabo_npost` = ?,`eml_fabo_tedit` = ?,`eml_fabo_pedit` = ?,`mailfrom` = ?,`tmpdir` = ?,`persinfo` = ?,`wmodus` = ?,"
    . "`urls_linked` = ?,`steam_api_key` = ? WHERE `id` = 1;",
    array(up($_POST['clanname']),up($_POST['pagetitel']),up($_POST['badwords']), intval($_POST['gmaps_who']),up($_POST['language']),intval($_POST['regcode']),intval($_POST['forum_vote']),
    intval($_POST['reg_forum']),intval($_POST['reg_artikel']),intval($_POST['reg_shout']),intval($_POST['reg_cwcomments']),intval($_POST['counter_start']),intval($_POST['reg_nc']),
    intval($_POST['reg_dl']),up($_POST['eml_reg_subj']),up($_POST['eml_pwd_subj']),up($_POST['eml_nletter_subj']),up($_POST['eml_pn_subj']),intval($_POST['double_post']),intval($_POST['gb_activ']),
    up($_POST['eml_fabo_npost_subj']),up($_POST['eml_reg']),up($_POST['eml_pwd']),up($_POST['eml_nletter']),up($_POST['eml_pn']),up($_POST['eml_fabo_npost']),up($_POST['eml_fabo_tedit']), 
    up($_POST['eml_fabo_pedit']),up($_POST['mailfrom']),up($_POST['tmpdir']),intval($_POST['persinfo']),intval($_POST['wmodus']),up($_POST['urls_linked']), up($_POST['steam_apikey'])));
    
    //-> Settingstabelle auslesen * Use function settings('xxxxxx');
    $get_settings = $sql->selectSingle("SELECT * FROM `{prefix_settings}`;");
    dbc_index::setIndex('settings', $get_settings);
    unset($get_settings);

    //-> Configtabelle auslesen * Use function config('xxxxxx');
    $get_config = $sql->selectSingle("SELECT * FROM `{prefix_config}`;");
    dbc_index::setIndex('config', $get_config);
    unset($get_config);

    notification::add_success(_config_set);
}

$get = dbc_index::getIndex('config');
$gets = dbc_index::getIndex('settings');

$files = get_files(basePath.'/inc/lang/languages/',false,true,array('php')); $lang = '';
foreach($files as $file) {
    $lng = preg_replace("#.php#", "",$file);
    $lang .= show(_select_field, array("value" => $lng, "what" => $lng, "sel" => re($gets['language']) == $lng ? 'selected="selected"' : ''));
}

$tmps = get_files(basePath.'/inc/_templates_/',true); $tmpldir = '';
foreach ($tmps as $tmp) {
    $tmpldir .= show(_select_field, array("value" => $tmp, "what" => $tmp, "sel" => re($gets['tmpdir']) == $tmp ? 'selected="selected"' : ''));
}

$selyes = $gets['regcode'] ? 'selected="selected"' : '';
$selno = !$gets['regcode'] ? 'selected="selected"' : '';
$selr_forum = $gets['reg_forum'] ? 'selected="selected"' : '';
$selr_nc = $gets['reg_newscomments'] ? 'selected="selected"' : '';
$selr_dl = $gets['reg_dl'] ? 'selected="selected"' : '';
$selr_artikel = $gets['reg_artikel'] ? 'selected="selected"' : '';
$selr_cwc = $gets['reg_cwcomments'] ? 'selected="selected"' : '';
$selr_shout = $gets['reg_shout'] ? 'selected="selected"' : '';
$selwm = $gets['wmodus'] ? 'selected="selected"' : '';
$selr_pi = !$gets['persinfo'] ? 'selected="selected"' : '';
$sel_sl = $get['securelogin'] ? 'selected="selected"' : '';
$selh_all = $get['allowhover'] == 1 ? 'selected="selected"' : '';
$selh_cw = $get['allowhover'] == 2 ? 'selected="selected"' : '';
$sel_gm = $gets['gmaps_who'] ? 'selected="selected"' : '';
$sel_dp = $gets['double_post'] ? 'selected="selected"' : '';
$sel_fv = $gets['forum_vote'] ? 'selected="selected"' : '';
$sel_gba =  $gets['gb_activ'] ? 'selected="selected"' : '';
$sel_url = $gets['urls_linked'] ? 'selected="selected"' : '';

$show_ = show($dir."/form_config", array("sel_refresh" => ($get['direct_refresh'] == 1 ? ' selected="selected"' : ''),
                                         "sel_gm" => $sel_gm,
                                         "cache_teamspeak" => intval($get['cache_teamspeak']),
                                         "cache_server" => $get['cache_server'],
                                         "c_eml_reg_subj" => re($gets['eml_reg_subj']),
                                         "c_eml_pwd_subj" => re($gets['eml_pwd_subj']),
                                         "c_eml_nletter_subj" => re($gets['eml_nletter_subj']),
                                         "c_eml_pn_subj" => re($gets['eml_pn_subj']),
                                         "c_eml_fabo_npost_subj" => re($gets['eml_fabo_npost_subj']),
                                         "c_eml_fabo_tedit_subj" => re($gets['eml_fabo_tedit_subj']),
                                         "c_eml_fabo_pedit_subj" => re($gets['eml_fabo_pedit_subj']),
                                         "c_eml_reg" => re($gets['eml_reg']),
                                         "c_eml_pwd" => re($gets['eml_pwd']),
                                         "c_eml_nletter" => re($gets['eml_nletter']),
                                         "c_eml_pn" => re($gets['eml_pn']),
                                         "c_eml_fabo_npost" => re($gets['eml_fabo_npost']),
                                         "c_eml_fabo_tedit" => re($gets['eml_fabo_tedit']),
                                         "c_eml_fabo_pedit" => re($gets['eml_fabo_pedit']),
                                         "tmpdir" => $tmpldir,
                                         "maxwidth" => intval($get['maxwidth']),
                                         "l_servernavi" => intval($get['l_servernavi']),
                                         "mailfrom" => re($gets['mailfrom']),
                                         "selpi" => $selr_pi,
                                         "l_lreg" => intval($get['l_lreg']),
                                         "m_lreg" => intval($get['m_lreg']),
                                         "steam_apikey" => re($gets['steam_api_key']),
                                         "selr_shout" => $selr_shout,
                                         "badwords" => re($gets['badwords']),
                                         "l_shoutnick" => intval($get['l_shoutnick']),
                                         "m_awards" => intval($get['m_awards']),
                                         "selr_cwc" => $selr_cwc,
                                         "f_cwcom" => intval($get['f_cwcom']),
                                         "selyes" => $selyes,
                                         "selno" => $selno,
                                         "regcode" => intval($gets['regcode']),
                                         "m_gallery" => intval($get['gallery']),
                                         "m_lnews" => intval($get['m_lnews']),
                                         "m_lartikel" => intval($get['m_lartikel']),
                                         "m_ftopics" => intval($get['m_ftopics']),
                                         "m_lwars" => intval($get['m_lwars']),
                                         "m_nwars" => intval($get['m_nwars']),
                                         "m_events" => intval($get['m_events']),
                                         "m_topdl" => intval($get['m_topdl']),
                                         "m_usergb" => intval($get['m_usergb']),
                                         "m_clankasse" => intval($get['m_clankasse']),
                                         "m_userlist" => intval($get['m_userlist']),
                                         "m_banned" => intval($get['m_banned']),
                                         "m_adminnews" => intval($get['m_adminnews']),
                                         "m_shout" => intval($get['m_shout']),
                                         "m_shouta" => intval($get['maxshoutarchiv']),
                                         "zeichen" => intval($get['shout_max_zeichen']),
                                         "m_comments" => intval($get['m_comments']),
                                         "m_cwcomments" => intval($get['m_cwcomments']),
                                         "m_archivnews" => intval($get['m_archivnews']),
                                         "m_membermap" => intval($get['m_membermap']),
                                         "m_gb" => intval($get['m_gb']),
                                         "m_fthreads" => intval($get['m_fthreads']),
                                         "m_fposts" => intval($get['m_fposts']),
                                         "m_clanwars" => intval($get['m_clanwars']),
                                         "m_news" => intval($get['m_news']),
                                         "m_gallerypics" => intval($get['m_gallerypics']),
                                         "m_upicsize" => intval($get['upicsize']),
                                         "c_start" => intval($gets['counter_start']),
                                         "f_forum" => intval($get['f_forum']),
                                         "f_gb" => intval($get['f_gb']),
                                         "f_membergb" => intval($get['f_membergb']),
                                         "f_shout" => intval($get['f_shout']),
                                         "f_newscom" => intval($get['f_newscom']),
                                         "m_artikel" => intval($get['m_artikel']),
                                         "m_adminartikel" => intval($get['m_adminartikel']),
                                         "m_away" => intval($get['m_away']),
                                         "c_wmodus" => intval($gets['wmodus']),
                                         "selwm" => $selwm,
                                         "l_clanwars" => intval($get['l_clanwars']),
                                         "l_newsadmin" => intval($get['l_newsadmin']),
                                         "l_shouttext" => intval($get['l_shouttext']),
                                         "l_newsarchiv" => intval($get['l_newsarchiv']),
                                         "l_forumtopic" => intval($get['l_forumtopic']),
                                         "l_forumsubtopic" => intval($get['l_forumsubtopic']),
                                         "l_topdl" => intval($get['l_topdl']),
                                         "l_ftopics" => intval($get['l_ftopics']),
                                         "l_lnews" => intval($get['l_lnews']),
                                         "l_lartikel" => intval($get['l_lartikel']),
                                         "l_lwars" => intval($get['l_lwars']),
                                         "l_nwars" => intval($get['l_nwars']),
                                         "clanname" => re($gets['clanname']),
                                         "pagetitel" => re($gets['pagetitel']),
                                         "lang" => $lang,
                                         "sel_fv" => $sel_fv,
                                         "sel_sl" => $sel_sl,
                                         "sel_dp" => $sel_dp,
                                         "sel_gba" => $sel_gba,
                                         "selh_all" => $selh_all,
                                         "selh_cw" => $selh_cw,
                                         "selr_nc" => $selr_nc,
                                         "selr_forum" => $selr_forum,
                                         "selr_dl" => $selr_dl,
                                         "selr_artikel" => $selr_artikel,
                                         "c_teamrow" => intval($get['teamrow']),
                                         "f_artikelcom" => intval($get['f_artikelcom']),
                                         "sel_url" => $sel_url));

$show = show($dir."/form", array("head" => _config_global_head,
                                 "what" => "config",
                                 "value" => _button_value_config,
                                 "show" => $show_));