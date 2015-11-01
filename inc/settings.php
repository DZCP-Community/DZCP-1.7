<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

class settings {
    private static $index = array();

    /**
     * Gibt eine Einstellung aus der Settings Tabelle zurück
     * @param string $what
     * @return string|int|boolean
     */
    public final static function get($what='') {
        global $sql;
        $what = strtolower($what);
        if(!count(self::$index) && dbc_index::issetIndex('settings')) {
            self::$index = dbc_index::getIndex('settings');
        }
        
        if(self::is_exists($what)) {
            $data = self::$index[$what];
            return $data['value'];
        } else {
            $get = $sql->fetch("SELECT `value` FROM `{prefix_settings}` WHERE `key` = ? LIMIT 1;",array($what));
            if(!$sql->rowCount()) {
                if (show_settings_debug) {
                    DebugConsole::insert_error('settings::get()', 'Setting "' . $what . '" not found in ' . $sql->rep_prefix('{prefix_settings}'));
                }
            } else {
                return $get['value'];
            }
        }

        return false;
    }

    /**
     * Gibt mehrere Einstellungen aus der Settings Tabelle zurück
     * @param string $what
     * @return array|boolean
     */
    public final static function get_array($what=array()) {
        if (!is_array($what) || !count($what) || empty($what)) {
            return false;
        }

        $return = array();
        foreach ($what as $key) {
            $key = strtolower($key);
            if(array_key_exists($key, self::$index)) {
                $data = self::$index[$key];
                $return[$key] = $data['value'];
                $data = array();
            }
        }

        if(count($return) >= 1) return $return;
        return false;
    }

    /**
     * Gibt die Standard Einstellung einer Einstellung zurück
     * @param string $what
     * @return mixed|boolean
     */
    public final static function get_default($what='') {
        global $sql;
        $what = strtolower($what);
        if (self::is_exists($what)) {
            $data = self::$index[$what];
            return $data['default'];
        } else {
            $get = $sql->fetch("SELECT `default` FROM `{prefix_settings}` WHERE `key` = ? LIMIT 1;",array($what));
            if(!$sql->rowCount()) {
                if (show_settings_debug) {
                    DebugConsole::insert_error('settings::get_default()', 'Setting "' . $what . '" not found in '.$sql->rep_prefix('{prefix_settings}'));
                }
            } else {
                return $get['default'];
            }
        }

        return false;
    }

    /**
     * Aktualisiert die Werte innerhalb der Settings Tabelle
     * @param string $what
     * @param string $var
     * @return boolean
     */
    public final static function set($what='',$var='', $default=true) {
        global $sql;
        $what = strtolower($what);
        if(self::is_exists($what)) {
            if(self::changed($what,$var)) {
                $var = empty($var) && $default ? self::get_default($what) : $var;
                $data = self::$index[$what];
                $data['value'] = ($data['length'] >= 1 ? cut($var,((int)$data['length']),false) : $var);
                self::$index[$what] = $data;
                if (show_settings_debug) {
                    DebugConsole::insert_successful('settings::set()', 'Set "'.$what.'" to "'.$var.'"');
                }
                return $sql->update("UPDATE `{prefix_settings}` SET `value` = ? WHERE `key` = ?;",
                    array(($data['length'] >= 1 ? cut($var,((int)$data['length']),false) : $var),$what)) ? true : false;
            }
        }

        return false;
    }

    /**
     * Vergleicht den Aktuellen Wert mit dem neuen Wert ob ein Update erforderlich ist
     * @param string $what
     * @param string $var
     * @return boolean
     */
    public final static function changed($what='',$var='') {
        $what = strtolower($what);
        if(self::is_exists($what)) {
            $data = self::$index[$what];
            return ($data['value'] == $var ? false : true);
        }

        return false;
    }

    /**
     * Prüft ob ein Key existiert
     * @param string $what
     * @return boolean
     */
    public final static function is_exists($what='') { 
        return (array_key_exists(strtolower($what), self::$index)); 
    }

    /**
     * Laden der Einstellungen aus der Datenbank
     */
    public final static function load($reload=false) {
        global $sql;
        if($reload || !dbc_index::issetIndex('settings')) {
            $qry = $sql->select("SELECT `key`,`value`,`default`,`length`,`type` FROM `{prefix_settings}`;");
            foreach($qry as $get) {
                $setting = array();
                $setting['value'] = !((int)$get['length']) ? $get['type'] == 'int' ? ((int)$get['value']) : ((string)$get['value'])
                : cut($get['type'] == 'int' ? ((int)$get['value']) : ((string)$get['value']),((int)$get['length']),false);
                $setting['default'] = $get['type'] == 'int' ? ((int)$get['default']) : ((string)$get['default']);
                $setting['length'] = ((int)$get['length']);
                self::$index[$get['key']] = $setting; unset($setting);
            }

            dbc_index::setIndex('settings', self::$index, 4);
        }
    }

    /**
     * Eine neue Einstellung in die Datenbank schreiben
     * @param string $what
     * @param string/int $var
     * @param string/int $default
     * @param int $length
     * @param boolean $int
     * @return boolean
     */
    public final static function add($what='',$var='',$default='',$length='',$int=false) {
        global $sql;
        $what = strtolower($what);
        if(!self::is_exists($what)) {
            $setting = array();
            $setting['value'] = !((int)$length) ? $int ? ((int)$var) : ((string)$var)
            : cut($int ? ((int)$var) : ((string)$var),((int)$length),false);
            $setting['default'] = $int ? ((int)$default) : ((string)$default);
            $setting['length'] = ((int)$length);
            self::$index[$what] = $setting;
            unset($setting);
            if (show_settings_debug) {
                DebugConsole::insert_successful('settings::add()', 'Add "'.$what.'" set to "'.$var.'"');
            }
            dbc_index::setIndex('settings', self::$index, 4);
            return $sql->insert("INSERT INTO `{prefix_settings}` SET `key` = ?, `value` = ?,"
                . "`default` = ?,`length` = ?,`type` = '".($int ? 'int' : 'string')."';",array($what,$var,$default,$length));
        }

        return false;
    }

    /**
     * Löscht eine Einstellung aus der Datenbank
     * @param string $what
     * @return boolean
     */
    public final static function remove($what='') {
        global $sql;
        $what = strtolower($what);
        if(self::is_exists($what)) {
            if (show_settings_debug) {
                DebugConsole::insert_info('settings::remove()', 'Remove "'.$what.'"');
            }
            unset(self::$index[$what]);
            dbc_index::setIndex('settings', self::$index, 4);
            return $sql->delete("DELETE FROM `{prefix_settings}` WHERE `key` = ?;",array($what)) ? true : false;
        }

        return false;
    }
}