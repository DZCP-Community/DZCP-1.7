<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

class FTP {
    private static $options = array();
    public static $connect = null;
    public static $login = false;
    private static $pasv = true;
    private static $dir = '';

    public static function init() {
        self::$options['ssl'] = false;
        self::$options['timeout'] = 90;

        self::$options['host'] = 'localhost';
        self::$options['port'] = 21;
        self::$options['user'] = '';
        self::$options['pass'] = '';

        self::$pasv = true; //Set Passive Mode to On
    }

    public static function set($option='',$var='')
    { self::$options[$option] = $var; }

    /**
     * Verbindet zu einem FTP Server
     * @return boolean
     */
    public static function connect() {
        if(self::$connect != false && is_resource(self::$connect))
            return true;

        if(self::$options['ssl'] && function_exists('ftp_ssl_connect'))
            self::$connect = @ftp_ssl_connect(self::$options['host'],self::$options['port']);
        else
            self::$connect = @ftp_connect(self::$options['host'],self::$options['port']);

        if(self::$connect != false && is_resource(self::$connect))
            return true;

        DebugConsole::insert_error('', 'Can\'t connect to FTP Server on '.self::$options['host'].':'.self::$options['port']);
        return false;
    }

    /**
     * Anmeldung am FTP Server mit Benuzer und Passwort
     * @return boolean
     */
    public static function login() {
        if(!self::$connect || !is_resource(self::$connect))
            self::connect();

        if(self::$connect != false && is_resource(self::$connect) && !self::$login) {
            if(ftp_login(self::$connect, self::$options['user'], self::$options['pass'])) {
                self::$options['login'] = self::$options['user'].'@'.self::$options['host'].':'.self::$options['port'];
                @ftp_pasv(self::$connect,self::$pasv) or DebugConsole::insert_warning('FTP::login()', 'Can not change Passive/Activ Mode!');
                DebugConsole::insert_successful('FTP::login()', 'Login to "'.self::$options['user'].'@'.self::$options['host'].':'.self::$options['port'].'" successful!');
                self::$login = true;
                return true;
            }

            self::$login = false;
            DebugConsole::insert_warning('FTP::login()', 'Invalid authentication for "'.self::$options['user'].'@'.self::$options['host'].':'.self::$options['port'].'"');
            self::close();
        }

        return false;
    }

    /**
     * Prüft ob ein Verzeichniss gültig ist und wechselt dann dort hin
     * @param string $dir
     * @return boolean
     */
    public static function move($dir='') {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            if($dir == '/')
                $dir_path = '/';
            else {
                $dirs = explode('/', $dir); $dir_path = '/';
                foreach($dirs as $dir) {
                    if(empty($dir)) continue;
                    $dir_path .= $dir.'/';
                    if(!ftp_chdir(self::$connect, $dir_path)) {
                        DebugConsole::insert_warning('FTP::move()', 'Dir, '.$dir_path.' is not exists!');
                        return false;
                    }
                }
            }

            DebugConsole::insert_info('FTP::move()', 'Go to dir: "'.$dir_path.'"');
            return true;
        }

