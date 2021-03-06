<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

/**
 * Pruft online ob DZCP aktuell ist.
 * @return array
 */
function show_dzcp_version() {
    global $cache,$config_cache;
    $dzcp_version_info = 'onmouseover="DZCP.showInfo(\'<tr><td colspan=2 align=center padding=3 class=infoTop>DZCP Versions Checker</td></tr><tr><td>'._dzcp_vcheck.'</td></tr>\')" onmouseout="DZCP.hideInfo()"';
    $return = array();
    if(dzcp_version_checker || allow_url_fopen_support()) {
        if(!$config_cache['use_cache'] || !$cache->isExisting('dzcp_version')) {
			$input = json_encode(array('event' => 'version', 'dzcp' => _version, 'edition' => _edition, 'type' => 'xml');
            if($dzcp_online_v = get_external_contents('http://www.dzcp.de/api.php?input='.$input))
        } else
            $dzcp_online_v = $cache->get('dzcp_version');
		unset($input);

        if($dzcp_online_v && !empty($dzcp_online_v) && strpos($dzcp_online_v, 'not found') === false) {
            $xml = simplexml_load_string($dzcp_online_v, 'SimpleXMLElement', LIBXML_NOCDATA);
			if(empty($xml) || is_bool($xml) || !is_object($xml)) {
				$return['version'] = '<b>'._akt_version.': <a href="" [info]><font color="#FFFF00">'._version.'</font></a> / Release: '._release.' / Build: '._build.'</b>';
				$return['version'] = show($return['version'],array('info' => $dzcp_version_info));
				$return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
				return $return;
			}
			
			$xml = SteamAPI::objectToArray($xml);
			if(empty($xml) || is_bool($xml) || !is_array($xml)) {
				$return['version'] = '<b>'._akt_version.': <a href="" [info]><font color="#FFFF00">'._version.'</font></a> / Release: '._release.' / Build: '._build.'</b>';
				$return['version'] = show($return['version'],array('info' => $dzcp_version_info));
				$return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
				return $return;
			}
			
			if(strtolower($xml['edition']) != strtolower(_edition)) {
				$return['version'] = '<b>'._akt_version.': <a href="" [info]><font color="#FFFF00">'._version.'</font></a> / Release: '._release.' / Build: '._build.'</b>';
				$return['version'] = show($return['version'],array('info' => $dzcp_version_info));
				$return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
				return $return;
			}
			
			if($config_cache['use_cache'])
                    $cache->set('dzcp_version', $dzcp_online_v, dzcp_version_checker_refresh);
			unset($dzcp_online_v);
			
			$_build = _build;
            if($xml['build'] > _build) $_build = '<font color="#FF0000">'._build.'</font> => <font color="#00FF00">'.$xml['build'].'</font>';

            if($xml['version'] <= _version) {
                $return['version'] = '<b>'._akt_version.': <a href="" [info]><span class="fontGreen">'._version.'</span></a> / Release: '._release.' / Build: '.$_build.'</b>';
                $return['version'] = show($return['version'],array('info' => $dzcp_version_info));
                $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
            } else {
                $return['version'] = '<a href="http://www.dzcp.de/" target="_blank" title="external Link: www.dzcp.de"><b>'._akt_version.':</b> <span class="fontRed">'._version.'</span> / Update Version: <span class="fontGreen">'.$xml['version'].'</span></a> / Release: <span class="fontGreen">'.$xml['release'].'</span> / Build: <span class="fontGreen">'.$xml['build'].'</span>';
                $return['version_img'] = '<img src="../inc/images/admin/version_old.gif" align="absmiddle" width="111" height="14" />';
            }
        } else {
            $return['version'] = '<b>'._akt_version.': <a href="" [info]><font color="#FFFF00">'._version.'</font></a> / Release: '._release.' / Build: '._build.'</b>';
            $return['version'] = show($return['version'],array('info' => $dzcp_version_info));
            $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
        }
    } else {
        //check disabled
        $return['version'] = '<b><font color="#999999">'._akt_version.': '._version.'</font> / Release: '._release.' / Build: '._build.'</b>';
        $return['version_img'] = '<img src="../inc/images/admin/version.gif" align="absmiddle" width="111" height="14" />';
    }

    return $return;
}

//PHPInfo in ein Array einlesen
function parsePHPInfo() {
    ob_start();
    phpinfo();
    $s = ob_get_contents();
    ob_end_clean();

    $s = strip_tags($s,'<h2><th><td>');
    $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);
    $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);
    $vTmp = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);
    $vModules = array();
    for ($i=1;$i<count($vTmp);$i++) {
        if(preg_match('/<h2[^>]*>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {
            $vName = trim($vMat[1]);
            $vTmp2 = explode("\n",$vTmp[$i+1]);
            foreach ($vTmp2 AS $vOne) {
                $vPat = '<info>([^<]+)<\/info>';
                $vPat3 = "/$vPat\s*$vPat\s*$vPat/";
                $vPat2 = "/$vPat\s*$vPat/";

                if(preg_match($vPat3,$vOne,$vMat))
                    $vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
                else if(preg_match($vPat2,$vOne,$vMat))
                    $vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
            }
        }
    }

    return $vModules;
}

