<?php
/**
 * <DZCP-Extended Edition>
 * @package: DZCP-Extended Edition
 * @author: DZCP Developer Team || Hammermaps.de Developer Team
 * @link: http://www.dzcp.de || http://www.hammermaps.de
 */

/* Charset */
define('_charset', 'iso-8859-1');

define('_true', '<img src="../_installer/html/img/true.gif" border="0" alt="" vspace="0" align="center"> ');
define('_false', '<img src="../_installer/html/img/false.gif" border="0" alt="" vspace="0" align="center"> ');
define('version_input', "<tr><td><table class=\"info\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>
<td width=\"0\" height=\"0\" valign=\"middle\"><input type=\"radio\" [disabled] [checked] name=\"version\" id=\"version\" value=\"[version_num]\" /> DZCP-[version_num_view]</td></tr></table></td></tr>");
define('_step', '<td></td><td>[text]</td></tr>');

define('_link_start', '<font class="enabled">&raquo; Lizenz</font>');
define('_link_start_1', '<font class="disabled">1. Lizenz</font>');

define('_link_type', '<font class="enabled">&raquo; Setup Type</font>');
define('_link_type_1', '<font class="disabled">2. Setup Type</font>');

define('_link_ftp', '<font class="enabled">&raquo; FTP Zugriff</font>');
define('_link_ftp_1', '<font class="disabled">3. FTP Zugriff</font>');

define('_link_prepare', '<font class="enabled">&raquo; Vorbereitung</font>');
define('_link_prepare_1', '<font class="disabled">4. Vorbereitung</font>');

define('_link_mysql', '<font class="enabled">&raquo; MySQL</font>');
define('_link_mysql_1', '<font class="disabled">5. MySQL</font>');

define('_link_db', '<font class="enabled">&raquo; Speichern</font>');
define('_link_db_1', '<font class="disabled">6. Speichern</font>');

define('_link_update', '<font class="enabled">&raquo; Update</font>');
define('_link_update_1', '<font class="disabled">7. Update</font>');

define('_link_adminacc', '<font class="enabled">&raquo; Account</font>');
define('_link_adminacc_1', '<font class="disabled">8. Account</font>');

define('_link_done', '<font class="enabled">&raquo; Fertig</font>');
define('_link_done_1', '<font class="disabled">9. Fertig</font>');

