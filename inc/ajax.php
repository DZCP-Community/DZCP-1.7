<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
define('basePath', dirname(dirname(__FILE__).'../'));
ob_start();
ob_implicit_flush(false);
    $ajaxJob = true;

    ## INCLUDES ##
    include(basePath."/inc/common.php");

    ## SETTINGS ##
    $dir = "sites";
    addNoCacheHeaders(); //No Browser-Cache
    $is_debug = isset($_GET['debug']);

    ## SECTIONS ##
    //-> Steam Status
    function steamIMG($steamID='') {
        global $cache, $config_cache;
        if(!allow_url_fopen_support()) return _fopen;
        if(empty($steamID) || !steam_enable) return '-';
        if(!$steam = SteamAPI::getUserInfos($steamID)) return '-'; //UserInfos
        if(!$steam || empty($steam)) return '-';

        //Steam Avatar
        if(!$config_cache['use_cache'] || !$cache->isExisting('steam_avatar_'.$steamID)) {
            $ctx = stream_context_create(array('http'=>array('timeout' => file_get_contents_timeout)));
            if($img_stream = file_get_contents($steam['user']['avatarIcon_url'], false, $ctx)) {
                $steam['user']['avatarIcon_url'] = 'data:image/png;base64,'.base64_encode($img_stream);
                if(steam_avatar_cache && $config_cache['use_cache'])
                    $cache->set('steam_avatar_'.$steamID, bin2hex($img_stream), steam_avatar_refresh);
            } else 
                return '-';
        } else 
            $steam['user']['avatarIcon_url'] = 'data:image/png;base64,'.base64_encode(hextobin($cache->get('steam_avatar_'.$steamID)));

        switch($steam['user']['onlineState']) {
            case 'in-game': $status_set = '2'; $text_1 = _steam_in_game; $text_2 = $steam['user']['gameextrainfo']; break;
            case 'online': $status_set = '1'; $text_1 = _steam_online; $text_2 = ''; break;
            default: $status_set = '0'; $text_1 = $steam['user']['runnedSteamAPI'] ? show(_steam_offline,array('time' => 
                get_elapsed_time($steam['user']['lastlogoff'],time(),1))) : _steam_offline_simple; $text_2 = ''; break;
        }

        return show((isset($_GET['list']) ? _steamicon_nouser : _steamicon), array('profile_url' => $steam['user']['profile_url'],
                                                                                   'username' => $steam['user']['nickname'],
                                                                                   'avatar_url' => $steam['user']['avatarIcon_url'],
                                                                                   'text1' => $text_1,
                                                                                   'text2' => $text_2,
                                                                                   'status' => $status_set));
    }

    $mod = isset($_GET['i']) ? $_GET['i'] : '';
    if($mod != 'securimage' && $mod != 'securimage_audio')
        header("Content-Type: text/xml; charset=".(!defined('_charset') ? 'iso-8859-1' : _charset));
    else if($mod == 'server' || $mod == 'teamspeak')
        header("Content-type: application/x-www-form-urlencoded;charset=utf-8");

    switch ($mod):
        case 'kalender':
            require_once(basePath."/inc/menu-functions/kalender.php");
            $month = (isset($_GET['month']) ? $_GET['month'] : '');
            $year = (isset($_GET['year']) ? $_GET['year'] : '');
            echo kalender($month,$year,true);
        break;
        case 'counter':
            require_once(basePath."/inc/menu-functions/counter.php");
            echo counter(true); 
        break;
        case 'teams':
            require_once(basePath."/inc/menu-functions/team.php");
            echo team($_GET['tID']); 
        break;
        case 'server':
            require_once(basePath."/inc/menu-functions/server.php"); 
            echo '<table class="hperc" cellspacing="0">'.server($_GET['serverID']).'</table>'; 
        break;
        case 'shoutbox':
            require_once(basePath."/inc/menu-functions/shout.php");
            echo '<table class="hperc" cellspacing="1">'.shout(1).'</table>'; 
        break;
        case 'teamspeak':
            require_once(basePath."/inc/menu-functions/teamspeak.php");
            echo '<table class="hperc" cellspacing="0">'.teamspeak(1).'</table>'; 
        break;
        case 'steam':     echo steamIMG(trim($_GET['steamid'])); break;
        case 'autocomplete':
            if($_GET['type'] == 'srv') {
                if($_GET['game'] == 'nope') {
                    echo json_encode(array('qport' => ''));
                } else {
                    $protocols_array = GameQ::getGames();
                    foreach ($protocols_array AS $gameq => $info) {
                        if($gameq == $_GET['game'] && $info['autocomplete']) {
                            echo json_encode(array('qport' => $info['port'])); 
                            break; 
                        }
                    }

                    echo json_encode(array('qport' => ''));
                }
            }
            break;
        case 'securimage':
            if(!headers_sent()) {
                $securimage->background_directory = basePath.'/inc/images/securimage/background/';
                $securimage->code_length  = mt_rand(4, 6);
                $securimage->image_height = isset($_GET['height']) ? intval($_GET['height']) : 40;
                $securimage->image_width  = isset($_GET['width']) ? intval($_GET['width']) : 200;
                $securimage->perturbation = .75;
                $securimage->text_color   = new Securimage_Color("#CA0000");
                $securimage->num_lines    = isset($_GET['lines']) ? intval($_GET['lines']) : 2;
                $securimage->namespace    = isset($_GET['namespace']) ? $_GET['namespace'] : 'default';
                if(isset($_GET['length'])) $securimage->code_length = intval($_GET['length']);
                
                $imgData = $securimage->show();
                if(!$imgData['error']) {
                    if($is_debug) {
                        echo '<img src="data:image/gif;base64,'.$imgData['data'].'" />';
                    } else {
                        echo 'data:image/gif;base64,'.$imgData['data'];
                    }
                } else {
                    echo $imgData; 
                }
            }
            break;
        case 'securimage_audio':
            if(!headers_sent()) {
                $securimage->audio_path = basePath.'/inc/securimage/audio/en/';
                switch($language) {
                    case 'deutsch':
                        if(file_exists(basePath.'/inc/securimage/audio/de/0.wav')) {
                            $securimage->audio_path = basePath.'/inc/securimage/audio/de/';
                        }
                    break;
                    case 'english':
                        if(file_exists(basePath.'/inc/securimage/audio/en/0.wav')) {
                            $securimage->audio_path = basePath.'/inc/securimage/audio/en/';
                        }
                    break;
                    default:
                        if(file_exists(basePath.'/inc/securimage/audio/'.$language.'/0.wav')) {
                            $securimage->audio_path = basePath.'/inc/securimage/audio/'.$language.'/';
                        }
                    break;
                }
                
                if(captcha_audio_use_sox) {
                    $securimage->audio_use_sox   = true;
                    $securimage->audio_use_noise = captcha_audio_use_noise;
                    $securimage->degrade_audio   = captcha_degrade_audio;
                    $securimage->sox_binary_path = captcha_sox_binary_path;
                } else {
                    $securimage->audio_use_sox   = false;
                }

                $securimage->namespace = isset($_GET['namespace']) ? $_GET['namespace'] : 'default';
                echo $securimage->outputAudioFile();
            }
            break;
    endswitch;

    $output = ob_get_contents();
    if(debug_save_to_file) {
        DebugConsole::save_log(); //Debug save to file
    }

ob_end_clean();
ob_start('ob_gzhandler');
    echo ($is_debug ? DebugConsole::show_logs().$output : $output);
ob_end_flush();