function php_sapi_type() {
    $sapi_type = php_sapi_name();
    $sapi_types = array("apache" => 'Apache HTTP Server', "apache2filter" => 'Apache 2: Filter',
            "apache2handler" => 'Apache 2: Handler', "cgi" => 'CGI', "cgi-fcgi" => 'Fast-CGI', "cli" => 'CLI', "isapi" => 'ISAPI', "nsapi" => 'NSAPI');
    return(empty($sapi_types[substr($sapi_type, 0, 3)]) ? substr($sapi_type, 0, 3) : $sapi_types[substr($sapi_type, 0, 3)]);
}

/**
* Gibt eine Liste der Live Games aus
* @return string/options
*/
function listgames($game = '') {
    $protocols_array = GameQ::getGames(); $games = '';
    $block = array('teamspeak3','gamespy','gamespy2','gamespy3','source');
    foreach ($protocols_array AS $gameq => $info) {
        if(in_array($gameq,$block)) { continue; }
        $selected = (!empty($game) && $game != false && $game == $gameq ? 'selected="selected" ' : '');
        $games .= '<option '.$selected.'value="'.$gameq.'">'.htmlentities($info['name']).'</option>';
    }

    return $games;
}

function sql_backup() {
    global $sql;
    $backup_table_data = array();

    //Table Drop
    $sqlqry = $sql->show('SHOW TABLE STATUS;');
    foreach($sqlqry as $table) {
        $backup_table_data[$table['Name']]['drop'] = 'DROP TABLE IF EXISTS `'.$table['Name'].'`;'; 
    }
    unset($table);

    //Table Create
    foreach($backup_table_data as $table => $null) {
        unset($null);
        $sqlqry = $sql->show('SHOW CREATE TABLE '.$table.';');
        foreach($sqlqry as $table) { 
            $backup_table_data[$table['Table']]['create'] = $table['Create Table'].';'; 
        }
    }
    unset($table);

    //Insert Create
    foreach($backup_table_data as $table => $null) {
        unset($null); $backup = '';
        $sqlqry = $sql->select('SELECT * FROM '.$table.' ;');
        foreach($sqlqry as $dt) {
            if(!empty($dt)) {
                $backup_data = '';
                foreach ($dt as $key => $var) { 
                    $backup_data .= "`".$key."` = '".((string)(str_replace("'", "`", $var)))."',"; 
                }

                $backup .= "INSERT INTO `".$table."` SET ".substr($backup_data, 0, -1).";\r\n";
                unset($backup_data);
            }
        }

        $backup_table_data[$table]['insert'] = $backup;
        unset($backup);
    }
    unset($table);

    $sql_backup =  "-- -------------------------------------------------------------------\r\n";
    $sql_backup .= "-- Datenbank Backup von deV!L`z Clanportal v."._version."\r\n";
    $sql_backup .= "-- Build: "._release." * "._build."\r\n";
    $sql_backup .= "-- Host: ".$sql->getConfig('db_host','default')."\r\n";
    $sql_backup .= "-- Erstellt am: ".date("d.m.Y")." um ".date("H:i")."\r\n";
    $sql_backup .= "-- MySQL-Version: ".$sql->fetch("SELECT VERSION() as mysql_version",array(),'mysql_version')."\r\n";
    $sql_backup .= "-- PHP Version: ".phpversion()."\r\n";
    $sql_backup .= "-- -------------------------------------------------------------------\r\n\r\n";
    $sql_backup .= "--\r\n-- Datenbank: `".$sql->getConfig('db','default')."`\r\n--\n\n";
    $sql_backup .= "-- -------------------------------------------------------------------\r\n";
    foreach($backup_table_data as $table => $data) {
        $sql_backup .= "\r\n--\r\n-- Tabellenstruktur: `".$table."`\r\n--\r\n\r\n";
        $sql_backup .= $data['drop']."\r\n";
        $sql_backup .= $data['create']."\r\n";

        if(!empty($data['insert'])) {
            $sql_backup .= "\r\n--\r\n-- Datenstruktur: `".$table."`\r\n--\r\n\r\n";
            $sql_backup .= $data['insert']."\r\n";
        }
    }

    unset($data);
    return $sql_backup;
}

function bbcode_nletter($txt) {
    $txt = nl2br(trim(stripslashes($txt)));
    return '<style type="text/css">p { margin: 0px; padding: 0px; }</style>'.$txt;
}