//Texte
define('_error', 'Fehler');
define('_successful', 'Erfolgreich');
define('_warn', 'Hinweis');
define('prepare_no_ftp', 'Ihr Webserver unterst&uuml;tz eine der Funktionen <i>ftp_connect()</i>, <i>ftp_login()</i> oder <i>ftp_site()</i> nicht!
Diese sind jedoch notwendig um eine automatische Rechtevergabe der Dateien durchzuf&uuml;hren. Bitte aktiviere Sie diese oder setzen Sie manuell mittels
FTP-Client die notwendigen Rechte und aktualisieren Sie die Seite.');
define('prepare_no_ftp_connect', 'Der angegeben FTP-Host ist nicht erreichbar!<p>Bitte &uuml;berpr&uuml;fen Sie ihre Eingaben oder setzen die Dateirechte manuell per FTP-Client.');
define('prepare_no_ftp_login', 'Die angegeben Login-Daten wurden zur&uuml;ckgewiesen!<p>Bitte &uuml;berpr&uuml;fen Sie ihre Eingaben oder setzen die Dateirechte manuell per FTP-Client.');
define('prepare_files_error', 'Nicht alle notwendigen Dateirechte sind gesetzt, bitte verwenden Sie unsere "Automatische Rechtevergabe" oder setzen Sie manuell mittels FTP-Client die notwendigen Rechte und aktualisieren Sie diese Seite');
define('prepare_files_error_non_ftpauto', 'Nicht alle notwendigen Dateirechte sind gesetzt, setzen Sie die Rechte manuell mittels FTP-Client und aktualisieren Sie die diese Seite');
define('no_webmail', 'Sie haben keine Page E-Mail Adresse eingetragen!<p>&Uuml;berpr&uuml;fen Sie ihre Eingaben und wiederholen Sie den Vorgang.');
define('no_username', 'Sie haben keinen Usernamen eingegeben!<p>&Uuml;berpr&uuml;fen Sie ihre Eingaben und wiederholen Sie den Vorgang.');
define('no_pwd', 'Sie haben kein Passwort eingegeben!<p>&Uuml;berpr&uuml;fen Sie ihre Eingaben und wiederholen Sie den Vorgang.');
define('no_nick', 'Sie haben keinen Nicknamen eingegeben!<p>&Uuml;berpr&uuml;fen Sie ihre Eingaben und wiederholen Sie den Vorgang.');
define('no_email', 'Sie haben keine E-Mail Adresse eingetragen!<p>&Uuml;berpr&uuml;fen Sie ihre Eingaben und wiederholen Sie den Vorgang.');
define('no_clanname', 'Sie haben keinen Clannamen eingetragen!<p>&Uuml;berpr&uuml;fen Sie ihre Eingaben und wiederholen Sie den Vorgang.');
define('mysql_no_prefix', 'Das SQL-Prefix muss angegeben werden!');
define('mysql_no_login', 'Es konnte keine Verbindung zur Datenbank aufgebaut werden!<p>&Uuml;berpr&uuml;fen Sie User und Passwort!');
define('mysql_no_db', 'Die angegebene Datenbank konnte nicht gefunden werden!<p>&Uuml;berpr&uuml;fen Sie den eingegebenen Datenbanknamen!');
define('mysql_no_con_server', 'Es konnte keine Verbindung zur Datenbank aufgebaut werden!<p>&Uuml;berpr&uuml;fen Sie Host und Port des Servers.');
define('mysql_ok', 'Die MySQL-Verbindung wurde erfolgreich getestet!<p>Klicken Sie nun auf \'Weiter\'.');
define('mysql_no_aria', 'Der MySQL Server unterst&uuml;tzt kein Aria!<p>Die Aria Engine kann nur auf einem MariaDB Server verwendet werden.<br /><br />Sehe <a href="http://www.mariadb.org/" target="_blank">MariaDB</a>');
define('mysql_setup_saved', 'Die MySQL-Daten wurden erfolgreich gespeichert!<p>Klicken Sie auf weiter um mit der Datenbankinstallation zu beginnen.');
define('mysql_setup_created', 'Die MySQL Basisstruktur wurde angelegt!<p>Klicken Sie auf weiter um mit der Datenbankeinrichtung zu beginnen.');
define('prepare_files_success', 'Alle notwendigen Dateirechte sind gesetzt. <p> Klicken Sie unten rechts auf Weiter um fortzufahren.');
define('saved_user', 'Die Datenbank Informationen wurden erfolgreich gespeichert!<p>Klicken Sie auf &quot;Weiter&quot;.');
define('no_db_update', 'Die Datenbank ist bereits aktuell, es ist kein Update deiner Datenbank notwendig.');
define('no_db_update_selected', 'Du musst die zuvor installierte Version von DZCP ausw&auml;hlen um mit dem Update zu beginnen!');
define('_error_invalid_email', 'Du hast eine ung&uuml;ltige Emailadresse angegeben!');
define('_error_invalid_email_web', 'E-Mail Absender (Webseite): <p> Du hast eine ung&uuml;ltige Emailadresse angegeben! <p> Die Adresse muss im Format: "username@domain.de" sein, auﬂerdem muss die angegebene Adresse existieren.');
define('php_version_error', 'deV!L`z Clanportal erfordert PHP 5.4.0 oder h&ouml;her!');
define('ftp_files_success', 'Der FTP Zugriff wurde erfolgreich getestet!<p>Klicken Sie unten rechts auf Weiter um fortzufahren.');
