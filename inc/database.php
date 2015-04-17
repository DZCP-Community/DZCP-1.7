<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Database Connect & Functions Class (PDO)
 */

//Debugging PDO
define('pdo_disable_update_statement', false);
define('pdo_disable_insert_statement', false);
define('pdo_disable_delete_statement', false);

    /*
     * 
     * LOOPS
     * 
    foreach($sql->select("SELECT * FROM `".$db['settings']."`;") as $row) {
        echo '<pre>';
        print_r($row);
        echo '<p>';
    }
     * */

    /*
    $rows = $database->select();
    while($row = array_shift($rows)){

    }
     * 
     * Functions Calls
     * 
     	$sql->update();
	$sql->insert();
	$sql->select();
	$sql->rows();
	$sql->selectSingle();
	$sql->delete();
	$sql->rowCount();
	$sql->lastInsertId();
	$sql->query();
*/

class database {
    protected $dbConf = array();
    protected $instances = array();

    protected $active = false;
    protected $dbHandle = null;
    protected $lastInsertId = false;
    protected $rowCount = false;
    protected $queryCounter = 0;
    protected $active_driver = '';
    protected $connection_pooling = true;
    protected $connection_encrypting = true;
    protected $mysql_buffered_query = true;

    public function cloneConfig($active = "default",$from = "new") { 
        if (!isset($this->dbConf[$active])) {
            throw new Exception("Unexisting db-config $active");
        }
        
        $this->dbConf[$from] = $this->dbConf[$active];
    }

    public function setConfig($active = "default", array $data) {
        if(isset($data['db']) && isset($data['db_host']) && isset($data['db_host']) && isset($data['db_user']) && isset($data['db_pw'])) {
            $this->dbConf[$active] = $data;
        }
    }

    public final function getInstance($active = "default") {
        if(pdo_disable_update_statement) {
            DebugConsole::insert_error('database::update', 'PDO-Update statement is disabled!!!');
        }

        if(pdo_disable_insert_statement) {
            DebugConsole::insert_error('database::insert', 'PDO-Insert statement is disabled!!!');
        }

        if(pdo_disable_delete_statement) {
            DebugConsole::insert_error('database::delete', 'PDO-Delete statement is disabled!!!');
        }
        
        if (!isset($this->dbConf[$active])) {
            throw new Exception("Unexisting db-config $active");
        }

        if (!isset($this->instances[$active]) || $this->instances[$active] instanceOf database === false) {
            $this->instances[$active] = new database();
            $this->instances[$active]->setConfig($active,$this->dbConf[$active]);
            $this->instances[$active]->connect($active);
        }

        return $this->instances[$active];
    }

    public final function disconnect($active = "") {
        if(empty($active)) {
            unset($this->instances[$this->active]);
        } else {
            unset($this->instances[$active]);
        }

        $this->dbHandle = null;
    }

    public function getHandle() {
        return $this->dbHandle;
    }

    public function lastInsertId() {
        return $this->lastInsertId;
    }

    public function rowCount() {
        return $this->rowCount;
    }
    
    public function rows($qry, array $params = array()) {
        if (($type = $this->getQueryType($qry)) !== "select") {
            throw new Exception("Incorrect Delete Query");
        }

        $this->run_query($qry, $params, $type);
        return $this->rowCount;
    }
    
    public function delete($qry, array $params = array()) {
        if(pdo_disable_delete_statement) {
            return false;
        }
        
        if (($type = $this->getQueryType($qry)) !== "delete") {
            throw new Exception("Incorrect Delete Query");
        }

        return $this->run_query($qry, $params, $type);
    }

    public function update($qry, array $params = array()) {
        if(pdo_disable_update_statement) {
            return false;
        }
        
        if (($type = $this->getQueryType($qry)) !== "update") {
            throw new Exception("Incorrect Update Query");
        }

        return $this->run_query($qry, $params, $type);
    }

    public function insert($qry, array $params = array()) {
        if(pdo_disable_insert_statement) {
            return false;
        }
        
        if (($type = $this->getQueryType($qry)) !== "insert") {
            throw new Exception("Incorrect Insert Query");
        }

        return $this->run_query($qry, $params, $type);
    }

    public function select($qry, array $params = array()) {
        if (($type = $this->getQueryType($qry)) !== "select") {
            throw new Exception("Incorrect Select Query");
        }

        if ($stmnt = $this->run_query($qry, $params, $type)) {
            return $stmnt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return array();
        }
    }

