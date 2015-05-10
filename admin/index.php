<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/common.php");
include(basePath."/admin/helper.php");

## SETTINGS ##
$where = _site_config;
$dir = "admin";
$rootmenu = null;
$settingsmenu = null;
$contentmenu = null;
$addonsmenu = null;
$amenu = array();
$use_glossar = false;

## SECTIONS ##
$check = db("SELECT s1.user FROM ".$db['permissions']." s1, ".$db['users']." s2
             WHERE s1.user = '".$userid."'
             AND s2.id = '".intval($userid)."'
             AND s2.pwd = '".$_SESSION['pwd']."'");

if(!admin_perms($_SESSION['id']))
    $index = error(_error_wrong_permissions, 1);
else {
    if(isset($_GET['admin']) && file_exists(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.php') &&
                                file_exists(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.xml')) {
        $permission = false; define('_adminMenu', true);
        $xml = simplexml_load_file(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.xml');
        $rights = (string)$xml->Rights; $oa = (int)$xml->Only_Admin; $ora = (int)$xml->Only_Root;
        if(permission($rights) && !$oa && !$ora) $permission = true;
        if($oa && !$ora && $chkMe == 4) $permission = true;
        if($ora && $chkMe == 4 && rootAdmin()) $permission = true;

        if($permission)
            include(basePath.'/admin/menu/'.strtolower($_GET['admin']).'.php');
        else
            $show = error(_error_wrong_permissions, 1);
    }

    //Site Permissions
    $files = get_files(basePath.'/admin/menu/',false,true,array('xml'));
    if(count($files)) {
        foreach($files AS $file_xml) {
            if(file_exists(basePath.'/admin/menu/'.str_replace('.xml','.php',$file_xml))) {
                $permission = false;
                $xml = simplexml_load_file(basePath.'/admin/menu/'.$file_xml);
                $rights = (string)$xml->Rights; $oa = (int)$xml->Only_Admin; $ora = (int)$xml->Only_Root;
                if(permission($rights) && !$oa && !$ora) $permission = true;
                if($oa && !$ora && $chkMe == 4) $permission = true;
                if($ora && $chkMe == 4 && rootAdmin()) $permission = true;

                foreach($picformat AS $end) {
                    if(file_exists(basePath.'/admin/menu/'.str_replace('.xml','',$file_xml).'.'.$end))
                        break;
                }

                $link = constant("_config_".str_replace('.xml','',$file_xml));
                $menu = (string)$xml->Menu; $type = str_replace('.xml','',$file_xml);
                if(!empty($menu) && !empty($rights) && $permission)
                    $amenu[$menu][$type] = show("['[link]','?admin=[name]','background-image:url(menu/[name].".$end.");'],\n", array("link" => $link, 'name' => $type));
            }
        }
    }

    foreach($amenu AS $m => $k) {
        natcasesort($k);
        foreach($k AS $l) $$m .= $l;
    }

    $radmin1 = ''; $radmin2 = '';
    if(empty($rootmenu)) {
        $radmin1 = '/*'; $radmin2 = '*/';
    }

    $adminc1 = ''; $adminc2 = '';
    if(empty($settingsmenu)) {
        $adminc1 = '/*'; $adminc2 = '*/';
    }

    $cdminc1 = ''; $cdminc2 = '';
    if(empty($contentmenu)) {
        $cdminc1 = '/*'; $cdminc2 = '*/';
    }
    
    $addons1 = ''; $addons2 = '';
    if(empty($addonsmenu)) {
        $addons1 = '/*'; $addons2 = '*/';
    }

    //Dashboard
    if(empty($show)) {
        $show_news = '';
        if(allow_url_fopen_support()) {
            if(admin_view_dzcp_news) {
                if (!$config_cache['use_cache'] || !$cache->isExisting("admin_news")) {
                    $dzcp_news_stream = fileExists("http://www.dzcp.de/dzcp_news_1.7_test.php");
                    if ($dzcp_news_stream != false && !empty($dzcp_news_stream)) {
                        if ($config_cache['use_cache']) {
                            $cache->set("admin_news", base64_encode($dzcp_news_stream), 1200);
                        }

                        $dzcp_news_stream = json_decode($dzcp_news_stream, true);
                    }
                } else {
                    $dzcp_news_stream = json_decode(base64_decode($cache->get("admin_news")), true);
                }

                $dzcp_news_db = array();
                if(file_exists(basePath.'/inc/_cache_/admin_dzcp_news.dat')) {
                    $dzcp_news_db = json_decode(file_get_contents(basePath.'/inc/_cache_/admin_dzcp_news.dat'),true);
                    if(isset($_POST['what']) && $_POST['what'] == 'news') {
                        switch ($_POST['do']) {
                            case 'remove':
                                $dzcp_news_db[intval($_POST['newsID'])] = true;
                            break;
                            case 'update':
                                $dzcp_news_stream = fileExists("http://www.dzcp.de/dzcp_news_1.7_test.php");
                                if($dzcp_news_stream != false && !empty($dzcp_news_stream)) {
                                    if ($config_cache['use_cache']) {
                                        $cache->set("admin_news", base64_encode($dzcp_news_stream), 1200);
                                    }

                                    $dzcp_news_stream = json_decode($dzcp_news_stream, true);
                                }
                            break;
                        }
                        
                        file_put_contents(basePath.'/inc/_cache_/admin_dzcp_news.dat', json_encode($dzcp_news_db));
                    }
                } else {
                    file_put_contents(basePath.'/inc/_cache_/admin_dzcp_news.dat', json_encode(array()));
                }
                
                if(count($dzcp_news_stream) >= 1) {
                    foreach ($dzcp_news_stream as $news) {
                        if(!array_key_exists($news['newsid'], $dzcp_news_db)) {
                            if(!$cache->isExisting('dzcp_news_image')) {
                                $image = base64_encode(file_get_contents($news['image']));
                                $cache->set('dzcp_news_image',$image,300);
                            } else {
                                $image = $cache->get('dzcp_news_image');
                            }
                            
                            $show_news .= show($dir."/dzcp_news_show", array("titel" => $news['titel'],
                                                                             "image" => $image,
                                                                             "id" => $news['newsid'],
                                                                             "text" => cut($news['news'],230,true),
                                                                             "url" => $news['url'],
                                                                             "datum" => date("d.m.y H:i", $news['date'])._uhr));
                        }
                    }
                }

                if(empty($show_news)) {
                    $show_news = show(_no_news_yet, array("colspan" => "2"));
                }

                unset($news,$dzcp_news_stream);
            }
        }
        
        $show = show($dir."/dashboard", array('news' => show($dir."/dzcp_news", array('news' => $show_news))));
    }
    
    if(@file_exists(basePath."/_installer") && $chkMe == 4 && !view_error_reporting && _edition != 'dev')
        $index = _installdir;
    else {
        $dzcp_version = show_dzcp_version();
        $index = show($dir."/admin", array("version" => $dzcp_version['version'],
                                           "version_img" => $dzcp_version['version_img'],
                                           "einst" => _config_einst,
                                           "content" => _content,
                                           "addons" => _addons,
                                           "rootadmin" => _rootadmin,
                                           "rootmenu" => $rootmenu,
                                           "settingsmenu" => $settingsmenu,
                                           "contentmenu" => $contentmenu,
                                           "addonsmenu" => $addonsmenu,
                                           "radmin1" => $radmin1,
                                           "radmin2" => $radmin2,
                                           "adminc1" => $adminc1,
                                           "adminc2" => $adminc2,
                                           "cdminc1" => $cdminc1,
                                           "cdminc2" => $cdminc2,
                                           "addons1" => $addons1,
                                           "addons2" => $addons2,
                                           "show" => $show));
    }
}

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);