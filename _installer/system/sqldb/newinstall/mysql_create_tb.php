<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('INSTALLER')) exit();

//=======================================
//Create DZCP Database MySQL Installer
//=======================================

//Neuinstallation
function install_mysql_create() {
    global $database;
    ignore_user_abort(true);
    $sql = $database->getInstance();
    
    //===============================================================
    //-> Artikelkommentare ==========================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_acomments}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_acomments}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`artikel` int(11) NOT NULL DEFAULT '0',"
            . "`nick` varchar(50) NOT NULL DEFAULT '',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`email` varchar(100) NOT NULL DEFAULT '',"
            . "`hp` varchar(50) NOT NULL DEFAULT '',"
            . "`reg` int(5) NOT NULL DEFAULT '0',"
            . "`comment` text NOT NULL,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`editby` text, PRIMARY KEY (`id`), "
            . "KEY `artikel` (`artikel`) USING BTREE "
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Artikel ====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_artikel}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_artikel}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`autor` int(11) NOT NULL DEFAULT '0',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`kat` int(5) NOT NULL DEFAULT '0',"
            . "`titel` varchar(249) NOT NULL DEFAULT '',"
            . "`text` text NOT NULL,"
            . "`link1` varchar(100) NOT NULL DEFAULT '',"
            . "`url1` varchar(200) NOT NULL DEFAULT '',"
            . "`link2` varchar(100) NOT NULL DEFAULT '',"
            . "`url2` varchar(200) NOT NULL DEFAULT '',"
            . "`link3` varchar(100) NOT NULL DEFAULT '',"
            . "`url3` varchar(200) NOT NULL DEFAULT '',"
            . "`public` int(1) NOT NULL DEFAULT '0',"
            . "`viewed` int(11) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Autologin ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_autologin}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_autologin}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`uid` int(11) NOT NULL DEFAULT '0',"
            . "`ssid` varchar(50) NOT NULL DEFAULT '',"
            . "`pkey` varchar(50) NOT NULL DEFAULT '',"
            . "`name` varchar(60) NOT NULL DEFAULT '',"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`host` varchar(150) NOT NULL DEFAULT '',"
            . "`date` int(11) NOT NULL DEFAULT '0',"
            . "`update` int(11) NOT NULL DEFAULT '0',"
            . "`expires` int(11) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `ssid` (`ssid`) USING BTREE,"
            . "KEY `pkey` (`pkey`) USING BTREE,"
            . "KEY `uid` (`uid`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Awards =====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_awards}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_awards}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`squad` int(11) NOT NULL DEFAULT '0',"
            . "`date` int(20) NOT NULL DEFAULT '0',"
            . "`postdate` int(20) NOT NULL DEFAULT '0',"
            . "`event` varchar(50) NOT NULL DEFAULT '',"
            . "`place` varchar(5) NOT NULL DEFAULT '',"
            . "`prize` text,"
            . "`url` text,"
            . "PRIMARY KEY (`id`),"
            . "KEY `squad` (`squad`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Away =======================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_away}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_away}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`userid` int(11) NOT NULL DEFAULT '0',"
            . "`titel` varchar(100) NOT NULL DEFAULT '',"
            . "`reason` text,"
            . "`start` int(20) NOT NULL DEFAULT '0',"
            . "`end` int(20) NOT NULL DEFAULT '0',"
            . "`date` int(20) NOT NULL DEFAULT '0',"
            . "`lastedit` text,"
            . "PRIMARY KEY (`id`),"
            . "KEY `userid` (`userid`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Bot Liste ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_botlist}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_botlist}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`name` varchar(50) NOT NULL DEFAULT '',"
            . "`name_extra` varchar(150) NOT NULL DEFAULT '',"
            . "`regexpattern` varchar(255) NOT NULL DEFAULT '',"
            . "`type` int(1) NOT NULL DEFAULT '0',"
            . "`enabled` int(1) NOT NULL DEFAULT '1',"
            . "PRIMARY KEY (`id`),"
            . "UNIQUE KEY `regexpattern` (`regexpattern`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Captcha ====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_captcha}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_captcha}` ("
            . "`id` varchar(40) NOT NULL,"
            . "`namespace` varchar(32) NOT NULL,"
            . "`code` varchar(32) NOT NULL,"
            . "`code_display` varchar(32) NOT NULL,"
            . "`created` int(11) NOT NULL,"
            . "PRIMARY KEY (`id`,`namespace`),"
            . "KEY `created` (`created`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Clankasse ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_clankasse}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_clankasse}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(11) NOT NULL DEFAULT '0',"
            . "`member` varchar(50) NOT NULL DEFAULT '',"
            . "`transaktion` varchar(249) NOT NULL DEFAULT '',"
            . "`pm` int(1) NOT NULL DEFAULT '0',"
            . "`betrag` float NOT NULL,"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Clankasse Kategorien =======================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_clankasse_kats}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_clankasse_kats}` ("
            . "`id` int(5) NOT NULL AUTO_INCREMENT,"
            . "`kat` varchar(30) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Clankasse Bezahlt ==========================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_clankasse_payed}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_clankasse_payed}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(5) NOT NULL DEFAULT '0',"
            . "`payed` varchar(20) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Clanwars ===================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_clanwars}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_clanwars}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`squad_id` int(11) NOT NULL,"
            . "`gametype` varchar(249) NOT NULL DEFAULT '',"
            . "`gcountry` varchar(20) NOT NULL DEFAULT 'de',"
            . "`matchadmins` varchar(249) NOT NULL DEFAULT '',"
            . "`lineup` varchar(249) NOT NULL DEFAULT '',"
            . "`glineup` varchar(249) NOT NULL DEFAULT '',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`clantag` varchar(20) NOT NULL DEFAULT '',"
            . "`gegner` varchar(100) NOT NULL DEFAULT '',"
            . "`url` varchar(249) NOT NULL DEFAULT '',"
            . "`xonx` varchar(10) NOT NULL DEFAULT '',"
            . "`liga` varchar(30) NOT NULL DEFAULT '',"
            . "`punkte` int(5) NOT NULL DEFAULT '0',"
            . "`gpunkte` int(5) NOT NULL DEFAULT '0',"
            . "`maps` varchar(30) NOT NULL DEFAULT '',"
            . "`serverip` varchar(50) NOT NULL DEFAULT '',"
            . "`servername` varchar(249) NOT NULL DEFAULT '',"
            . "`serverpwd` varchar(20) NOT NULL DEFAULT '',"
            . "`bericht` text,"
            . "`top` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `squad_id` (`squad_id`) USING BTREE,"
            . "KEY `top` (`top`) USING BTREE,"
            . "KEY `datum` (`datum`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Clanwars Player ============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_clanwar_players}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_clanwar_players}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`cwid` int(5) NOT NULL DEFAULT '0',"
            . "`member` int(5) NOT NULL DEFAULT '0',"
            . "`status` int(5) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `cwid` (`cwid`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Clanwars Kommentare ========================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_cw_comments}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_cw_comments}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`cw` int(11) NOT NULL DEFAULT '0',"
            . "`nick` varchar(50) NOT NULL DEFAULT '',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`email` varchar(100) NOT NULL DEFAULT '',"
            . "`hp` varchar(50) NOT NULL DEFAULT '',"
            . "`reg` int(5) NOT NULL DEFAULT '0',"
            . "`comment` text NOT NULL,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`editby` text,"
            . "PRIMARY KEY (`id`),"
            . "KEY `cw` (`cw`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> IP Counter =================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_clicks_ips}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_clicks_ips}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`uid` int(11) NOT NULL DEFAULT '0',"
            . "`ids` int(11) NOT NULL DEFAULT '0',"
            . "`side` varchar(30) NOT NULL DEFAULT '',"
            . "`time` int(20) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `ip` (`ip`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Side Counter ===============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_counter}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_counter}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`visitors` int(20) NOT NULL DEFAULT '0',"
            . "`today` varchar(10) NOT NULL DEFAULT '0',"
            . "`maxonline` int(5) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `today` (`today`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Side Counter IPs ===========================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_counter_ips}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_counter_ips}` ("
            . "`id` int(10) NOT NULL AUTO_INCREMENT,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `ip` (`ip`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Side Counter Whoison =======================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_counter_whoison}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_counter_whoison}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`ssid` varchar(50) NOT NULL DEFAULT '',"
            . "`online` int(20) NOT NULL DEFAULT '0',"
            . "`whereami` text,"
            . "`login` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "UNIQUE KEY `ip` (`ip`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Downloads ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_downloads}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_downloads}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`download` varchar(249) NOT NULL DEFAULT '',"
            . "`url` varchar(249) NOT NULL DEFAULT '',"
            . "`beschreibung` text,"
            . "`hits` int(20) NOT NULL DEFAULT '0',"
            . "`kat` int(5) NOT NULL DEFAULT '0',"
            . "`date` int(20) NOT NULL DEFAULT '0',"
            . "`last_dl` int(20) NOT NULL DEFAULT '0',"
            . "`intern` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Download Kategorien ========================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_download_kat}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_download_kat}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`name` varchar(249) NOT NULL DEFAULT '', "
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Kalender Events ============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_events}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_events}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`title` varchar(30) NOT NULL DEFAULT '',"
            . "`event` text,"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Forum Kategorien ===========================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_forumkats}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_forumkats}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`kid` int(10) NOT NULL DEFAULT '0',"
            . "`name` varchar(50) NOT NULL DEFAULT '',"
            . "`intern` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    
    //===============================================================
    //-> Forum Posts ================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_forumposts}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_forumposts}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`kid` int(2) NOT NULL DEFAULT '0',"
            . "`sid` int(2) NOT NULL DEFAULT '0',"
            . "`date` int(20) NOT NULL DEFAULT '0',"
            . "`nick` varchar(50) NOT NULL DEFAULT '',"
            . "`reg` int(1) NOT NULL DEFAULT '0',"
            . "`email` varchar(100) NOT NULL DEFAULT '',"
            . "`text` text,`edited` text,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`hp` varchar(249) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`),"
            . "KEY `sid` (`sid`) USING BTREE,"
            . "KEY `date` (`date`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Forum Sub-Kategorien =======================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_forumsubkats}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_forumsubkats}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`sid` int(10) NOT NULL DEFAULT '0',"
            . "`kattopic` varchar(150) NOT NULL DEFAULT '',"
            . "`subtopic` varchar(150) NOT NULL DEFAULT '',"
            . "`pos` int(5) NOT NULL,"
            . "PRIMARY KEY (`id`),"
            . "KEY `sid` (`sid`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Forum Threads ==============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_forumthreads}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_forumthreads}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`kid` int(10) NOT NULL DEFAULT '0',"
            . "`t_date` int(20) NOT NULL DEFAULT '0',"
            . "`topic` varchar(249) NOT NULL DEFAULT '',"
            . "`subtopic` varchar(100) NOT NULL DEFAULT '',"
            . "`t_nick` varchar(50) NOT NULL DEFAULT '',"
            . "`t_reg` int(11) NOT NULL DEFAULT '0',"
            . "`t_email` varchar(100) NOT NULL DEFAULT '',"
            . "`t_text` text,`hits` int(10) NOT NULL DEFAULT '0',"
            . "`first` int(1) NOT NULL DEFAULT '0',"
            . "`lp` int(20) NOT NULL DEFAULT '0',"
            . "`sticky` int(1) NOT NULL DEFAULT '0',"
            . "`closed` int(1) NOT NULL DEFAULT '0',"
            . "`global` int(1) NOT NULL DEFAULT '0',"
            . "`edited` text,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`t_hp` varchar(249) NOT NULL DEFAULT '',"
            . "`vote` varchar(10) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`),"
            . "KEY `kid` (`kid`) USING BTREE,"
            . "KEY `lp` (`lp`) USING BTREE,"
            . "KEY `topic` (`topic`) USING BTREE,"
            . "KEY `first` (`first`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Forum ABO ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_f_abo}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_f_abo}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`fid` int(10) NOT NULL,"
            . "`datum` int(20) NOT NULL,"
            . "`user` int(5) NOT NULL,"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Forum Access ===============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_f_access}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_f_access}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(10) NOT NULL DEFAULT '0',"
            . "`pos` int(5) NOT NULL DEFAULT '0',"
            . "`forum` int(10) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `user` (`user`) USING BTREE,"
            . "KEY `forum` (`forum`) USING BTREE,"
            . "KEY `pos` (`pos`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Gallery ====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_gallery}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_gallery}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`kat` varchar(200) NOT NULL DEFAULT '',"
            . "`beschreibung` text,"
            . "`intern` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Guestbook ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_gb}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_gb}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`nick` varchar(50) NOT NULL DEFAULT '',"
            . "`email` varchar(100) NOT NULL DEFAULT '',"
            . "`hp` varchar(249) NOT NULL DEFAULT '',"
            . "`reg` int(1) NOT NULL DEFAULT '0',"
            . "`nachricht` text,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`editby` text,"
            . "`public` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Guestbook Kommentare =======================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_gbcomments}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_gbcomments}` ("
            . "`id` int(10) NOT NULL AUTO_INCREMENT,"
            . "`gbe` int(10) NOT NULL DEFAULT '0',"
            . "`nick` varchar(50) NOT NULL DEFAULT '',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`email` varchar(130) NOT NULL DEFAULT '',"
            . "`hp` varchar(50) NOT NULL DEFAULT '',"
            . "`reg` int(5) NOT NULL DEFAULT '0',"
            . "`comment` text NOT NULL,"
            . "`ip` varchar(50) NOT NULL DEFAULT '',"
            . "`editby` text,"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Glossar ====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_glossar}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_glossar}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`word` varchar(200) NOT NULL DEFAULT '',"
            . "`glossar` text,"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> IPban ======================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_ipban}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_ipban}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`time` int(11) NOT NULL DEFAULT '0',"
            . "`data` text,"
            . "`typ` int(1) NOT NULL DEFAULT '0',"
            . "`enable` int(1) NOT NULL DEFAULT '1',"
            . "PRIMARY KEY (`id`),"
            . "KEY `ip` (`ip`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> IPcheck ====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_ipcheck}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_ipcheck}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`user_id` int(11) NOT NULL DEFAULT '0',"
            . "`what` varchar(40) NOT NULL DEFAULT '',"
            . "`time` int(20) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `ip` (`ip`) USING BTREE,"
            . "KEY `what` (`what`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> IPtoDNS ====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_iptodns}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_iptodns}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`sessid` varchar(50) NOT NULL DEFAULT '',"
            . "`time` int(11) NOT NULL DEFAULT '0',"
            . "`update` int(11) NOT NULL DEFAULT '0',"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`dns` varchar(200) NOT NULL DEFAULT '',"
            . "`agent` varchar(250) NOT NULL DEFAULT '',"
            . "`bot` int(1) NOT NULL DEFAULT '0',"
            . "`bot_name` varchar(250) NOT NULL DEFAULT '',"
            . "`bot_fullname` varchar(250) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`),"
            . "KEY `sessid` (`sessid`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Links ======================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_links}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_links}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`url` varchar(249) NOT NULL DEFAULT '',"
            . "`text` varchar(249) NOT NULL DEFAULT '',"
            . "`banner` int(1) NOT NULL DEFAULT '0',"
            . "`beschreibung` text,"
            . "`hits` int(11) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Linkus =====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_linkus}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_linkus}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`url` varchar(249) NOT NULL DEFAULT '',"
            . "`text` varchar(249) NOT NULL DEFAULT '',"
            . "`banner` int(1) NOT NULL DEFAULT '0',"
            . "`beschreibung` varchar(249) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Messages ===================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_messages}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_messages}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`von` int(5) NOT NULL DEFAULT '0',"
            . "`an` int(5) NOT NULL DEFAULT '0',"
            . "`see_u` int(1) NOT NULL DEFAULT '0',"
            . "`page` int(1) NOT NULL DEFAULT '0',"
            . "`titel` varchar(80) NOT NULL DEFAULT '',"
            . "`nachricht` text,"
            . "`see` int(1) NOT NULL DEFAULT '0',"
            . "`readed` int(1) NOT NULL DEFAULT '0',"
            . "`sendmail` int(1) DEFAULT '0',"
            . "`sendnews` int(1) NOT NULL DEFAULT '0',"
            . "`senduser` int(5) NOT NULL DEFAULT '0',"
            . "`sendnewsuser` int(5) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `an` (`an`) USING BTREE,"
            . "KEY `page` (`page`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Navigation =================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_navi}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_navi}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`pos` int(20) NOT NULL DEFAULT '0',"
            . "`kat` varchar(20) NOT NULL DEFAULT '',"
            . "`shown` int(1) NOT NULL DEFAULT '0',"
            . "`name` varchar(249) NOT NULL DEFAULT '',"
            . "`url` varchar(249) NOT NULL DEFAULT '',"
            . "`target` int(1) NOT NULL DEFAULT '0',"
            . "`type` int(1) NOT NULL DEFAULT '0',"
            . "`internal` int(1) NOT NULL DEFAULT '0',"
            . "`wichtig` int(1) NOT NULL DEFAULT '0',"
            . "`editor` int(10) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `url` (`url`) USING BTREE,"
            . "KEY `kat` (`kat`) USING BTREE,"
            . "KEY `shown` (`shown`) USING BTREE,"
            . "KEY `pos` (`pos`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Navigation Kategorien ======================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_navi_kats}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_navi_kats}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`name` varchar(200) NOT NULL DEFAULT '',"
            . "`placeholder` varchar(200) NOT NULL DEFAULT '',"
            . "`level` int(2) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `placeholder` (`placeholder`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> News =======================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_news}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_news}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`autor` int(5) NOT NULL DEFAULT '0',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`kat` int(2) NOT NULL DEFAULT '0',"
            . "`sticky` int(20) NOT NULL DEFAULT '0',"
            . "`titel` varchar(249) NOT NULL DEFAULT '',"
            . "`intern` int(1) NOT NULL DEFAULT '0',"
            . "`text` text,"
            . "`klapplink` varchar(20) NOT NULL DEFAULT '',"
            . "`klapptext` text,"
            . "`link1` varchar(100) NOT NULL DEFAULT '',"
            . "`url1` varchar(200) NOT NULL DEFAULT '',"
            . "`link2` varchar(100) NOT NULL DEFAULT '',"
            . "`url2` varchar(200) NOT NULL DEFAULT '',"
            . "`link3` varchar(100) NOT NULL DEFAULT '',"
            . "`url3` varchar(200) NOT NULL DEFAULT '',"
            . "`viewed` int(10) NOT NULL DEFAULT '0',"
            . "`public` int(1) NOT NULL DEFAULT '0',"
            . "`timeshift` int(14) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> News Kommentare ============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_newscomments}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_newscomments}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`news` int(10) NOT NULL DEFAULT '0',"
            . "`nick` varchar(50) NOT NULL DEFAULT '',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`email` varchar(100) NOT NULL DEFAULT '',"
            . "`hp` varchar(50) NOT NULL DEFAULT '',"
            . "`reg` int(5) NOT NULL DEFAULT '0',"
            . "`comment` text,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`editby` text,PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> News Kategorien ============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_newskat}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_newskat}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`katimg` varchar(200) NOT NULL DEFAULT '',"
            . "`kategorie` varchar(60) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Partners ===================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_partners}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_partners}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`link` varchar(150) NOT NULL DEFAULT '',"
            . "`banner` varchar(100) NOT NULL DEFAULT '',"
            . "`textlink` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Permissions ================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_permissions}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_permissions}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(5) NOT NULL DEFAULT '0',"
            . "`pos` int(1) NOT NULL DEFAULT '0',"
            . "`positions` int(1) NOT NULL DEFAULT '0',"
            . "`intforum` int(1) NOT NULL DEFAULT '0',"
            . "`clankasse` int(1) NOT NULL DEFAULT '0',"
            . "`clanwars` int(1) NOT NULL DEFAULT '0',"
            . "`clear` int(1) NOT NULL DEFAULT '0',"
            . "`config` int(1) DEFAULT '0',"
            . "`shoutbox` int(1) NOT NULL DEFAULT '0',"
            . "`serverliste` int(1) NOT NULL DEFAULT '0',"
            . "`editusers` int(1) NOT NULL DEFAULT '0',"
            . "`editsquads` int(1) NOT NULL DEFAULT '0',"
            . "`editserver` int(1) NOT NULL DEFAULT '0',"
            . "`editkalender` int(1) NOT NULL DEFAULT '0',"
            . "`news` int(1) NOT NULL DEFAULT '0',"
            . "`gb` int(1) NOT NULL DEFAULT '0',"
            . "`partners` int(1) NOT NULL DEFAULT '0',"
            . "`profile` int(1) NOT NULL DEFAULT '0',"
            . "`protocol` int(1) NOT NULL DEFAULT '0',"
            . "`forum` int(1) NOT NULL DEFAULT '0',"
            . "`forumkats` int(1) NOT NULL DEFAULT '0',"
            . "`votes` int(1) NOT NULL DEFAULT '0',"
            . "`gallery` int(1) NOT NULL DEFAULT '0',"
            . "`votesadmin` int(1) NOT NULL DEFAULT '0',"
            . "`links` int(1) NOT NULL DEFAULT '0',"
            . "`downloads` int(1) NOT NULL DEFAULT '0',"
            . "`newsletter` int(1) NOT NULL DEFAULT '0',"
            . "`intnews` int(1) NOT NULL DEFAULT '0',"
            . "`impressum` int(1) NOT NULL DEFAULT '0',"
            . "`rankings` int(1) NOT NULL DEFAULT '0',"
            . "`contact` int(1) NOT NULL DEFAULT '0',"
            . "`joinus` int(1) NOT NULL DEFAULT '0',"
            . "`awards` int(1) NOT NULL DEFAULT '0',"
            . "`artikel` int(1) NOT NULL DEFAULT '0',"
            . "`backup` int(1) NOT NULL DEFAULT '0',"
            . "`receivecws` int(1) NOT NULL DEFAULT '0',"
            . "`editor` int(1) NOT NULL DEFAULT '0',"
            . "`glossar` int(1) NOT NULL DEFAULT '0',"
            . "`gs_showpw` int(1) NOT NULL DEFAULT '0',"
            . "`slideshow` int(1) NOT NULL DEFAULT '0',"
            . "`smileys` int(1) NOT NULL DEFAULT '0',"
            . "`support` int(1) NOT NULL DEFAULT '0',"
            . "`galleryintern` int(1) NOT NULL DEFAULT '0',"
            . "`dlintern` int(1) NOT NULL DEFAULT '0',"
            . "`ipban` int(1) NOT NULL DEFAULT '0',"
            . "`startpage` int(1) NOT NULL DEFAULT '0',"
            . "`security` int(1) NOT NULL,"
            . "`teamspeak` int(1) NOT NULL,"
            . "PRIMARY KEY (`id`),"
            . "KEY `user` (`user`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Positions ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_positions}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_positions}` ("
            . "`id` int(2) NOT NULL AUTO_INCREMENT,"
            . "`pid` int(2) NOT NULL DEFAULT '0',"
            . "`position` varchar(50) NOT NULL DEFAULT '',"
            . "`nletter` int(1) NOT NULL DEFAULT '0',"
            . "`color` varchar(7) NOT NULL DEFAULT '#000000',"
            . "PRIMARY KEY (`id`),"
            . "KEY `pid` (`pid`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Rankings ===================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_rankings}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_rankings}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`league` varchar(50) NOT NULL DEFAULT '',"
            . "`lastranking` int(10) NOT NULL DEFAULT '0',"
            . "`rank` int(10) NOT NULL DEFAULT '0',"
            . "`squad` int(5) NOT NULL DEFAULT '0',"
            . "`url` varchar(249) NOT NULL DEFAULT '',"
            . "`postdate` int(20) NOT NULL,"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Server =====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_server}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_server}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`shown` int(1) NOT NULL DEFAULT '1',"
            . "`navi` int(1) NOT NULL DEFAULT '0',"
            . "`name` varchar(100) NOT NULL DEFAULT '',"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`port` int(10) NOT NULL DEFAULT '0',"
            . "`pwd` varchar(40) NOT NULL DEFAULT '',"
            . "`game` varchar(30) NOT NULL DEFAULT '',"
            . "`qport` varchar(10) NOT NULL DEFAULT '',"
            . "`custom_icon` varchar(100) NOT NULL DEFAULT '',"
            . "`icon` varchar(150) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Serverliste ================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_serverliste}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_serverliste}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`clanname` varchar(200) NOT NULL DEFAULT '',"
            . "`clanurl` varchar(255) NOT NULL DEFAULT '',"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`port` int(10) NOT NULL DEFAULT '0',"
            . "`pwd` varchar(40) NOT NULL DEFAULT '',"
            . "`checked` int(1) NOT NULL DEFAULT '0',"
            . "`slots` char(11) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Sessions ===================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_sessions}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_sessions}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`ssid` varchar(200) NOT NULL DEFAULT '',"
            . "`time` int(11) NOT NULL DEFAULT '0',"
            . "`data` blob,"
            . "PRIMARY KEY (`id`),"
            . "KEY `ssid` (`ssid`) USING BTREE,"
            . "KEY `time` (`time`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Settings ===================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_settings}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_settings}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`key` varchar(200) NOT NULL DEFAULT '',"
            . "`value` text,"
            . "`default` text,"
            . "`length` int(11) NOT NULL DEFAULT '1',"
            . "`type` varchar(20) NOT NULL DEFAULT 'int' COMMENT 'int/string',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> Shoutbox ===================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_shoutbox}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_shoutbox}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(30) NOT NULL DEFAULT '0',"
            . "`nick` varchar(30) NOT NULL DEFAULT '',"
            . "`email` varchar(130) NOT NULL DEFAULT '',"
            . "`text` text,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Sites ======================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_sites}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_sites}` ("
            . "`id` int(5) NOT NULL AUTO_INCREMENT,"
            . "`titel` text,"
            . "`text` text,"
            . "`html` int(1) NOT NULL,"
            . "`php` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Slideshow ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_slideshow}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_slideshow}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`pos` int(5) NOT NULL DEFAULT '0',"
            . "`bez` varchar(200) NOT NULL DEFAULT '',"
            . "`showbez` int(1) NOT NULL DEFAULT '1',"
            . "`desc` varchar(249) NOT NULL DEFAULT '',"
            . "`url` varchar(200) NOT NULL DEFAULT '',"
            . "`target` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Sponsoren ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_sponsoren}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_sponsoren}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`name` varchar(249) NOT NULL DEFAULT '',"
            . "`link` varchar(249) NOT NULL DEFAULT '',"
            . "`beschreibung` text,"
            . "`site` int(1) NOT NULL DEFAULT '0',"
            . "`slink` varchar(249) NOT NULL DEFAULT '',"
            . "`banner` int(1) NOT NULL DEFAULT '0',"
            . "`bend` varchar(5) NOT NULL DEFAULT '',"
            . "`blink` varchar(249) NOT NULL DEFAULT '',"
            . "`box` int(1) NOT NULL DEFAULT '0',"
            . "`xend` varchar(5) NOT NULL DEFAULT '',"
            . "`xlink` varchar(255) NOT NULL DEFAULT '',"
            . "`pos` int(5) NOT NULL,"
            . "`hits` int(11) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `banner` (`banner`) USING BTREE,"
            . "KEY `pos` (`pos`) USING BTREE,"
            . "KEY `site` (`site`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Squads =====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_squads}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_squads}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`name` varchar(40) NOT NULL DEFAULT '',"
            . "`game` varchar(40) NOT NULL DEFAULT '',"
            . "`icon` varchar(20) NOT NULL DEFAULT '',"
            . "`pos` int(1) NOT NULL DEFAULT '0',"
            . "`shown` int(1) NOT NULL DEFAULT '0',"
            . "`navi` int(1) NOT NULL DEFAULT '1',"
            . "`status` int(1) NOT NULL DEFAULT '1',"
            . "`beschreibung` text,"
            . "`team_show` int(1) NOT NULL DEFAULT '1',"
            . "`team_joinus` int(1) NOT NULL DEFAULT '1',"
            . "`team_fightus` int(1) NOT NULL DEFAULT '1',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Squad Users ================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_squaduser}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_squaduser}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(5) NOT NULL DEFAULT '0',"
            . "`squad` int(2) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Startpage ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_startpage}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_startpage}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`name` varchar(200) NOT NULL,"
            . "`url` varchar(200) NOT NULL,"
            . "`level` int(1) NOT NULL DEFAULT '1',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Teamspeak ==================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_teamspeak}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_teamspeak}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`host_ip_dns` varchar(200) NOT NULL DEFAULT '',"
            . "`server_port` int(8) NOT NULL DEFAULT '9987',"
            . "`query_port` int(8) NOT NULL DEFAULT '10011',"
            . "`file_port` int(8) NOT NULL DEFAULT '30033',"
            . "`username` varchar(100) NOT NULL DEFAULT '',"
            . "`passwort` varchar(100) NOT NULL DEFAULT '',"
            . "`customicon` int(1) NOT NULL DEFAULT '1',"
            . "`showchannel` int(1) NOT NULL DEFAULT '0',"
            . "`default_server` int(1) NOT NULL DEFAULT '0',"
            . "`show_navi` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> User Buddys ================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_userbuddys}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_userbuddys}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(5) NOT NULL DEFAULT '0',"
            . "`buddy` int(5) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `user` (`user`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> User Profile ===============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_profile}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_profile}` ("
            . "`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,"
            . "`kid` int(11) NOT NULL DEFAULT '0',"
            . "`name` varchar(200) NOT NULL DEFAULT '',"
            . "`feldname` varchar(30) NOT NULL DEFAULT '',"
            . "`type` int(5) NOT NULL DEFAULT '1',"
            . "`shown` int(5) NOT NULL DEFAULT '1',"
            . "PRIMARY KEY (`id`),"
            . "KEY `kid` (`kid`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> User Gallery ===============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_usergallery}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_usergallery}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(5) NOT NULL DEFAULT '0',"
            . "`beschreibung` text,"
            . "`pic` varchar(200) NOT NULL DEFAULT '',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> User GB ====================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_usergb}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_usergb}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(5) NOT NULL DEFAULT '0',"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`nick` varchar(30) NOT NULL DEFAULT '',"
            . "`email` varchar(130) NOT NULL DEFAULT '',"
            . "`hp` varchar(100) NOT NULL DEFAULT '',"
            . "`reg` int(1) NOT NULL DEFAULT '0',"
            . "`nachricht` text,"
            . "`ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',"
            . "`editby` text,"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> User Position ==============================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_userposis}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_userposis}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(5) NOT NULL DEFAULT '0',"
            . "`posi` int(5) NOT NULL DEFAULT '0',"
            . "`squad` int(5) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `user` (`user`) USING BTREE,"
            . "KEY `squad` (`squad`) USING BTREE,"
            . "KEY `posi` (`posi`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");

    //===============================================================
    //-> User Stats =================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_userstats}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_userstats}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`user` int(10) NOT NULL DEFAULT '0',"
            . "`logins` int(11) NOT NULL DEFAULT '0',"
            . "`writtenmsg` int(10) NOT NULL DEFAULT '0',"
            . "`lastvisit` int(20) NOT NULL DEFAULT '0',"
            . "`hits` int(11) NOT NULL DEFAULT '0',"
            . "`votes` int(5) NOT NULL DEFAULT '0',"
            . "`profilhits` int(20) NOT NULL DEFAULT '0',"
            . "`forumposts` int(5) NOT NULL DEFAULT '0',"
            . "`cws` int(5) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `user` (`user`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> User =======================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_users}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_users}` ("
            . "`id` int(5) NOT NULL AUTO_INCREMENT,"
            . "`user` varchar(200) NOT NULL DEFAULT '',"
            . "`nick` varchar(200) NOT NULL DEFAULT '',"
            . "`pwd` varchar(255) NOT NULL DEFAULT '',"
            . "`pwd_encoder` int(1) NOT NULL DEFAULT '0',"
            . "`sessid` varchar(32) NOT NULL DEFAULT '',"
            . "`country` varchar(20) NOT NULL DEFAULT 'de',"
            . "`ip` varchar(50) NOT NULL DEFAULT '',"
            . "`regdatum` int(20) NOT NULL DEFAULT '0',"
            . "`email` varchar(200) NOT NULL DEFAULT '',"
            . "`icq` varchar(20) NOT NULL DEFAULT '',"
            . "`hlswid` varchar(100) NOT NULL DEFAULT '',"
            . "`steamid` varchar(20) NOT NULL DEFAULT '',"
            . "`battlenetid` varchar(100) NOT NULL DEFAULT '',"
            . "`originid` varchar(100) NOT NULL DEFAULT '',"
            . "`skypename` varchar(100) NOT NULL DEFAULT '',"
            . "`psnid` varchar(100) NOT NULL DEFAULT '',"
            . "`xboxid` varchar(100) NOT NULL DEFAULT '',"
            . "`level` int(2) NOT NULL DEFAULT '0',"
            . "`banned` int(1) NOT NULL DEFAULT '0',"
            . "`rlname` varchar(200) NOT NULL DEFAULT '',"
            . "`city` varchar(200) NOT NULL DEFAULT '',"
            . "`sex` int(1) NOT NULL DEFAULT '0',"
            . "`bday` int(11) NOT NULL DEFAULT '0',"
            . "`hobbys` varchar(249) NOT NULL DEFAULT '',"
            . "`motto` varchar(249) NOT NULL DEFAULT '',"
            . "`hp` varchar(200) NOT NULL DEFAULT '',"
            . "`cpu` varchar(200) NOT NULL DEFAULT '',"
            . "`ram` varchar(200) NOT NULL DEFAULT '',"
            . "`monitor` varchar(200) NOT NULL DEFAULT '',"
            . "`maus` varchar(200) NOT NULL DEFAULT '',"
            . "`mauspad` varchar(200) NOT NULL DEFAULT '',"
            . "`headset` varchar(200) NOT NULL DEFAULT '',"
            . "`board` varchar(200) NOT NULL DEFAULT '',"
            . "`os` varchar(200) NOT NULL DEFAULT '',"
            . "`graka` varchar(200) NOT NULL DEFAULT '',"
            . "`hdd` varchar(200) NOT NULL DEFAULT '',"
            . "`inet` varchar(200) NOT NULL DEFAULT '',"
            . "`signatur` text,"
            . "`position` int(2) NOT NULL DEFAULT '0',"
            . "`status` int(1) NOT NULL DEFAULT '1',"
            . "`ex` varchar(200) NOT NULL DEFAULT '',"
            . "`job` varchar(200) NOT NULL DEFAULT '',"
            . "`time` int(20) NOT NULL DEFAULT '0',"
            . "`listck` int(1) NOT NULL DEFAULT '0',"
            . "`online` int(1) NOT NULL DEFAULT '0',"
            . "`nletter` int(1) NOT NULL DEFAULT '1',"
            . "`whereami` text,"
            . "`drink` varchar(249) NOT NULL DEFAULT '',"
            . "`essen` varchar(249) NOT NULL DEFAULT '',"
            . "`film` varchar(249) NOT NULL DEFAULT '',"
            . "`musik` varchar(249) NOT NULL DEFAULT '',"
            . "`song` varchar(249) NOT NULL DEFAULT '',"
            . "`buch` varchar(249) NOT NULL DEFAULT '',"
            . "`autor` varchar(249) NOT NULL DEFAULT '',"
            . "`person` varchar(249) NOT NULL DEFAULT '',"
            . "`sport` varchar(249) NOT NULL DEFAULT '',"
            . "`sportler` varchar(249) NOT NULL DEFAULT '',"
            . "`auto` varchar(249) NOT NULL DEFAULT '',"
            . "`game` varchar(249) NOT NULL DEFAULT '',"
            . "`favoclan` varchar(249) NOT NULL DEFAULT '',"
            . "`spieler` varchar(249) NOT NULL DEFAULT '',"
            . "`map` varchar(249) NOT NULL DEFAULT '',"
            . "`waffe` varchar(249) NOT NULL DEFAULT '',"
            . "`rasse` varchar(249) NOT NULL DEFAULT '',"
            . "`url2` varchar(249) NOT NULL DEFAULT '',"
            . "`url3` varchar(249) NOT NULL DEFAULT '',"
            . "`beschreibung` text,"
            . "`gmaps_koord` varchar(249) NOT NULL DEFAULT '',"
            . "`pnmail` int(1) NOT NULL DEFAULT '1',"
            . "`perm_gallery` int(1) NOT NULL DEFAULT '0',"
            . "`perm_gb` int(1) NOT NULL DEFAULT '1',"
            . "`profile_access` int(1) NOT NULL DEFAULT '0',"
            . "`startpage` int(11) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `pwd` (`pwd`) USING BTREE,"
            . "KEY `time` (`time`) USING BTREE,"
            . "KEY `bday` (`bday`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Votes ======================================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_votes}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_votes}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`datum` int(20) NOT NULL DEFAULT '0',"
            . "`titel` varchar(249) NOT NULL DEFAULT '',"
            . "`intern` int(1) NOT NULL DEFAULT '0',"
            . "`menu` int(1) NOT NULL DEFAULT '0',"
            . "`closed` int(1) NOT NULL DEFAULT '0',"
            . "`von` int(10) NOT NULL DEFAULT '0',"
            . "`forum` int(1) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`)"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    //===============================================================
    //-> Votes Ergebnisse ===========================================
    //===============================================================
    $sql->delete("DROP TABLE IF EXISTS `{prefix_vote_results}`;");
    $sql->create("CREATE TABLE IF NOT EXISTS `{prefix_vote_results}` ("
            . "`id` int(11) NOT NULL AUTO_INCREMENT,"
            . "`vid` int(5) NOT NULL DEFAULT '0',"
            . "`what` varchar(5) NOT NULL DEFAULT '',"
            . "`sel` varchar(80) NOT NULL DEFAULT '',"
            . "`stimmen` int(5) NOT NULL DEFAULT '0',"
            . "PRIMARY KEY (`id`),"
            . "KEY `vid` (`vid`) USING BTREE,"
            . "KEY `what` (`what`) USING BTREE"
            . ") {engine}DEFAULT CHARSET=utf8;");
    
    $sql->disconnect();
}