        return false;
    }

    /**
     * Gibt eine Liste der im angegebenen Verzeichnis enthaltenen Dateien zurück
     * @return array|boolean
     */
    public static function nlist() {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login)
            return ftp_nlist(self::$connect, self::$dir);

        return false;
    }


    /**
     * Spreichert eine neue Datei auf dem FTP, Quelle ist eine Datei
     * @param string $local_file
     * @param string $remote_file
     * @param string $mode_binary
     * @return boolean
     */
    public static function put_file($local_file=null,$remote_file=null,$mode_binary=false) {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $ret = ftp_put(self::$connect,str_replace('//', '/', self::$dir.'/'.$remote_file),$local_file,($mode_binary ? FTP_BINARY : FTP_ASCII)) or DebugConsole::insert_warning('FTP::put_file()', 'Can not Upload file, '.$local_file);
            if($ret == FTP_FAILED) DebugConsole::insert_warning('FTP::put_file()', 'File Upload failed, '.$local_file);
            else return true;
        }

        return false;
    }

    /**
     * Holt eine Datei vom FTP, Ziel ist eine Datei
     * @param string $local_file
     * @param string $remote_file
     * @param string $mode_binary
     * @return boolean
     */
    public static function get_file($local_file=null,$remote_file=null,$mode_binary=false) {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $ret = ftp_get(self::$connect,str_replace('//', '/', self::$dir.'/'.$remote_file),$local_file,($mode_binary ? FTP_BINARY : FTP_ASCII)) or DebugConsole::insert_warning('FTP::get_file()', 'Can not Download file, '.$remote_file);
            if($ret == FTP_FAILED) DebugConsole::insert_warning('FTP::get_file()', 'File Download failed, '.$remote_file);
            else return true;
        }

        return false;
    }

    /**
     * Spreichert eine neue Datei auf dem FTP, Quelle ist ein String/Binary-String
     * @param string $stream
     * @param string $remote_file
     * @param string $mode_binary
     * @return boolean
     */
    public static function put_stream($stream=null,$remote_file=null,$mode_binary=false) {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $stream = fopen('data://text/plain,' . $stream,'r');
            $ret = ftp_fput(self::$connect,str_replace('//', '/', self::$dir.'/'.$remote_file),$stream,($mode_binary ? FTP_BINARY : FTP_ASCII)) or DebugConsole::insert_warning('FTP::put_stream()', 'Can not Upload to file, '.$remote_file);
            if($ret == FTP_FAILED) DebugConsole::insert_warning('FTP::put_stream()', 'Stream Upload failed, '.$remote_file);
            else return true;
        }

        return false;
    }

    /**
     * Holt eine Datei vom FTP Server, Ziel ist ein String/Binary-String
     * @param string $remote_file
     * @param string $mode_binary
     * @return string|boolean
     */
    public static function get_stream($remote_file=null,$mode_binary=false) {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $tempHandle = fopen('php://temp', 'r+');
            $ret = ftp_fput(self::$connect,str_replace('//', '/', self::$dir.'/'.$remote_file),$tempHandle,($mode_binary ? FTP_BINARY : FTP_ASCII)) or DebugConsole::insert_warning('FTP::get_stream()', 'Can not Download file, '.$remote_file);
            if($ret == FTP_FAILED) DebugConsole::insert_warning('FTP::get_stream()', 'Stream Download failed, '.$remote_file);
            else { rewind($tempHandle); return stream_get_contents($tempHandle); }
        }

        return false;
    }

    /**
     * Ändert die Zugriffsrechte einer oder mehreren Dateien
     * @param string/array $file
     * @param string $mode
     * @return boolean|number
     */
    public static function chmod($file='',$mode='644') {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $mode = octdec(str_pad($mode,4,'0',STR_PAD_LEFT));
            if(is_array($file)) {
                foreach($file as $file_c) {
                    if(!ftp_chmod(self::$connect, $mode, str_replace('//', '/', self::$dir.'/'.$file_c)))
                        return false;
                }

                return true;
            } else
                return @ftp_chmod(self::$connect, $mode, str_replace('//', '/', self::$dir.'/'.$file));
        }

        return false;
    }

    /**
     * Erstellt einen Ordner auf dem FTP
     * @param string $dir
     * @param string $use_dir
     * @return boolean
     */
    public static function mkdir($dir='',$use_dir=false) {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $ret = ftp_mkdir(self::$connect, str_replace('//', '/',(!$use_dir ? self::$dir : $use_dir).'/'.$dir)) or DebugConsole::insert_warning('FTP::mkdir()', 'Can not create dir, '.self::$dir.'/'.$dir);
            if($ret != false) return true;
        }

        return false;
    }

    /**
     * Löscht einen Ordner auf dem FTP
     * @param string $dir
     * @param string $use_dir
     * @return boolean
     */
    public static function rmdir($dir='',$use_dir=false) {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $ret = ftp_rmdir(self::$connect, str_replace('//', '/', (!$use_dir ? self::$dir : $use_dir).'/'.$dir)) or DebugConsole::insert_warning('FTP::rmdir()', 'Can not remove dir, '.self::$dir.'/'.$dir);
            if($ret != false) return true;
        }

        return false;
    }

    /**
     * Benennt eine Datei auf dem FTP-Server um
     * @param string $file_old
     * @param string $file_new
     * @return boolean
     */
    public static function rename($file_old='',$file_new='') {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $ret = ftp_rename(self::$connect, str_replace('//', '/', self::$dir.'/'.$file_old), str_replace('//', '/', self::$dir.'/'.$file_new)) or DebugConsole::insert_warning('FTP::rename()', 'Can not renames dir or file, '.$file_old.' to '.$file_new);
            if($ret != false) return true;
        }

        return false;
    }

    /**
     * Löscht eine Datei auf dem FTP-Server
     * @param string $file
     * @return boolean
     */
    public static function delete($file='') {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $ret = ftp_delete(self::$connect, str_replace('//', '/', self::$dir.'/'.$file));
            if($ret != false) return true;
        }

        return false;
    }

    /**
     * Gibt den aktuellen Verzeichnisnamen zurück
     * @return boolean
     */
    public static function pwd() {
        self::login();
        if(self::$connect != false && is_resource(self::$connect) && self::$login) {
            $ret = ftp_pwd(self::$connect);
            if($ret != false) return true;
        }

        return false;
    }

    /**
     * Schließt die FTP-Verbindung
     * @return boolean
     */
    public static function close() {
        if(self::$connect != false && is_resource(self::$connect)) {
            self::$login = false;
            return ftp_close(self::$connect);
        }

        return false;
    }
}