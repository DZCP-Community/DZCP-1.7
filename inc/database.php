<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Database Connect & Functions
 */

class database {
    public $mysqli_resource = null;
    
    /* Constructor */
    public function __construct() {
        global $db;
        $this->connect($db['host'],$db['user'],$db['pass'],$db['db']);
    }
    
    public function __construct1($host,$db) {
        global $db;
        $this->connect($host,$db['user'],$db['pass'],$db);
    }
    
    public function __construct2($host,$db,$user,$pass) {
        $this->connect($host,$user,$pass,$db);
    }
    
    /* Destructor */
    public function __destruct() {
        $this->close();
    }
    
    /* Close */
    public function close() {
        if (!mysqli_persistconns && $this->mysqli_resource instanceof mysqli) {
            $this->mysqli_resource->close();
        }
    }
    
    /* MySQL Functions */
    public function rows($rows) {
        if ($this->mysqli_resource instanceOf mysqli && (array_key_exists('_stmt_rows_',$rows) || is_object($rows))) {
            return array_key_exists('_stmt_rows_', $rows) ? $rows['_stmt_rows_'] : $rows->num_rows;
        }

        return false;
    }

    public function fetch($fetch) {
        if ($this->mysqli_resource instanceOf mysqli && (is_array($fetch) || is_object($fetch))) {
            return array_key_exists('_stmt_rows_', $fetch) ? $fetch[0] : $fetch->fetch_assoc();
        }

        return false;
    }

    public function real_escape_string($string='') {
        if ($this->mysqli_resource instanceOf mysqli) {
            return (!empty($string) ? $this->mysqli_resource->real_escape_string($string) : $string);
        }

        return false;
    }

    public function insert_id() {
        if ($this->mysqli_resource instanceOf mysqli) {
            return $this->mysqli_resource->insert_id;
        }

        return false;
    }
    
    public function db($query='',$rows=false,$fetch=false) {
        global $clanname,$updater;
        if ($this->mysqli_resource instanceOf mysqli) {
            if(empty($query)) { DebugConsole::insert_warning('database::db(()', 'MySQL-Query is empty!'); return false; }
            if(debug_all_sql_querys) { DebugConsole::wire_log('debug', 9, 'SQL_Query', $query); }
            if($updater) { $qry = $this->mysqli_resource->query($query); } else {
                if(!$qry = $this->mysqli_resource->query($query)) {
                    $message = DebugConsole::sql_error_handler($query);
                    include_once(basePath."/inc/lang/languages/english.php");
                    $message = 'SQL-Debug:<p>'.$message;
                    exit(show('<b>Upps...</b><br /><br />Entschuldige bitte! Das h&auml;tte nicht passieren d&uuml;rfen. Wir k&uuml;mmern uns so schnell wie m&ouml;glich darum.<br><br>'.$clanname.
                    '<br><br>'.(view_error_reporting ? nl2br($message).'<br><br>' : '').'[lang_back]'));
                }
            }

            if ($rows && !$fetch) {
                $rqry = $this->rows($qry);
            } else if($fetch && $rows) {
                $rqry = $qry->fetch_array(MYSQLI_NUM);
            } else if($fetch && !$rows) {
                $rqry = $this->fetch($qry);
            } else {
                $rqry = $qry;
            }
            
            if ($rows || $fetch) {
                $qry->free_result();
                $qry->close();
            }

            return $rqry;
        }

        return false;
    }

    /**
     *  i     corresponding variable has type integer
     *  d     corresponding variable has type double
     *  s     corresponding variable has type string
     *  b     corresponding variable is a blob and will be sent in packets
     */
    public function db_stmt($query,$params=array('si', 'hallo', '4'),$rows=false,$fetch=false) {
        global $prefix;
        if ($this->mysqli_resource instanceOf mysqli) {
            if(!$statement = $this->mysqli_resource->prepare($query)) exit('<b>MySQL-Query failed:</b><br /><br /><ul>'.
                    '<li><b>ErrorNo</b> = '.!empty($prefix) ? str_replace($prefix,'',$this->mysqli_resource->connect_errno) : $this->mysqli_resource->connect_errno.
                    '<li><b>Error</b>   = '.!empty($prefix) ? str_replace($prefix,'',$this->mysqli_resource->connect_error) : $this->mysqli_resource->connect_error.
                    '<li><b>Query</b>   = '.!empty($prefix) ? str_replace($prefix,'',$query).'</ul>' : $query);

            call_user_func_array(array($statement, 'bind_param'), $this->refValues($params));
            if(!$statement->execute()) exit('<b>MySQL-Query failed:</b><br /><br /><ul>'.
                    '<li><b>ErrorNo</b> = '.!empty($prefix) ? str_replace($prefix,'',$this->mysqli_resource->connect_errno) : $this->mysqli_resource->connect_errno.
                    '<li><b>Error</b>   = '.!empty($prefix) ? str_replace($prefix,'',$this->mysqli_resource->connect_error) : $this->mysqli_resource->connect_error.
                    '<li><b>Query</b>   = '.!empty($prefix) ? str_replace($prefix,'',$query).'</ul>' : $query);

            $meta = $statement->result_metadata();
            if(!$meta || empty($meta)) { $statement->close(); return; }
            $row = array(); $parameters = array(); $results = array();
            while ( $field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }

            $statement->store_result();
            $results['_stmt_rows_'] = $statement->num_rows();
            call_user_func_array(array($statement, 'bind_result'), $this->refValues($parameters));

            while (mysqli_stmt_fetch($statement)) {
                $x = array();
                foreach( $row as $key => $val ) {
                    $x[$key] = $val;
                }

                $results[] = $x;
            }

            if ($rows && !$fetch) {
                $results = $this->rows($results);
            } else if($fetch && !$rows) {
                $results = $this->fetch($results);
            }
            
            $statement->free_result();
            $statement->close();
            return $results;
        }
    }
    
    private function refValues($arr) {
        if (strnatcmp(phpversion(),'5.3') >= 0) {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];

            return $refs;
        }

        return $arr;
    }
    
    /* Privat Connect */
    private function connect($host,$user,$pass,$db) {
        global $thumbgen;
        if ($this->mysqli_resource instanceOf mysqli === false) {
            if(!$thumbgen && !empty($host) && !empty($user) && !empty($pass) && !empty($db)) {
                $db_host = (mysqli_persistconns ? 'p:' : '').$host;
                $this->mysqli_resource = new mysqli($db_host,$user,$pass,$db);
                if($this->mysqli_resource->connect_errno != 0) {
                    die('Unable to connect to database! [' . $this->mysqli_resource->connect_error . '] '
                    . '-> ['.$this->mysqli_resource->connect_errno.']');
                }
                
                if($this->mysqli_resource instanceof mysqli === true) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
