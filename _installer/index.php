<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

ob_start();
    session_start();
    if((isset($_SESSION['db_install']) ? $_SESSION['db_install'] : false) == true && (isset($_GET['action']) ? $_GET['action'] : '') == '')
        $_SESSION['db_install'] = false;

    $_SESSION['setup_step'] = (!isset($_GET['action']) ? (!empty($_SESSION['setup_step']) ? $_SESSION['setup_step'] : '') : $_GET['action']);
    
    define('basePath', dirname(dirname(__FILE__).'../'));
    require_once(basePath.'/_installer/system/core.php');
    $index = '';

    switch (isset($_GET['action']) ? $_GET['action'] : ''):
    default:
        require_once(basePath.'/_installer/index/default.php');
    break;
    case 'installtype'; //Auswahl: Installation / Update
        require_once(basePath.'/_installer/index/installtype.php');
    break;
    case'ftp'; //Schreibrechte Prüfen
        require_once(basePath.'/_installer/index/ftp.php');
    break;
    case'prepare'; //Schreibrechte Prüfen
        require_once(basePath.'/_installer/index/prepare.php');
    break;
    case'mysql'; //MySQL Verbindung abfragen & herstellen
        require_once(basePath.'/_installer/index/mysql.php');
    break;
    case'mysql_setup'; //MySQL Config schreiben
        require_once(basePath.'/_installer/index/mysql_setup.php');
    break; //Tabellen anlegen
    case 'mysql_setup_tb';
        require_once(basePath.'/_installer/index/mysql_setup_tb.php');
    break;
    case 'mysql_setup_users'; //Administrator anlegen,Erste Konfiguration etc.
        require_once(basePath.'/_installer/index/mysql_setup_users.php');
    break;
    case 'done'; require_once(basePath.'/_installer/index/done.php'); break;
    endswitch;

    switch (_edition) {
        case 'dev': $edition = 'Development'; break;
        case 'society': $edition = 'Society'; break;
        default: $edition = 'Stable'; break;
    }
    
    echo show("body", array("index" => $index, "edition" => $edition, "steps" => steps(), "ctime" => date("Y", time())));

    if((isset($_GET['action']) ? $_GET['action'] : '') == 'done')
        $_SESSION['installer'] = false;

    if(isset($_GET['action']) ? $_GET['action'] : '' == 'done')
        unset($_SESSION['setup_step']);

ob_end_flush();