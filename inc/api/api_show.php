<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

/*
 * Usage:
 * $show_addon = new api_show();
 * $show_addon->set_tpl_dir('inc/additional-functions/addon_xxx/template'):
 * $show = $show_addon->show('datei',array('test' => 'hallo'));
 */

//API-Class * Show * Constructor
class api_show {
    private $addon_tpl_dir = 'inc/additional-functions/addon_xxx/template';
    private $addon_lang_constant = array();
    private $addon_block_pholder = array();
    
    public function __construct() {
        $this->addon_tpl_dir = '';
        $this->addon_lang_constant = array();
        $this->addon_block_pholder = array();
    }
    
    //Set Addon Template Dir
    public function set_tpl_dir($dir) {
        if(!empty($dir)) {
            $this->addon_tpl_dir = $dir;
            return true;
        }
        
        return false;
    }

    //Set Addon Language constants
    public function set_lang_constant($array=array()) {
        if(!is_array($array)) {
            $this->addon_lang_constant = $array;
            return true;
        }
        
        return false;
    }
    
    //Set Addon Block placeholders
    public function set_block_pholder($array=array()) {
        if(!is_array($array)) {
            $this->addon_block_pholder = $array;
            return true;
        }
        
        return false;
    }
    
    //Call Show
    public function show($tpl="", $array=array()) {
        return show_runner($tpl,$this->addon_tpl_dir,$array,$this->addon_lang_constant,$this->addon_block_pholder,$this->addon_tpl_dir);
    }
}