    public function selectSingle($qry, array $params = array(), $field = false) {
        if (($type = $this->getQueryType($qry)) !== "select") {
            throw new Exception("Incorrect Select Query");
        }

        if ($stmnt = $this->run_query($qry, $params, $type)) {
            $res = $stmnt->fetch(PDO::FETCH_ASSOC);
            return ($field === false) ? $res : $res[$field];
        } else {
            return false;
        }
    }

    public final function query($qry) {
        $this->lastInsertId = false;
        $this->rowCount = false;
        $this->rowCount = $this->dbHandle->exec($qry);
        $this->queryCounter++;
    }

    public function getQueryCounter() {
        return $this->queryCounter;
    }

    public function quote($str) {
        return $this->dbHandle->quote($str);
    }

    /************************
     * Protected
     ************************/
    
    /**
     * Erstellt das PDO Objekt mit vorhandener Konfiguration
     * @namespace system\database
     * @category PDO Database
     * @param string $active = "default"
     * @throws PDOException
     */
    protected final function connect($active = "default") {
        if (!isset($this->dbConf[$active])) {
            throw new PDOException("No supported connection scheme");
        }

        $dbConf = $this->dbConf[$active];
        try {
            if (!$dsn = $this->dsn($active)) {
                throw new Exception("PDO driver is missing");
            }

            $db = new PDO($dsn, $dbConf['db_user'], $dbConf['db_pw']);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->query("set character set utf8");
            $db->query("set names utf8");

            $this->dbHandle = $db;
            $this->active = $active; //mark as active
        } catch (PDOException $ex) {
            throw new PDOException("Connection Exception: " . $ex->getMessage());
        }
    }
    
    protected final function run_query($qry, array $params, $type) {
        if (in_array($type, array("insert", "select", "update", "delete")) === false) {
            throw new Exception("Unsupported Query Type");
        }

        // replace sql prefix
        if(strpos($qry,"{prefix_")!==false) {
            $qry = preg_replace_callback("#\{prefix_(.*?)\}#",function($tb) { 
                global $db; 
                return str_ireplace($tb[0],$db['prefix'].$tb[1],$tb[0]); 
            },$qry);
        }

        //Debug
        if(show_pdo_delete_debug || show_pdo_delete_debug || show_pdo_delete_debug || show_pdo_delete_debug) {
            DebugConsole::insert_sql_info('database::run_query('.$type.')',$qry,$params);
        }

        $this->lastInsertId = false;
        $this->rowCount = false;
        $stmnt = $this->active_driver == 'mysql' ? $this->dbHandle->prepare($qry, array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => $this->mysql_buffered_query)) : $this->dbHandle->prepare($qry);

        try
        {
            $success = (count($params) !== 0) ? $stmnt->execute($params) : $stmnt->execute();
            $this->queryCounter++;

            if (!$success) {
                return false;
            }

            if ($type === "insert") {
                $this->lastInsertId = $this->dbHandle->lastInsertId();
            }

            $this->rowCount = $stmnt->rowCount();

            return ($type === "select") ? $stmnt : true;
        } catch (PDOException $ex) {
            throw new PDOException("PDO-Exception: " . $ex->getMessage());
        }
    }

    protected final function check_driver($use_driver) {
        foreach(PDO::getAvailableDrivers() as $driver) {
            if ($use_driver == $driver) {
                return true;
            }
        }

        return false;
    }

    protected final function dsn($active) {
        $dbConf = $this->dbConf[$active];
        if (!$this->check_driver($dbConf['driver'])) {
            return false;
        }

        $this->active_driver = $dbConf['driver'];
        $dsn= sprintf('%s:', $dbConf['driver']);
        switch($dbConf['driver']) {
            case 'mysql':
            case 'pgsql':
                $dsn .= sprintf('host=%s;dbname=%s', $dbConf['db_host'], $dbConf['db']);
                break;
            case 'sqlsrv':
                $dsn .= sprintf('Server=%s;1433;Database=%s', $dbConf['db_host'], $dbConf['db']);
                if ($this->connection_pooling) {
                    $dsn .= ';ConnectionPooling=1';
                }
                
                if($this->connection_encrypting) {
                    $dsn .= ';Encrypt=1';
                }
                break;
        }

        return $dsn;
    }

    protected function getQueryType($qry) {
        list($type, ) = explode(" ", strtolower($qry), 2);
        return $type;
    }
}