<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('INSTALLER')) exit();

if($_COOKIE['agb'] =! true)
    $index = show("/msg/agb_error");
else {
    //Updater
    $use_mysql_config = (file_exists(basePath."/inc/mysql.php") && $_SESSION['type'] == 1) ? true : false;
    $sql_host = ''; $sql_db = ''; $sql_prefix = ''; $sql_user = ''; $sql_pass = '';
    if($use_mysql_config) {
        $sql_host = $database->getConfig('db_host');
        $sql_db = $database->getConfig('db');
        $sql_prefix = $database->getConfig('prefix');
        $sql_user = $database->getConfig('db_user');
        $sql_pass = $database->getConfig('db_pw');
        $sql_engine = $database->getConfig('db_engine');
    }
    
    $mysql_host = isset($_POST['host']) ? $_POST['host'] : ($use_mysql_config ? $sql_host : 'localhost');
    $mysql_database = isset($_POST['database']) ? $_POST['database'] : ($use_mysql_config ? $sql_db : 'dzcp');
    $mysql_prefix = isset($_POST['prefix']) ? $_POST['prefix'] : ($use_mysql_config ? $sql_prefix : 'dzcp_');
    $mysql_user = isset($_POST['user']) ? $_POST['user'] : ($use_mysql_config ? $sql_user : '');
    $mysql_pwd = isset($_POST['pwd']) ? $_POST['pwd'] : ($use_mysql_config ? $sql_pass : '');
    $mysql_engine = isset($_POST['dbEngine']) ? $_POST['dbEngine'] : ($use_mysql_config ? $sql_engine : 'default');
    
    $msg=''; $nextlink=''; $dbe_selected0 = ''; $dbe_selected1 = ''; $dbe_selected2 = ''; $dbe_selected3 = ''; $dbe_selected4 = ''; $disabled = '';
    if(isset($_GET['do']) || $use_mysql_config) {
        if(isset($_GET['do']) ? ($_GET['do'] == 'test_mysql') : false || $use_mysql_config) {
            //Set Config in Sessions
            $_SESSION['mysql_password'] = $mysql_pwd;
            $_SESSION['mysql_user'] = $mysql_user;
            $_SESSION['mysql_prefix'] = $mysql_prefix;
            $_SESSION['mysql_database'] = $mysql_database;
            $_SESSION['mysql_host'] = $mysql_host;
            $_SESSION['mysql_engine'] = $mysql_engine;
            #########################

            $dbe_selected0 = ($_SESSION['mysql_engine'] == 0 ? 'selected="selected"' : '');
            $dbe_selected1 = ($_SESSION['mysql_engine'] == 1 ? 'selected="selected"' : '');
            $dbe_selected2 = ($_SESSION['mysql_engine'] == 2 ? 'selected="selected"' : '');
            $dbe_selected3 = ($_SESSION['mysql_engine'] == 3 ? 'selected="selected"' : '');

            if($mysql_prefix != NULL) {
                $posi = (strpos($_SESSION['mysql_host'], ':') !== false ? true : false);
                $exp = ($posi ? explode(':',$_SESSION['mysql_host']) : $_SESSION['mysql_host']);
                if(!fsockopen_support() || ping_port(($posi ? $exp[0] : $exp), ($posi ? $exp[1] : 3306))) {
                    $database->setConfig('test',array("prefix" => "","driver" => "mysql", "db" => $_SESSION['mysql_database'], "db_host" => $_SESSION['mysql_host'], 
                        "db_user" => $_SESSION['mysql_user'], "db_pw" => $_SESSION['mysql_password'], "persistent" => false));
                    $test = $database->getInstance('test');
                    if($test['status'] && !$test['code']) { $database->disconnect('test');
                    
                        if($_SESSION['type'] != 1) {
                            $database->setConfig('default',array("prefix" => "","driver" => "mysql", "db" => $_SESSION['mysql_database'], "db_engine" => $_SESSION['mysql_engine'], "db_host" => $_SESSION['mysql_host'], 
                                "db_user" => $_SESSION['mysql_user'], "db_pw" => $_SESSION['mysql_password'], "persistent" => false));
                        }
                        
                        $sql = $database->getInstance();
                        
                        //Updater
                        if($_SESSION['type'] == 1) { //Update
                            $_SESSION['mysql_engine'] = $sql->getConfig('db_engine');
                            $dbe_selected0 = ($_SESSION['mysql_engine'] == 0 ? 'selected="selected"' : '');
                            $dbe_selected1 = ($_SESSION['mysql_engine'] == 1 ? 'selected="selected"' : '');
                            $dbe_selected2 = ($_SESSION['mysql_engine'] == 2 ? 'selected="selected"' : '');
                            $dbe_selected3 = ($_SESSION['mysql_engine'] == 3 ? 'selected="selected"' : '');
                        }
                        //End
                        
                        switch($_SESSION['mysql_engine']) {
                            case 'aria':
                                //Aria
                                if(!check_db_aria($sql))
                                    $msg = writemsg(mysql_no_aria,true);
                                else {
                                    $msg = writemsg(mysql_ok,false);
                                    $nextlink = show("/msg/nextlink",array("ac" => 'action=mysql_setup'));
                                    $disabled = 'disabled="disabled"';
                                }
                            break;
                            default:
                                $msg = writemsg(mysql_ok,false);
                                $nextlink = show("/msg/nextlink",array("ac" => 'action=mysql_setup'));
                                $disabled = 'disabled="disabled"';
                            break;
                        }

                        $sql->disconnect();
                    } else
                        $msg = writemsg(mysql_no_login,true);
                } else
                    $msg = writemsg(mysql_no_con_server,true);
            } else
                $msg = writemsg(mysql_no_prefix,true);
        }
    }

    $index = show("mysql",array("disabled" => $disabled, "mysql_host" => $mysql_host, "mysql_database" => $mysql_database, "mysql_prefix" => $mysql_prefix, "mysql_user" => $mysql_user,
    "mysql_pwd" => $mysql_pwd, "msg" => $msg, "next" => $nextlink, "dbe_selected0" => $dbe_selected0, "dbe_selected1" => $dbe_selected1, "dbe_selected2" => $dbe_selected2, 
        "dbe_selected3" => $dbe_selected3));
}