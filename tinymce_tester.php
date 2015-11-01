<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'false';
$data['core'] = 'false';
$data['headers'] = 'false';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = '';
$data['languages'] = 'de';
$data['debug'] = 'true';

tinymce_tester::setTest($page="http://jfv-vorderpfalz.de", $data); //Basic & with out headers

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'true';
$data['core'] = 'false';
$data['headers'] = 'false';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = '';
$data['languages'] = 'de';
$data['debug'] = 'false';

tinymce_tester::setTest($page, $data); //Cache
tinymce_tester::setTest($page, $data); //From Cache

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'false';
$data['core'] = 'true';
$data['headers'] = 'false';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = '';
$data['languages'] = 'de';
$data['debug'] = 'true';

tinymce_tester::setTest($page, $data); //With Core

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'false';
$data['core'] = 'true';
$data['headers'] = 'true';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = '';
$data['languages'] = 'de';
$data['debug'] = 'true';

tinymce_tester::setTest($page, $data); //With Core & Headers

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'false';
$data['core'] = 'false';
$data['headers'] = 'false';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = '';
$data['languages'] = 'de,en';
$data['debug'] = 'true';

tinymce_tester::setTest($page, $data); //Languages

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'false';
$data['core'] = 'true';
$data['headers'] = 'true';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = '';
$data['languages'] = 'de,en';
$data['debug'] = 'true';

tinymce_tester::setTest($page, $data); //Languages & Headers & Core

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'true';
$data['core'] = 'true';
$data['headers'] = 'false';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = 'contextmenu,dzcp,advimage,paste,table,fullscreen,inlinepopups,spellchecker,searchreplace,insertdatetime,dzcp';
$data['languages'] = 'de,en';
$data['debug'] = 'false';

tinymce_tester::setTest($page, $data); //Languages & Headers & Core & Plugins & Cache
tinymce_tester::setTest($page, $data); //From Cache

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'true';
$data['core'] = 'true';
$data['headers'] = 'true';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = 'contextmenu,dzcp,advimage,paste,table,fullscreen,inlinepopups,spellchecker,searchreplace,insertdatetime,dzcp';
$data['languages'] = 'de,en';
$data['debug'] = 'true';

tinymce_tester::setTest($page, $data); //Languages & Headers & Core & Plugins & Cache & Headers

$data = array();
$data['js'] = 'true';
$data['compress'] = 'false';
$data['diskcache'] = 'true';
$data['core'] = 'true';
$data['headers'] = 'true';
$data['suffix'] = 'context_demo';
$data['themes'] = 'advanced';
$data['plugins'] = 'contextmenu,dzcp,advimage,paste,table,fullscreen,inlinepopups,spellchecker,searchreplace,insertdatetime,dzcp';
$data['languages'] = 'de,en';
$data['debug'] = 'false';

tinymce_tester::setTest($page, $data); //Languages & Headers & Core & Plugins & Cache & Headers & no Debug


tinymce_tester::runTest();
class tinymce_tester {
    static $tests = array();
    
    public static function setTest($host,$options) {
        self::$tests[] = array('url' => $host, 'query' => $options);
    }
    
    public static function runTest() {
        self::test();
        foreach (self::$tests as $id => $test) {
            echo '<br>###########################################################################################<br>';
            echo '######################################## TEST ID: '.$id.' ########################################<br>';
            echo '###########################################################################################<br>';
            echo $test['running_test'];
        }
    }
    
    private static function test() {
        foreach (self::$tests as $id => $test) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $test['url'].'/inc/tinymce/tiny_mce_gzip.php?'.http_build_query($test['query']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
            self::$tests[$id]['running_test'] = curl_exec($ch);   
        }
    }
    
    
}