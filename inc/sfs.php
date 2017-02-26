<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

class sfs {
    private static $endpoint = 'http://www.stopforumspam.com/';
    private static $url = '';
    private static $json = '';
    private static $confidence = 70;
    private static $frequency = 50;
    private static $autoblock = true;
    private static $blockuser = false;
    public static function check() {
        global $userip,$sql;
        ## http://de.wikipedia.org/wiki/Private_IP-Adresse ##
        if(!validateIpV4Range($userip, '[192].[168].[0-255].[0-255]') && !validateIpV4Range($userip, '[127].[0].[0-255].[0-255]') && !validateIpV4Range($userip, '[10].[0-255].[0-255].[0-255]') && !validateIpV4Range($userip, '[172].[16-31].[0-255].[0-255]')) {
            $get = $sql->fetch("SELECT * FROM `{prefix_ipban}` WHERE `ip` = ? LIMIT 1;",array($userip));
            if($sql->rowCount()) {
                if((time()-$get['time']) > (2*86400) && $get['enable']) {
                    self::get(array('ip' => $userip)); //Array ( [success] => 1 [ip] => Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 ) )
                    $stopforumspam = self::$json;
                    if($stopforumspam['success']) {
                        $stopforumspam = $stopforumspam['ip']; // Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 )
                        $stopforumspam_data_db = unserialize($get['data']);
                        if($stopforumspam['appears'] == '1' && ($stopforumspam['confidence'] >= self::$confidence || $stopforumspam['frequency'] >= self::$frequency) && self::$autoblock) {
                            $stopforumspam_data_db['confidence'] = $stopforumspam['confidence'];
                            $stopforumspam_data_db['frequency'] = $stopforumspam['frequency'];
                            $stopforumspam_data_db['lastseen'] = $stopforumspam['lastseen'];
                            $stopforumspam_data_db['banned_msg'] = 'Autoblock by stopforumspam.com';
                            $sql->update("UPDATE `{prefix_ipban}` SET `time` = ?, `typ` = 1, `data` = ? WHERE `id` = ?;",
                                    array(time(),serialize($stopforumspam_data_db),$get['id']));
                            $sql->delete("DELETE FROM `{prefix_counter_ips}` WHERE `ip` = ?;",array($userip));
                            $sql->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `ip` = ?;",array($userip));
                            $sql->delete("DELETE FROM `{prefix_iptodns}` WHERE `ip` = ?;",array($userip));
                            self::$blockuser = true;
                        } else {
                            $stopforumspam_data_db['appears'] = $stopforumspam['appears'];
                            $sql->update("UPDATE `{prefix_ipban}` SET `time` = ?, `typ` = 0, `data` = ? WHERE `id` = ?;",
                                    array(time(),serialize($stopforumspam_data_db),$get['id']));
                            self::$blockuser = false;
                        }
                    }
                }
                else if($get['typ'] == 1)
                    self::$blockuser = true;
                else
                    self::$blockuser = false;
            } else {
                //typ: 0 = Off, 1 = GSL, 2 = SysBan, 3 = Ipban
                self::get(array('ip' => $userip)); //Array ( [success] => 1 [ip] => Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 ) )
                $stopforumspam = self::$json;
                if(array_key_exists('success', $stopforumspam) && $stopforumspam['success']) {
                    $stopforumspam = $stopforumspam['ip']; // Array ( [lastseen] => 2013-04-26 19:57:51 [frequency] => 1327 [appears] => 1 [confidence] => 99.89 )
                    if($stopforumspam['appears'] == '1' && $stopforumspam['confidence'] >= self::$confidence && $stopforumspam['frequency'] >= self::$frequency && self::$autoblock) {
                        $stopforumspam['banned_msg'] = 'Autoblock by stopforumspam.com';
                        $sql->delete("DELETE FROM `{prefix_counter_ips}` WHERE `ip` = ?;",array($userip));
                        $sql->delete("DELETE FROM `{prefix_counter_whoison}` WHERE `ip` = ?;",array($userip));
                        $sql->delete("DELETE FROM `{prefix_iptodns}` WHERE `ip` = ?;",array($userip));
                        $sql->insert("INSERT INTO `{prefix_ipban}` SET `ip` = ?, `time` = ?, `typ` = 1, `data` = ?;",
                                array($userip,time(),serialize($stopforumspam))); //Banned
                        self::$blockuser = true;
                    } else {
                        $stopforumspam['banned_msg'] = '';
                        $sql->insert("INSERT INTO `{prefix_ipban}` SET `ip` = ?, `time` = ?,`typ` = 0, `data` = ?;",
                                array($userip,time(),serialize($stopforumspam))); //Add to DB
                        self::$blockuser = false;
                    }
                }
            }
        }
    }

    public static function is_spammer()
    { return self::$blockuser; }

    public static function get( $args = array() ) {
        self::$url = self::$endpoint.'api?f=json&'.http_build_query($args, '', '&');
        if(!self::call_json()) return array('data' => array('success' => '0'));
    }

    protected static function call_json() {
        if(view_error_reporting && debug_save_to_file) {
            $fp = fopen(basePath."/inc/_logs/fsf_ips.log", "a+");
            fwrite($fp, self::$url); 
            fclose($fp);
        }

        if(!(self::$json = get_external_contents(self::$url))) return false;
        if(empty(self::$json)) return false;

        self::$json = json_decode(self::$json,true);
        if(!self::$json) false;
        return true;
    }
}