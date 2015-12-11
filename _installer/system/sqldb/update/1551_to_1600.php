<?php
/**
 * <DZCP-Extended Edition>
 * @package: DZCP-Extended Edition
 * @author: DZCP Developer Team || Hammermaps.de Developer Team
 * @link: http://www.dzcp.de || http://www.hammermaps.de
 */

$versions[3] = array('update_id' => 3, 3 => '1.5.5.x', "version_list" => 'v1.5.5.x', 'call' => '155x_1600', 'dbv' => false); //Update Info

//Update von V1.5.5.x auf V1.6.0.0 DZCP-Extended Edition
function install_155x_1600_update()
{
    db("ALTER TABLE `".dba::get('f_threads')."` CHANGE `edited` `edited` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` CHANGE `whereami` `whereami` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` CHANGE `hlswid` `xfire` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",false,false,true);
    db("ALTER TABLE `".dba::get('downloads')."` ADD `last_dl` INT( 20 ) NOT NULL DEFAULT '0' AFTER `date`",false,false,true);
    db("ALTER TABLE `".dba::get('gb')."` CHANGE `hp` `hp` VARCHAR(130) CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('msg')."` CHANGE `see_u` `see_u` INT( 1 ) NOT NULL DEFAULT '0'",false,false,true);
    db("ALTER TABLE `".dba::get('msg')."` CHANGE `page` `page` INT( 1 ) NOT NULL DEFAULT '0'",false,false,true);
    db("ALTER TABLE `".dba::get('away')."` CHANGE `lastedit` `lastedit` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('newskat')."` CHANGE `katimg` `katimg` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",false,false,true);
    db("ALTER TABLE `".dba::get('newskat')."` CHANGE `kategorie` `kategorie` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",false,false,true);
    db("ALTER TABLE `".dba::get('server')."` CHANGE `name` `name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('server')."` DROP `bl_file`, DROP `bl_path`, DROP `ftp_pwd`, DROP `ftp_login`, DROP `ftp_host`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `pkey` VARCHAR( 50 ) NOT NULL DEFAULT '' AFTER `sessid`;",false,false,true);
    db("ALTER TABLE `".dba::get('navi')."` ADD `extended_perm` varchar(50) DEFAULT NULL AFTER `editor`;",false,false,true);
    db("ALTER TABLE `".dba::get('newscomments')."` CHANGE `editby` `editby` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('acomments')."` CHANGE `editby` `editby` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('cw_comments')."` CHANGE `editby` `editby` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('gb')."` CHANGE `editby` `editby` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('usergb')."` CHANGE `editby` `editby` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('usergb')."` ADD INDEX ( `user` );",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` CHANGE `gmaps_koord` `gmaps_koord` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `pwd_encoder` INT( 1 ) NOT NULL DEFAULT '0' AFTER `pwd`;",false,false,true);
    db("ALTER TABLE `".dba::get('artikel')."` ADD `viewed` INT( 11 ) NOT NULL DEFAULT '0' AFTER `url3`;",false,false,true);
    db("ALTER TABLE `".dba::get('downloads')."` ADD `comments` INT( 1 ) NOT NULL DEFAULT '0' AFTER `last_dl`;",false,false,true);
    db("ALTER TABLE `".dba::get('f_posts')."` CHANGE `edited` `edited` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL",false,false,true);
    db("ALTER TABLE `".dba::get('rankings')."` CHANGE `lastranking` `lastranking` INT( 10 ) NOT NULL DEFAULT '0'",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `profile_access` INT( 1 ) NOT NULL DEFAULT '0' AFTER `pnmail`;",false,false,true);
    db("ALTER TABLE `".dba::get('news')."` ADD `comments` INT( 1 ) NOT NULL DEFAULT '1' AFTER `timeshift`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `rss_key` VARCHAR( 50 ) NOT NULL DEFAULT '' AFTER `profile_access`;",false,false,true);
    db("ALTER TABLE `".dba::get('artikel')."` ADD `comments` INT( 1 ) NOT NULL DEFAULT '1' AFTER `public`;",false,false,true);
    db("ALTER TABLE `".dba::get('server')."` ADD `custom_icon` VARCHAR( 30 ) NOT NULL DEFAULT '' AFTER `qport`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `language` VARCHAR( 15 ) NOT NULL DEFAULT 'default' AFTER `country`;",false,false,true);
    db("ALTER TABLE `".dba::get('news')."` ADD `custom_image` INT( 1 ) NOT NULL DEFAULT '0' AFTER `comments` ;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `actkey` VARCHAR( 50 ) NOT NULL DEFAULT '' AFTER `pkey`;",false,false,true);
    db("ALTER TABLE `".dba::get('userstats')."` ADD `akl` INT( 5 ) NOT NULL DEFAULT '1' AFTER `cws`;",false,false,true);
    db("ALTER TABLE `".dba::get('artikel')."` ADD `custom_image` INT( 1 ) NOT NULL DEFAULT '0' AFTER `comments`;",false,false,true);
    db("ALTER TABLE `".dba::get('navi')."` ADD `title` VARCHAR( 249 ) NOT NULL DEFAULT '' AFTER `name`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `startpage` INT( 5 ) NOT NULL DEFAULT '0' AFTER `rss_key`;",false,false,true);
    db("ALTER TABLE `".dba::get('links')."` CHANGE `text` `blink` VARCHAR( 249 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` CHANGE `steamid` `steamurl` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",false,false,true);
    db("ALTER TABLE `".dba::get('pos')."` CHANGE `nletter` `nletter` INT( 1 ) NOT NULL DEFAULT '0';",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `skype` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `icq`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `xbox` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `skype`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `psn` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `xbox`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `origin` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `psn`;",false,false,true);
    db("ALTER TABLE `".dba::get('users')."` ADD `bnet` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `origin`;",false,false,true);

    // Setze fehlende Indexes * MySQL Optimierung
    db("ALTER TABLE `".dba::get('squaduser')."` ADD INDEX ( `user` ) ;",false,false,true);

    // Ersetze Settings und Config Tabelle
    if($get_settings = db("SELECT * FROM `".dba::get('settings')."` WHERE `id` = 1",false,true))
    {
        $finfo = database::get_fetch_fields();
        $keys = array();
        foreach ($finfo as $val)
        { $keys[$val['name']] = $get_settings[$val['name']]; }
    }

    dba::set('config','config');
    if($get_config = db("SELECT * FROM `".dba::get('config')."` WHERE `id` = 1",false,true))
    {
        $finfo = database::get_fetch_fields();
        foreach ($finfo as $val)
        { $keys[$val['name']] = $get_config[$val['name']]; }
    }

    db("DROP TABLE IF EXISTS `".dba::get('settings')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('settings')."` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `key` varchar(200) NOT NULL DEFAULT '',
        `value` text,
        `default` text,
        `length` int(11) NOT NULL DEFAULT '1',
        `type` varchar(20) NOT NULL DEFAULT 'int' COMMENT 'int/string',
        PRIMARY KEY (`id`),
        UNIQUE KEY `key` (`key`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    //E-Mail Templates
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_akl_register', `value` = '".string::encode(emlv('eml_akl_register'))."', `default` = '".string::encode(emlv('eml_akl_register'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_akl_register_subj', `value` = '".string::encode(emlv('eml_akl_register_subj'))."', `default` = '".string::encode(emlv('eml_akl_register_subj'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_fabo_npost', `value` = '".string::encode(emlv('eml_fabo_npost'))."', `default` = '".string::encode(emlv('eml_fabo_npost'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_fabo_npost_subj', `value` = '".string::encode(emlv('eml_fabo_npost_subj'))."', `default` = '".string::encode(emlv('eml_fabo_npost_subj'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_fabo_pedit', `value` = '".string::encode(emlv('eml_fabo_pedit'))."', `default` = '".string::encode(emlv('eml_fabo_pedit'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_fabo_pedit_subj', `value` = '".string::encode(emlv('eml_fabo_pedit_subj'))."', `default` = '".string::encode(emlv('eml_fabo_pedit_subj'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_fabo_tedit', `value` = '".string::encode(emlv('eml_fabo_tedit'))."', `default` = '".string::encode(emlv('eml_fabo_tedit'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_fabo_tedit_subj', `value` = '".string::encode(emlv('eml_fabo_tedit_subj'))."', `default` = '".string::encode(emlv('eml_fabo_tedit_subj'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_nletter', `value` = '".string::encode(emlv('eml_nletter'))."', `default` = '".string::encode(emlv('eml_nletter'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_nletter_subj', `value` = '".string::encode(emlv('eml_nletter_subj'))."', `default` = '".string::encode(emlv('eml_nletter_subj'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_pn', `value` = '".string::encode(emlv('eml_pn'))."', `default` = '".string::encode(emlv('eml_pn'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_pn_subj', `value` = '".string::encode(emlv('eml_pn_subj'))."', `default` = '".string::encode(emlv('eml_pn_subj'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_pwd', `value` = '".string::encode(emlv('eml_pwd'))."', `default` = '".string::encode(emlv('eml_pwd'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_pwd_subj', `value` = '".string::encode(emlv('eml_pwd_subj'))."', `default` = '".string::encode(emlv('eml_pwd_subj'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_reg', `value` = '".string::encode(emlv('eml_reg'))."', `default` = '".string::encode(emlv('eml_reg'))."', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'eml_reg_subj', `value` = '".string::encode(emlv('eml_reg_subj'))."', `default` = '".string::encode(emlv('eml_reg_subj'))."', `length` = '0', `type` = 'string';",false,false,true);

    //FTP Zugang
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'ftp_hostname', `value` = '".string::encode($_SESSION['ftp_host'])."', `default` = 'localhost', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'ftp_port', `value` = '".$_SESSION['ftp_port']."', `default` = '".$_SESSION['ftp_port']."', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'ftp_password', `value` = '".(!empty($_SESSION['ftp_pwd']) ? bin2hex(session::encrypt($_SESSION['ftp_pwd'])) : '')."', `default` = '', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'ftp_path', `value` = '".string::encode($_SESSION['ftp_pfad'])."', `default` = '/', `length` = '200', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'ftp_username', `value` = '".string::encode($_SESSION['ftp_user'])."', `default` = '".string::encode($_SESSION['ftp_user'])."', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'ftp_ssl', `value` = '".convert::ToString($_SESSION['ftp_ssl'])."', `default` = '".convert::ToString($_SESSION['ftp_ssl'])."', `length` = '1', `type` = 'int';",false,false,true);

    //Config
    $set_cache = 'file'; //File * Standard *
    if(function_exists('zend_shm_cache_store')) $set_cache = 'shm'; //ZEND Server - Shared Memory Cache
    else if(function_exists('apc_store')) $set_cache = 'apc'; //Alternative PHP Cache * APC *
    else if(function_exists('zend_disk_cache_store')) $set_cache = 'zenddisk'; //ZEND Server - Disk Cache
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'cache_engine', `value` = '".$set_cache."', `default` = 'file', `length` = '20', `type` = 'string';",false,false,true);
    unset($set_cache);

    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'securelogin', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'allowhover', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'badwords', `value` = 'arsch,Arsch,arschloch,Arschloch,hure,Hure', `default` = '', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'bic', `value` = '', `default` = '', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'cache_news', `value` = '5', `default` = '5', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'cache_server', `value` = '30', `default` = '30', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'cache_teamspeak', `value` = '30', `default` = '30', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'clanname', `value` = 'Dein Clanname hier!', `default` = 'Dein Clanname hier!', `length` = '50', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'db_version', `value` = '1600', `default` = '1600', `length` = '8', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'default_pwd_encoder', `value` = '2', `default` = '2', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'direct_refresh', `value` = '0', `default` = '0', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'domain', `value` = '".$_SERVER['SERVER_ADDR']."', `default` = '127.0.0.1', `length` = '150', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'double_post', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'forum_vote', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_artikelcom', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_cwcom', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_downloadcom', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_forum', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_gb', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_membergb', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_newscom', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'f_shout', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'gallery', `value` = '4', `default` = '4', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'gb_activ', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'gmaps_who', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'iban', `value` = '', `default` = '', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'i_autor', `value` = '', `default` = '', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'i_domain', `value` = '".$_SERVER['SERVER_NAME']."', `default` = 'www.deineUrl.de', `length` = '80', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'k_bank', `value` = 'Musterbank', `default` = 'Musterbank', `length` = '200', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'k_blz', `value` = '123456789', `default` = '123456789', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'k_inhaber', `value` = 'Max Mustermann', `default` = 'Max Mustermann', `length` = '50', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'k_nr', `value` = '123456789', `default` = '123456789', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'k_vwz', `value` = '', `default` = '', `length` = '200', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'k_waehrung', `value` = '&euro;', `default` = '&euro;', `length` = '15', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'language', `value` = 'deutsch', `default` = 'deutsch', `length` = '50', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'last_backup', `value` = '0', `default` = '0', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_clanwars', `value` = '30', `default` = '30', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_forumsubtopic', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_forumtopic', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_ftopics', `value` = '28', `default` = '28', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_lartikel', `value` = '18', `default` = '18', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_lnews', `value` = '22', `default` = '22', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_lreg', `value` = '12', `default` = '12', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_lwars', `value` = '12', `default` = '12', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_newsadmin', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_newsarchiv', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_nwars', `value` = '12', `default` = '12', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_servernavi', `value` = '22', `default` = '22', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_shoutnick', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_shouttext', `value` = '22', `default` = '22', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'l_topdl', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'mailfrom', `value` = 'info@127.0.0.1', `default` = 'info@127.0.0.1', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'mail_extension', `value` = 'mail', `default` = 'mail', `length` = '20', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'maxshoutarchiv', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'maxwidth', `value` = '400', `default` = '400', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'memcache_host', `value` = 'localhost', `default` = 'localhost', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'memcache_port', `value` = '11211', `default` = '11211', `length` = '11', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_adminartikel', `value` = '15', `default` = '15', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_adminnews', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_archivnews', `value` = '30', `default` = '30', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_artikel', `value` = '15', `default` = '15', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_awards', `value` = '15', `default` = '15', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_away', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_clankasse', `value` = '20', `default` = '20', `length` = 5'', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_clanwars', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_comments', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_cwcomments', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_events', `value` = '5', `default` = '5', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_fposts', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_fthreads', `value` = '20', `default` = '20', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_ftopics', `value` = '6', `default` = '6', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_gallery', `value` = '36', `default` = '36', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_gallerypics', `value` = '5', `default` = '5', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_gb', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_lartikel', `value` = '5', `default` = '5', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_lnews', `value` = '6', `default` = '6', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_lreg', `value` = '5', `default` = '5', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_lwars', `value` = '6', `default` = '6', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_news', `value` = '5', `default` = '5', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_nwars', `value` = '6', `default` = '6', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_shout', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_topdl', `value` = '5', `default` = '5', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_usergb', `value` = '10', `default` = '10', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'm_userlist', `value` = '40', `default` = '40', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'news_feed', `value` = '2', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'pagetitel', `value` = 'Dein Seitentitel hier!', `default` = 'Dein Seitentitel hier!', `length` = '50', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'persinfo', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'prev', `value` = '', `default` = '', `length` = '3', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'regcode', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'reg_artikel', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'reg_cwcomments', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'reg_dl', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'reg_dlcomments', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'reg_forum', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'reg_newscomments', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'reg_shout', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'sendmail_path', `value` = '/usr/sbin/sendmail', `default` = '/usr/sbin/sendmail', `length` = '150', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'shout_max_zeichen', `value` = '100', `default` = '100', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'smtp_hostname', `value` = 'localhost', `default` = 'localhost', `length` = '100', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'smtp_password', `value` = '', `default` = '', `length` = '0', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'smtp_port', `value` = '25', `default` = '25', `length` = '11', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'smtp_tls_ssl', `value` = '0', `default` = '0', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'smtp_username', `value` = '', `default` = '', `length` = '150', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'teamrow', `value` = '3', `default` = '3', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'tmpdir', `value` = 'version1.6', `default` = 'version1.6', `length` = '50', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'tmpdir_mobile', `value` = 'mobile1.6', `default` = 'mobile1.6', `length` = '50', `type` = 'string';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'upicsize', `value` = '100', `default` = '100', `length` = '5', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'urls_linked', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'use_akl', `value` = '1', `default` = '1', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'wmodus', `value` = '0', `default` = '0', `length` = '1', `type` = 'int';",false,false,true);
    db("INSERT INTO `".dba::get('settings')."` SET `key` = 'steam_api_key', `value` = '', `default` = 'XXXXXXXXXXXXXXXXXXXX', `length` = '100', `type` = 'string';",false,false,true);

    //Update
    foreach($keys as $key => $var)
    {
        if(!db("SELECT id FROM `".dba::get('settings')."` WHERE `key` = '".$key."'",true))
            db("INSERT INTO `".dba::get('settings')."` SET `key` = '".$key."', `value` = '".$var."', `default` = '', `length` = '100', `type` = 'string';",false,false,true);
        else
            db("UPDATE `".dba::get('settings')."` SET `value` = '".$var."' WHERE `key` = '".$key."';",false,false,true);
    }

    // Lösche dzcp_banned Tabelle
    dba::set('banned','banned'); //Tempadd
    db("DROP TABLE `".dba::get('banned')."`",false,false,true);

    // Forum Sortieren
    db("ALTER TABLE ".dba::get('f_skats')." ADD `pos` int(5) NOT NULL",false,false,true);

    // Forum Sortieren funktion: schreibe id von spalte in pos feld um konflikte zu vermeiden!
    $qry = db("SELECT id FROM `".dba::get('f_skats')."`");
    if(_rows($qry) >= 1)
    {  while($get = _fetch($qry)) { db("UPDATE ".dba::get('f_skats')." SET `pos` = '".$get['id']."' WHERE `id` = '".$get['id']."';",false,false,true); } }

    // Update News einsenden Link * wenn vorhanden
    $qry = db("SELECT id,url FROM `".dba::get('navi')."` WHERE `name` = '_news_send_'");
    if(_rows($qry) >= 1)
    {  while($get = _fetch($qry)) { if($get['url'] == '../news/send.php') db("UPDATE ".dba::get('navi')." SET `url` = '?index=news&action=send' WHERE `id` = '".$get['id']."';",false,false,true); } }

    // Update setze MD5 Encoder für alte User & Gen. Private RSS Key
    $qry = db("SELECT id FROM `".dba::get('users')."`");
    if(_rows($qry) >= 1)
    {  while($get = _fetch($qry)) { db("UPDATE ".dba::get('users')." SET `pwd_encoder` = 0, `rss_key`  = '".md5(mkpwd())."' WHERE `id` = '".$get['id']."';",false,false,true); } }

    //===============================================================
    //-> Cache ======================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('cache')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('cache')."` (
      `qry` varchar(32) NOT NULL DEFAULT '',
      `data` longblob,
      `timestamp` varchar(16) DEFAULT NULL,
      `cacheTime` varchar(16) DEFAULT NULL,
      `array` varchar(1) NOT NULL DEFAULT '0',
      `stream_hash` varchar(60) NOT NULL DEFAULT '',
      `original_file` varchar(255) NOT NULL DEFAULT '',
      PRIMARY KEY (`qry`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8;",false,false,true);

    //===============================================================
    //-> Click IP Counter ===========================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('clicks_ips')."`;");
    db("CREATE TABLE IF NOT EXISTS `".dba::get('clicks_ips')."` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip` varchar(15) NOT NULL DEFAULT '000.000.000.000',
    `uid` int(11) NOT NULL DEFAULT '0',
    `ids` int(11) NOT NULL DEFAULT '0',
    `side` varchar(30) NOT NULL DEFAULT '',
    `time` int(20) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `ip` (`ip`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    // Ersetze Permissions Tabelle
    $qry = db("SELECT * FROM `".dba::get('permissions')."`");
    if(_rows($qry) >= 1)
    {
        $cache_array_sql = array();
        while($get = _fetch($qry))
        {
            $cache_array_sql[] = "INSERT INTO `".dba::get('permissions')."` SET
            `user` = ".$get['user'].",
            `pos` = ".$get['pos'].",
            `intforum` = ".$get['intforum'].",
            `clankasse` = ".$get['clankasse'].",
            `clanwars` = ".$get['clanwars'].",
            `shoutbox` = ".$get['shoutbox'].",
            `serverliste` = ".$get['serverliste'].",
            `editusers` = ".$get['editusers'].",
            `editsquads` = ".$get['editsquads'].",
            `editserver` = ".$get['editserver'].",
            `editkalender` = ".$get['editkalender'].",
            `news` = ".$get['news'].",
            `gb` = ".$get['gb'].",
            `forum` = ".$get['forum'].",
            `votes` = ".$get['votes'].",
            `gallery` = ".$get['gallery'].",
            `votesadmin` = ".$get['votesadmin'].",
            `links` = ".$get['links'].",
            `downloads` = ".$get['downloads'].",
            `newsletter` = ".$get['newsletter'].",
            `intnews` = ".$get['intnews'].",
            `rankings` = ".$get['rankings'].",
            `contact` = ".$get['contact'].",
            `joinus` = ".$get['joinus'].",
            `awards` = ".$get['awards'].",
            `artikel` = ".$get['artikel'].",
            `receivecws` = ".$get['receivecws'].",
            `editor` = ".$get['editor'].",
            `glossar` = ".$get['glossar'].",
            `gs_showpw` = ".$get['gs_showpw'].";";
        }

        unset($qry,$get);
    }

    //===============================================================
    //-> Rechte =====================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('permissions')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('permissions')."` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user` int(11) NOT NULL DEFAULT '0',
      `pos` int(1) NOT NULL DEFAULT '0',
      `addons` int(1) NOT NULL DEFAULT '0',
      `artikel` int(1) NOT NULL DEFAULT '0',
      `awards` int(1) NOT NULL DEFAULT '0',
      `activateusers` int(1) NOT NULL DEFAULT '0',
      `backup` int(1) NOT NULL DEFAULT '0',
      `clear` int(1) NOT NULL DEFAULT '0',
      `config` int(1) NOT NULL DEFAULT '0',
      `contact` int(1) NOT NULL DEFAULT '0',
      `clanwars` int(1) NOT NULL DEFAULT '0',
      `clankasse` int(1) NOT NULL DEFAULT '0',
      `downloads` int(1) NOT NULL DEFAULT '0',
      `editkalender` int(1) NOT NULL DEFAULT '0',
      `editserver` int(1) NOT NULL DEFAULT '0',
      `editteamspeak` int(1) NOT NULL DEFAULT '0',
      `editsquads` int(1) NOT NULL DEFAULT '0',
      `editusers` int(1) NOT NULL DEFAULT '0',
      `editor` int(1) NOT NULL DEFAULT '0',
      `forum` int(1) NOT NULL DEFAULT '0',
      `gallery` int(1) NOT NULL DEFAULT '0',
      `gb` int(1) NOT NULL DEFAULT '0',
      `gs_showpw` int(1) NOT NULL DEFAULT '0',
      `glossar` int(1) NOT NULL DEFAULT '0',
      `impressum` int(1) NOT NULL DEFAULT '0',
      `intforum` int(1) NOT NULL DEFAULT '0',
      `intnews` int(1) NOT NULL DEFAULT '0',
      `joinus` int(1) NOT NULL DEFAULT '0',
      `links` int(1) NOT NULL DEFAULT '0',
      `news` int(1) NOT NULL DEFAULT '0',
      `newsletter` int(1) NOT NULL DEFAULT '0',
      `partners` int(1) NOT NULL DEFAULT '0',
      `profile` int(1) NOT NULL DEFAULT '0',
      `protocol` int(1) NOT NULL DEFAULT '0',
      `rankings` int(1) NOT NULL DEFAULT '0',
      `receivecws` int(1) NOT NULL DEFAULT '0',
      `serverliste` int(1) NOT NULL DEFAULT '0',
      `slideshow` int(1) NOT NULL DEFAULT '0',
      `smileys` int(1) NOT NULL DEFAULT '0',
      `sponsors` int(1) NOT NULL DEFAULT '0',
      `shoutbox` int(1) NOT NULL DEFAULT '0',
      `startpage` int(1) NOT NULL DEFAULT '0',
      `support` int(1) NOT NULL DEFAULT '0',
      `votes` int(1) NOT NULL DEFAULT '0',
      `votesadmin` int(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `user` (`user`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    // Permissions Datensatz einspielen
    if(count($cache_array_sql) >= 1)
    {
        foreach ($cache_array_sql as $sql)
        { db($sql); }
    }
    unset($cache_array_sql);

    //Permissions Tabelle prüfen
    $qry = db("SELECT id FROM `".dba::get('users')."`");
    if(_rows($qry))
    {
        while($get = _fetch($qry))
        { if(!db("SELECT id FROM `".dba::get('permissions')."` WHERE `user` = ".$get['id'],true)) db("INSERT INTO ".dba::get('permissions')." SET `user` = ".$get['id']); }
    }

    // Ersetze Forum Access Tabelle
    $qry = db("SELECT * FROM `".dba::get('f_access')."`");
    if(_rows($qry) >= 1)
    {
        $cache_array_sql = array();
        while($get = _fetch($qry))
        { $cache_array_sql[] = "INSERT INTO `".dba::get('f_access')."` SET `user` = ".$get['user']." , `pos` =  ".(empty($get['pos']) ? '0' : $get['pos']).", `forum` = ".$get['forum']; }

        unset($qry,$get);
    }

    //===============================================================
    //-> Forum: Access ==============================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('f_access')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('f_access')."` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user` int(11) NOT NULL DEFAULT '0',
      `pos` int(5) NOT NULL DEFAULT '0',
      `forum` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      UNIQUE KEY `id` (`id`),
      KEY `user` (`user`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8;",false,false,true);

    // Furm Access Datensatz einspielen
    if(count($cache_array_sql) >= 1)
    {
        foreach ($cache_array_sql as $sql)
        { db($sql); }
    }
    unset($cache_array_sql);

    // Navigation aktualisieren
    $qry = db("SELECT id FROM `".dba::get('navi')."` WHERE `name` = '_clankasse_'");
    if(_rows($qry))
    {
        while($get = _fetch($qry))
        { db("UPDATE `".dba::get('navi')."` SET `extended_perm` = 'clankasse' WHERE `id` = ".$get['id'].";"); }
    }

    //===============================================================
    //-> Downloadkommentare =========================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('dl_comments')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('dl_comments')."` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `download` int(10) NOT NULL DEFAULT '0',
      `nick` varchar(50) NOT NULL DEFAULT '',
      `datum` int(20) NOT NULL DEFAULT '0',
      `email` varchar(130) NOT NULL DEFAULT '',
      `hp` varchar(50) NOT NULL DEFAULT '',
      `reg` int(5) NOT NULL DEFAULT '0',
      `comment` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `editby` text,
      PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8;",false,false,true);

    //===============================================================
    //-> Gästebuchkommentare ========================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('gb_comments')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('gb_comments')."` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `gbe` int(10) NOT NULL DEFAULT '0',
      `nick` varchar(50) NOT NULL DEFAULT '',
      `datum` int(20) NOT NULL DEFAULT '0',
      `email` varchar(130) NOT NULL DEFAULT '',
      `hp` varchar(50) NOT NULL DEFAULT '',
      `reg` int(5) NOT NULL DEFAULT '0',
      `comment` text NOT NULL,
      `ip` varchar(50) NOT NULL DEFAULT '',
      `editby` text,
      PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8;",false,false,true);

    // Gallery Bilder verschieben
    $files = get_files(basePath."/gallery/images/",false,true);
    foreach($files as $file)
    {
        if(copy(basePath."/gallery/images/".$file ,basePath."/inc/images/uploads/gallery/".$file))
            if(file_exists(basePath."/inc/images/uploads/gallery/".$file))
                unlink(basePath."/gallery/images/".$file);
    }

    // Alten Gallery Bilder Ordner löschen
    if(is_dir(basePath."/gallery/images"))
        @rmdir(basePath."/gallery/images");

    // Squads Bilder verschieben
    $files = get_files(basePath."/inc/images/squads/",false,true);
    foreach($files as $file)
    {
        if(copy(basePath."/inc/images/squads/".$file ,basePath."/inc/images/uploads/squads/".$file))
            if(file_exists(basePath."/inc/images/uploads/squads/".$file))
            unlink(basePath."/inc/images/squads/".$file);
    }

    // Alten Squads Bilder Ordner löschen
    if(is_dir(basePath."/inc/images/squads"))
        @rmdir(basePath."/inc/images/squads");

    // Clanwars Bilder verschieben
    $files = get_files(basePath."/inc/images/clanwars/",false,true);
    foreach($files as $file)
    {
        if(copy(basePath."/inc/images/clanwars/".$file ,basePath."/inc/images/uploads/clanwars/".$file))
            if(file_exists(basePath."/inc/images/uploads/clanwars/".$file))
            unlink(basePath."/inc/images/clanwars/".$file);
    }

    // Alten Clanwars Bilder Ordner löschen
    if(is_dir(basePath."/inc/images/clanwars"))
        @rmdir(basePath."/inc/images/clanwars");

    //===============================================================
    //-> RSS Feeds ==================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('rss')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('rss')."` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `userid` int(11) NOT NULL,
      `show_public_news` int(1) NOT NULL DEFAULT '1',
      `show_public_news_max` int(11) NOT NULL DEFAULT '4',
      `show_intern_news` int(1) NOT NULL DEFAULT '1',
      `show_intern_news_max` int(11) NOT NULL DEFAULT '4',
      `show_artikel` int(1) NOT NULL DEFAULT '1',
      `show_artikel_max` int(11) NOT NULL DEFAULT '4',
      `show_downloads` int(1) NOT NULL DEFAULT '1',
      `show_downloads_max` int(11) NOT NULL DEFAULT '2',
      PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    //RSS Feed Config anlegen
    $qry = db("SELECT id FROM `".dba::get('users')."`");
    if(_rows($qry))
    {
        while($get = _fetch($qry))
        { db("INSERT INTO `".dba::get('rss')."` SET userid = ".$get['id'].";",false,false,true); }
    }

    //===============================================================
    //-> Teamspeak ==================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('ts')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('ts')."` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `host_ip_dns` varchar(200) NOT NULL DEFAULT '',
      `server_port` int(8) NOT NULL DEFAULT '9987',
      `query_port` int(8) NOT NULL DEFAULT '10011',
      `file_port` int(8) NOT NULL DEFAULT '30033',
      `username` varchar(100) NOT NULL DEFAULT '',
      `passwort` varchar(100) NOT NULL DEFAULT '',
      `customicon` int(1) NOT NULL DEFAULT '1',
      `showchannel` int(1) NOT NULL DEFAULT '0',
      `default_server` int(1) NOT NULL DEFAULT '0',
      `show_navi` int(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    //===============================================================
    //-> Teamspeak Update ===========================================
    //===============================================================
    $ts_settings = settings(array('ts_ip','ts_port','ts_sport','ts_version','ts_customicon','ts_showchannel'));
    if($ts_settings['ts_version'] == '3')
        db("INSERT INTO `".dba::get('ts')."` SET `host_ip_dns` = '".$ts_settings['ts_ip']."', `server_port` = ".$ts_settings['ts_port'].", `query_port` = ".$ts_settings['ts_sport'].", `customicon` = ".$ts_settings['ts_customicon'].", `showchannel` = ".$ts_settings['ts_showchannel'].", `default_server` = 1, `show_navi` = 1;",false,false,true);

    unset($ts_settings);
    db("ALTER TABLE `".dba::get('settings')."` DROP `ts_ip`, DROP `ts_port`, DROP `ts_sport`, DROP `ts_version`, DROP `ts_customicon`, DROP `ts_showchannel`, DROP `ts_width`;",false,false,true);

    //===============================================================
    //-> IP-Ban & Spam Blocker ======================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('ipban')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('ipban')."` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `ip` varchar(15) NOT NULL DEFAULT '255.255.255.255',
        `time` int(11) NOT NULL DEFAULT '0',
        `data` text,
        `typ` int(1) NOT NULL DEFAULT '0',
        `enable` int(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `id` (`id`),
        KEY `ip` (`ip`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    //===============================================================
    //-> Slideshow ==================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('slideshow')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('slideshow')."` (
    `id` int(5) NOT NULL AUTO_INCREMENT,
    `pos` int(5) NOT NULL DEFAULT '0',
    `bez` varchar(200) NOT NULL DEFAULT '',
    `showbez` int(1) NOT NULL default '1',
    `desc` varchar(249) NOT NULL DEFAULT '',
    `url` varchar(200) NOT NULL DEFAULT '',
    `target` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    //===============================================================
    //-> Startseite =================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('startpage')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('startpage')."` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(200) NOT NULL,
        `url` varchar(200) NOT NULL,
        `level` int(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    db("INSERT INTO `".dba::get('startpage')."` SET `name` = 'News', `url` => 'news/', `level` = 1;",false,false,true);
    db("INSERT INTO `".dba::get('startpage')."` SET `name` = 'Forum', `url` => 'forum/', `level` = 1;",false,false,true);

    //===============================================================
    //-> Addons =====================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('addons')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('addons')."` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `dir` varchar(200) NOT NULL DEFAULT '',
    `installed` int(1) NOT NULL DEFAULT '0',
    `enable` int(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    //===============================================================
    //-> Captcha ====================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('captcha')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('captcha')."` (
       `id` varchar(40) NOT NULL,
       `namespace` varchar(32) NOT NULL,
       `code` varchar(32) NOT NULL,
       `code_display` varchar(32) NOT NULL,
       `created` int(11) NOT NULL,
       PRIMARY KEY (`id`,`namespace`),
       KEY `created` (`created`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8;",false,false,true);

    //===============================================================
    //-> Sessions ===================================================
    //===============================================================
    db("DROP TABLE IF EXISTS `".dba::get('sessions')."`;",false,false,true);
    db("CREATE TABLE IF NOT EXISTS `".dba::get('sessions')."` (
        `id` char(128) NOT NULL,
        `set_time` char(10) NOT NULL,
        `data` text NOT NULL,
        `session_key` char(128) NOT NULL,
        PRIMARY KEY (`id`)
    ) ".dba::get_db_engine($_SESSION['mysql_dbengine'])." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",false,false,true);

    $tblist = database::list_tables();
    foreach ($tblist as $tb)
    { db('ALTER TABLE `'.$tb.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci PAGE_CHECKSUM =0;'); }

    return true;
}