<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_global_head;

if($_POST) {
    if(settings::changed(($key='upicsize'),($var=intval($_POST['m_upicsize'])))) settings::set($key,$var);
    if(settings::changed(($key='m_gallery'),($var=intval($_POST['m_gallery'])))) settings::set($key,$var);
    if(settings::changed(($key='m_gallerypics'),($var=intval($_POST['m_gallerypics'])))) settings::set($key,$var);
    if(settings::changed(($key='m_usergb'),($var=intval($_POST['m_usergb'])))) settings::set($key,$var);
    if(settings::changed(($key='m_artikel'),($var=intval($_POST['m_artikel'])))) settings::set($key,$var);
    if(settings::changed(($key='m_adminartikel'),($var=intval($_POST['m_adminartikel'])))) settings::set($key,$var);
    if(settings::changed(($key='m_clanwars'),($var=intval($_POST['m_clanwars'])))) settings::set($key,$var);
    if(settings::changed(($key='m_awards'),($var=intval($_POST['m_awards'])))) settings::set($key,$var);
    if(settings::changed(($key='allowhover'),($var=intval($_POST['ahover'])))) settings::set($key,$var);
    if(settings::changed(($key='securelogin'),($var=intval($_POST['securelogin'])))) settings::set($key,$var);
    if(settings::changed(($key='m_clankasse'),($var=intval($_POST['m_clankasse'])))) settings::set($key,$var);
    if(settings::changed(($key='m_userlist'),($var=intval($_POST['m_userlist'])))) settings::set($key,$var);
    if(settings::changed(($key='m_adminnews'),($var=intval($_POST['m_adminnews'])))) settings::set($key,$var);
    if(settings::changed(($key='l_servernavi'),($var=intval($_POST['l_servernavi'])))) settings::set($key,$var);
    if(settings::changed(($key='l_shoutnick'),($var=intval($_POST['l_shoutnick'])))) settings::set($key,$var);
    if(settings::changed(($key='m_gb'),($var=intval($_POST['m_gb'])))) settings::set($key,$var);
    if(settings::changed(($key='m_fthreads'),($var=intval($_POST['m_fthreads'])))) settings::set($key,$var);
    if(settings::changed(($key='m_fposts'),($var=intval($_POST['m_fposts'])))) settings::set($key,$var);
    if(settings::changed(($key='gallery'),($var=intval($_POST['m_gallery_user'])))) settings::set($key,$var);
    if(settings::changed(($key='m_news'),($var=intval($_POST['m_news'])))) settings::set($key,$var);
    if(settings::changed(($key='m_shout'),($var=intval($_POST['m_shout'])))) settings::set($key,$var);
    if(settings::changed(($key='m_comments'),($var=intval($_POST['m_comments'])))) settings::set($key,$var);
    if(settings::changed(($key='m_archivnews'),($var=intval($_POST['m_archivnews'])))) settings::set($key,$var);
    if(settings::changed(($key='maxwidth'),($var=intval($_POST['maxwidth'])))) settings::set($key,$var);
    if(settings::changed(($key='f_forum'),($var=intval($_POST['f_forum'])))) settings::set($key,$var);
    if(settings::changed(($key='f_cwcom'),($var=intval($_POST['f_cwcom'])))) settings::set($key,$var);
    if(settings::changed(($key='f_gb'),($var=intval($_POST['f_gb'])))) settings::set($key,$var);
    if(settings::changed(($key='f_artikelcom'),($var=intval($_POST['f_artikelcom'])))) settings::set($key,$var);
    if(settings::changed(($key='f_membergb'),($var=intval($_POST['f_membergb'])))) settings::set($key,$var);
    if(settings::changed(($key='f_shout'),($var=intval($_POST['f_shout'])))) settings::set($key,$var);
    if(settings::changed(($key='f_newscom'),($var=intval($_POST['f_newscom'])))) settings::set($key,$var);
    if(settings::changed(($key='l_newsadmin'),($var=intval($_POST['l_newsadmin'])))) settings::set($key,$var);
    if(settings::changed(($key='l_shouttext'),($var=intval($_POST['l_shouttext'])))) settings::set($key,$var);
    if(settings::changed(($key='l_newsarchiv'),($var=intval($_POST['l_newsarchiv'])))) settings::set($key,$var);
    if(settings::changed(($key='l_forumtopic'),($var=intval($_POST['l_forumtopic'])))) settings::set($key,$var);
    if(settings::changed(($key='l_forumsubtopic'),($var=intval($_POST['l_forumsubtopic'])))) settings::set($key,$var);
    if(settings::changed(($key='l_clanwars'),($var=intval($_POST['l_clanwars'])))) settings::set($key,$var);
    if(settings::changed(($key='m_lnews'),($var=intval($_POST['m_lnews'])))) settings::set($key,$var);
    if(settings::changed(($key='m_lartikel'),($var=intval($_POST['m_lartikel'])))) settings::set($key,$var);
    if(settings::changed(($key='m_events'),($var=intval($_POST['m_events'])))) settings::set($key,$var);
    if(settings::changed(($key='m_topdl'),($var=intval($_POST['m_topdl'])))) settings::set($key,$var);
    if(settings::changed(($key='m_ftopics'),($var=intval($_POST['m_ftopics'])))) settings::set($key,$var);
    if(settings::changed(($key='m_cwcomments'),($var=intval($_POST['m_cwcomments'])))) settings::set($key,$var);
    if(settings::changed(($key='m_lwars'),($var=intval($_POST['m_lwars'])))) settings::set($key,$var);
    if(settings::changed(($key='m_lreg'),($var=intval($_POST['m_lreg'])))) settings::set($key,$var);
    if(settings::changed(($key='m_nwars'),($var=intval($_POST['m_nwars'])))) settings::set($key,$var);
    if(settings::changed(($key='l_topdl'),($var=intval($_POST['l_topdl'])))) settings::set($key,$var);
    if(settings::changed(($key='l_ftopics'),($var=intval($_POST['l_ftopics'])))) settings::set($key,$var);
    if(settings::changed(($key='l_lreg'),($var=intval($_POST['l_lreg'])))) settings::set($key,$var);
    if(settings::changed(($key='l_lnews'),($var=intval($_POST['l_lnews'])))) settings::set($key,$var);
    if(settings::changed(($key='l_lartikel'),($var=intval($_POST['l_lartikel'])))) settings::set($key,$var);
    if(settings::changed(($key='l_lwars'),($var=intval($_POST['l_lwars'])))) settings::set($key,$var);
    if(settings::changed(($key='teamrow'),($var=intval($_POST['teamrow'])))) settings::set($key,$var);
    if(settings::changed(($key='shout_max_zeichen'),($var=intval($_POST['zeichen'])))) settings::set($key,$var);
    if(settings::changed(($key='maxshoutarchiv'),($var=intval($_POST['m_shouta'])))) settings::set($key,$var);
    if(settings::changed(($key='m_away'),($var=intval($_POST['m_away'])))) settings::set($key,$var);
    if(settings::changed(($key='direct_refresh'),($var=intval($_POST['direct_refresh'])))) settings::set($key,$var);
    if(settings::changed(($key='cache_teamspeak'),($var=intval($_POST['cache_teamspeak'])))) settings::set($key,$var);
    if(settings::changed(($key='cache_server'),($var=intval($_POST['cache_server'])))) settings::set($key,$var);
    if(settings::changed(($key='cache_engine'),($var=stringParser::encode($_POST['cache_engine'])))) settings::set($key,$var);
    if(settings::changed(($key='l_nwars'),($var=intval($_POST['l_nwars'])))) settings::set($key,$var);
    if(settings::changed(($key='news_feed'),($var=intval($_POST['feed'])))) settings::set($key,$var);
    if(settings::changed(($key='clanname'),($var=stringParser::encode($_POST['clanname'])))) settings::set($key,$var);
    if(settings::changed(($key='default_pwd_encoder'),($var=stringParser::encode($_POST['pwd_encoder'])))) settings::set($key,$var);
    if(settings::changed(($key='pagetitel'),($var=stringParser::encode($_POST['pagetitel'])))) settings::set($key,$var);
    if(settings::changed(($key='badwords'),($var=stringParser::encode($_POST['badwords'])))) settings::set($key,$var);
    if(settings::changed(($key='gmaps_who'),($var=intval($_POST['gmaps_who'])))) settings::set($key,$var);
    if(settings::changed(($key='language'),($var=stringParser::encode($_POST['language'])))) settings::set($key,$var);
    if(settings::changed(($key='regcode'),($var=intval($_POST['regcode'])))) settings::set($key,$var);
    if(settings::changed(($key='forum_vote'),($var=intval($_POST['forum_vote'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_forum'),($var=intval($_POST['reg_forum'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_artikel'),($var=intval($_POST['reg_artikel'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_shout'),($var=intval($_POST['reg_shout'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_cwcomments'),($var=intval($_POST['reg_cwcomments'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_newscomments'),($var=intval($_POST['reg_nc'])))) settings::set($key,$var);
    if(settings::changed(($key='reg_dl'),($var=intval($_POST['reg_dl'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_reg_subj'),($var=stringParser::encode($_POST['eml_reg_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pwd_subj'),($var=stringParser::encode($_POST['eml_pwd_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_nletter_subj'),($var=stringParser::encode($_POST['eml_nletter_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pn_subj'),($var=stringParser::encode($_POST['eml_pn_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='double_post'),($var=intval($_POST['double_post'])))) settings::set($key,$var);
    if(settings::changed(($key='gb_activ'),($var=intval($_POST['gb_activ'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_npost_subj'),($var=stringParser::encode($_POST['eml_fabo_npost_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_tedit_subj'),($var=stringParser::encode($_POST['eml_fabo_tedit_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_pedit_subj'),($var=stringParser::encode($_POST['eml_fabo_pedit_subj'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_reg'),($var=stringParser::encode($_POST['eml_reg'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pwd'),($var=stringParser::encode($_POST['eml_pwd'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_nletter'),($var=stringParser::encode($_POST['eml_nletter'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_pn'),($var=stringParser::encode($_POST['eml_pn'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_npost'),($var=stringParser::encode($_POST['eml_fabo_npost'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_tedit'),($var=stringParser::encode($_POST['eml_fabo_tedit'])))) settings::set($key,$var);
    if(settings::changed(($key='eml_fabo_pedit'),($var=stringParser::encode($_POST['eml_fabo_pedit'])))) settings::set($key,$var);
    if(settings::changed(($key='mailfrom'),($var=stringParser::encode($_POST['mailfrom'])))) settings::set($key,$var);
    if(settings::changed(($key='tmpdir'),($var=stringParser::encode($_POST['tmpdir'])))) settings::set($key,$var);
    if(settings::changed(($key='wmodus'),($var=intval($_POST['wmodus'])))) settings::set($key,$var);
    if(settings::changed(($key='mail_extension'),($var=stringParser::encode($_POST['mail_extension'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_password'),($var=session::encode($_POST['smtp_pass'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_port'),($var=intval($_POST['smtp_port'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_hostname'),($var=stringParser::encode($_POST['smtp_host'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_username'),($var=stringParser::encode($_POST['smtp_username'])))) settings::set($key,$var);
    if(settings::changed(($key='smtp_tls_ssl'),($var=intval($_POST['smtp_tls_ssl'])))) settings::set($key,$var);
    if(settings::changed(($key='sendmail_path'),($var=stringParser::encode($_POST['sendmail_path'])))) settings::set($key,$var);
    if(settings::changed(($key='memcache_host'),($var=stringParser::encode($_POST['memcache_host'])))) settings::set($key,$var);
    if(settings::changed(($key='memcache_port'),($var=intval($_POST['memcache_port'])))) settings::set($key,$var);
    if(settings::changed(($key='urls_linked'),($var=stringParser::encode($_POST['urls_linked'])))) settings::set($key,$var);
    if(settings::changed(($key='steam_api_key'),($var=stringParser::encode($_POST['steam_apikey'])))) settings::set($key,$var);
    settings::load(true);
    notification::add_success(_config_set);
}

$files = get_files(basePath.'/inc/lang/languages/',false,true,array('php')); $lang = '';
foreach($files as $file) {
    $lng = preg_replace("#.php#", "",$file);
    $sel = (stringParser::decode(settings::get('language')) == $lng ? 'selected="selected"' : '');
    $lang .= show(_select_field, array("value" => $lng, "what" => $lng, "sel" => $sel));
}
unset($files,$file,$lng,$sel);

$tmps = get_files(basePath.'/inc/_templates_/',true); $tmplsel = '';
foreach($tmps as $tmp) {
    $selt = (stringParser::decode(settings::get('tmpdir')) == $tmp ? 'selected="selected"' : '');
    $tmplsel .= show(_select_field, array("value" => $tmp, "what" => $tmp, "sel" => $selt));
}
unset($tmps,$tmp,$selt);

$pwde_options = show('<option '.(!settings::get('default_pwd_encoder') ? 'selected="selected"' : '').' value="0">MD5 [lang_pwd_encoder_algorithm]</option>'
. '<option '.(settings::get('default_pwd_encoder') == 1 ? 'selected="selected"' : '').' value="1">SHA1 [lang_pwd_encoder_algorithm]</option>'
. '<option '.(settings::get('default_pwd_encoder') == 2 ? 'selected="selected"' : '').' value="2">SHA256 [lang_pwd_encoder_algorithm]</option>'
. '<option '.(settings::get('default_pwd_encoder') == 3 ? 'selected="selected"' : '').' value="3">SHA512 [lang_pwd_encoder_algorithm]</option>');

$cache_options = show('<option '.(settings::get('cache_engine') == 'auto' ? 'selected="selected"' : '').' value="auto">'._default.'</option>'
. '<option '.(settings::get('cache_engine') == 'files' ? 'selected="selected"' : '').' value="files">Files</option>'
. '<option '.(settings::get('cache_engine') == 'apc' ? 'selected="selected"' : '').' value="apc">Alternative PHP Cache</option>'
. '<option '.(settings::get('cache_engine') == 'memcache' ? 'selected="selected"' : '').' value="memcache">Memcache</option>'
. '<option '.(settings::get('cache_engine') == 'wincache' ? 'selected="selected"' : '').' value="wincache">WinCache</option>'
. '<option '.(settings::get('cache_engine') == 'xcache' ? 'selected="selected"' : '').' value="xcache">XCache</option>'
. '<option '.(settings::get('cache_engine') == 'sqlite' ? 'selected="selected"' : '').' value="sqlite">SQLite</option>');

$mail_options = show('<option '.(settings::get('mail_extension') == 'mail' ? 'selected="selected"' : '').' value="mail">'._default.'</option>'
. '<option '.(settings::get('mail_extension') == 'sendmail' ? 'selected="selected"' : '').' value="sendmail">Sendmail</option>'
. '<option '.(settings::get('mail_extension') == 'smtp' ? 'selected="selected"' : '').' value="smtp">SMTP</option>');

$smtp_secure_options = show('<option '.(!settings::get('smtp_tls_ssl') ? 'selected="selected"' : '').' value="0">[lang_default]</option>'
. '<option '.(settings::get('smtp_tls_ssl') == 1 ? 'selected="selected"' : '').' value="1">TLS</option>'
. '<option '.(settings::get('smtp_tls_ssl') == 2 ? 'selected="selected"' : '').' value="2">SSL</option>');

$show = show($dir."/form_config", array( "cache_select"          => $cache_options,
                                         "main_info"             => _main_info,
                                         "cache_info"            => _config_cache_info,
                                         "badword_info"          => _admin_config_badword_info,
                                         "eml_info"              => _admin_eml_info,
                                         "reg_info"              => _admin_reg_info,
                                         "c_limits_what"         => _config_c_limits_what,
                                         "c_floods_what"         => _config_c_floods_what,
                                         "c_length_what"         => _config_c_length_what,
                                         "cache_teamspeak"       => intval(settings::get('cache_teamspeak')),
                                         "cache_server"          => intval(settings::get('cache_server')),
                                         "c_eml_reg_subj"        => stringParser::decode(settings::get('eml_reg_subj')),
                                         "c_eml_pwd_subj"        => stringParser::decode(settings::get('eml_pwd_subj')),
                                         "c_eml_nletter_subj"    => stringParser::decode(settings::get('eml_nletter_subj')),
                                         "c_eml_pn_subj"         => stringParser::decode(settings::get('eml_pn_subj')),
                                         "c_eml_fabo_npost_subj" => stringParser::decode(settings::get('eml_fabo_npost_subj')),
                                         "c_eml_fabo_tedit_subj" => stringParser::decode(settings::get('eml_fabo_tedit_subj')),
                                         "c_eml_fabo_pedit_subj" => stringParser::decode(settings::get('eml_fabo_pedit_subj')),
                                         "c_eml_reg"             => stringParser::decode(settings::get('eml_reg')),
                                         "c_eml_pwd"             => stringParser::decode(settings::get('eml_pwd')),
                                         "c_eml_nletter"         => stringParser::decode(settings::get('eml_nletter')),
                                         "c_eml_pn"              => stringParser::decode(settings::get('eml_pn')),
                                         "c_eml_fabo_tedit"      => stringParser::decode(settings::get('eml_fabo_tedit')),
                                         "c_eml_fabo_pedit"      => stringParser::decode(settings::get('eml_fabo_pedit')),
                                         "c_eml_fabo_nposr"      => stringParser::decode(settings::get('eml_fabo_npost')),
                                         "memcache_host"         => stringParser::decode(settings::get('memcache_host')),
                                         "memcache_port"         => intval(settings::get('memcache_port')),
                                         "steam_apikey"          => stringParser::decode(settings::get('steam_api_key')),
                                         "tmplsel"               => $tmplsel,
                                         "maxwidth"              => intval(settings::get('maxwidth')),
                                         "l_servernavi"          => intval(settings::get('l_servernavi')),
                                         "mailfrom"              => stringParser::decode(settings::get('mailfrom')),
                                         "l_lreg"                => intval(settings::get('l_lreg')),
                                         "m_lreg"                => intval(settings::get('m_lreg')),
                                         "badwords"              => stringParser::decode(settings::get('badwords')),
                                         "l_shoutnick"           => intval(settings::get('l_shoutnick')),
                                         "m_awards"              => intval(settings::get('m_awards')),
                                         "f_cwcom"               => intval(settings::get('f_cwcom')),
                                         "regcode"               => intval(settings::get('regcode')),
                                         "m_gallery_user"        => intval(settings::get('gallery')),
                                         "m_gallery"             => intval(settings::get('m_gallery')),
                                         "m_lnews"               => intval(settings::get('m_lnews')),
                                         "m_lartikel"            => intval(settings::get('m_lartikel')),
                                         "m_ftopics"             => intval(settings::get('m_ftopics')),
                                         "m_lwars"               => intval(settings::get('m_lwars')),
                                         "m_nwars"               => intval(settings::get('m_nwars')),
                                         "m_events"              => intval(settings::get('m_events')),
                                         "m_topdl"               => intval(settings::get('m_topdl')),
                                         "m_usergb"              => intval(settings::get('m_usergb')),
                                         "m_clankasse"           => intval(settings::get('m_clankasse')),
                                         "m_userlist"            => intval(settings::get('m_userlist')),
                                         "m_adminnews"           => intval(settings::get('m_adminnews')),
                                         "m_shout"               => intval(settings::get('m_shout')),
                                         "m_shouta"              => intval(settings::get('maxshoutarchiv')),
                                         "zeichen"               => intval(settings::get('shout_max_zeichen')),
                                         "m_comments"            => intval(settings::get('m_comments')),
                                         "m_cwcomments"          => intval(settings::get('m_cwcomments')),
                                         "m_archivnews"          => intval(settings::get('m_archivnews')),
                                         "m_gb"                  => intval(settings::get('m_gb')),
                                         "m_fthreads"            => intval(settings::get('m_fthreads')),
                                         "m_fposts"              => intval(settings::get('m_fposts')),
                                         "m_clanwars"            => intval(settings::get('m_clanwars')),
                                         "m_news"                => intval(settings::get('m_news')),
                                         "m_gallerypics"         => intval(settings::get('m_gallerypics')),
                                         "m_upicsize"            => intval(settings::get('upicsize')),
                                         "f_forum"               => intval(settings::get('f_forum')),
                                         "f_gb"                  => intval(settings::get('f_gb')),
                                         "f_membergb"            => intval(settings::get('f_membergb')),
                                         "f_shout"               => intval(settings::get('f_shout')),
                                         "f_newscom"             => intval(settings::get('f_newscom')),
                                         "m_artikel"             => intval(settings::get('m_artikel')),
                                         "m_adminartikel"        => intval(settings::get('m_adminartikel')),
                                         "m_away"                => intval(settings::get('m_away')),
                                         "c_wmodus"              => intval(settings::get('wmodus')),
                                         "l_clanwars"            => intval(settings::get('l_clanwars')),
                                         "l_newsadmin"           => intval(settings::get('l_newsadmin')),
                                         "l_shouttext"           => intval(settings::get('l_shouttext')),
                                         "l_newsarchiv"          => intval(settings::get('l_newsarchiv')),
                                         "l_forumtopic"          => intval(settings::get('l_forumtopic')),
                                         "l_forumsubtopic"       => intval(settings::get('l_forumsubtopic')),
                                         "l_topdl"               => intval(settings::get('l_topdl')),
                                         "l_ftopics"             => intval(settings::get('l_ftopics')),
                                         "l_lnews"               => intval(settings::get('l_lnews')),
                                         "l_lartikel"            => intval(settings::get('l_lartikel')),
                                         "l_lwars"               => intval(settings::get('l_lwars')),
                                         "l_nwars"               => intval(settings::get('l_nwars')),
                                         "c_teamrow"             => intval(settings::get('teamrow')),
                                         "f_artikelcom"          => intval(settings::get('f_artikelcom')),
                                         "clanname"              => stringParser::decode(settings::get('clanname')),
                                         "pagetitel"             => stringParser::decode(settings::get('pagetitel')),
                                         "smtp_host"             => stringParser::decode(settings::get('smtp_hostname')),
                                         "smtp_username"         => stringParser::decode(settings::get('smtp_username')),
                                         "smtp_pass"             => session::decode(settings::get('smtp_password')),
                                         "smtp_port"             => intval(settings::get('smtp_port')),
                                         "sendmail_path"         => stringParser::decode(settings::get('sendmail_path')),
                                         "smtp_tls_ssl"          => $smtp_secure_options,
                                         "lang"                  => $lang,
                                         "mail_ext_select"       => $mail_options,
                                         "selyes"                => (settings::get('regcode') ? 'selected="selected"' : ''),
                                         "selno"                 => (!settings::get('regcode') ? 'selected="selected"' : ''),
                                         "selwm"                 => (settings::get('wmodus') ? 'selected="selected"' : ''),
                                         "sel_fv"                => (settings::get('forum_vote') ? 'selected="selected"' : ''),
                                         "sel_sl"                => (settings::get('securelogin') ? 'selected="selected"' : ''),
                                         "sel_dp"                => (settings::get('double_post') ? 'selected="selected"' : ''),
                                         "sel_gba"               => (settings::get('gb_activ') ? 'selected="selected"' : ''),
                                         "selh_all"              => (settings::get('allowhover') == 1 ? 'selected="selected"' : ''),
                                         "selh_cw"               => (settings::get('allowhover') == 2 ? 'selected="selected"' : ''),
                                         "selr_nc"               => (settings::get('reg_newscomments') ? 'selected="selected"' : ''),
                                         "selr_forum"            => (settings::get('reg_forum') ? 'selected="selected"' : ''),
                                         "selr_dl"               => (settings::get('reg_dl') ? 'selected="selected"' : ''),
                                         "selr_artikel"          => (settings::get('reg_artikel') ? 'selected="selected"' : ''),
                                         "sel_url"               => (settings::get('urls_linked') ? 'selected="selected"' : ''),
                                         "selr_shout"            => (settings::get('reg_shout') ? 'selected="selected"' : ''),
                                         "selfeed"               => (settings::get('news_feed') ? 'selected="selected"' : ''),
                                         "selr_cwc"              => (settings::get('reg_cwcomments') ? 'selected="selected"' : ''),
                                         "sel_refresh"           => (settings::get('direct_refresh') ? ' selected="selected"' : ''),
                                         "sel_gm"                => (settings::get('gmaps_who') ? 'selected="selected"' : ''),
                                         "pwde_options"          => $pwde_options));

$show = show($dir."/form", array("head" => _config_global_head, "what" => "config", "value" => _button_value_config, "show" => $show));