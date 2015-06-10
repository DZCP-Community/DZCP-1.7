<?php
/**
 * tiny_mce_gzip.php
 *
 * Copyright 2010, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

define('basePath', dirname(dirname(dirname(__FILE__))));
ob_start();

## Require ##
$ajaxJob = true;
include(basePath."/inc/common.php");

// Handle incoming request if it's a script call
if (TinyMCE_Compressor::getParam("js")) {
    // Default settings
    $tinyMCECompressor = new TinyMCE_Compressor();

    // Handle request, compress and stream to client
    $tinyMCECompressor->handleRequest();
}

class TinyMCE_Compressor {
    private $files, $settings;
    private static $defaultSettings = array(
        "plugins"    => "",
        "themes"     => "",
        "languages"  => "",
        "disk_cache" => true,
        "expires"    => "30d",
        "cache_dir"  => "",
        "compress"   => true,
        "suffix"     => "",
        "files"      => "",
        "source"     => false);

    /**
     * Constructs a new compressor instance.
     *
     * @param Array $settings Name/value array with non-default settings for the compressor instance.
     */
    public function __construct($settings = array()) {
        $this->settings = array_merge(self::$defaultSettings, $settings);

        if (empty($this->settings["cache_dir"])) {
            $this->settings["cache_dir"] = dirname(__FILE__);
        }
    }

    /**
     * Adds a file to the concatenation/compression process.
     *
     * @param String $path Path to the file to include in the compressed package/output.
     */
    public function &addFile($file) {
        $this->files .= ($this->files ? "," : "") . $file;
        return $this;
    }

    /**
     * Handles the incoming HTTP request and sends back a compressed script depending on settings and client support.
     */
    public function handleRequest() {
        global $cache;
        $files = array();
        $expiresOffset = $this->parseTime($this->settings["expires"]);
        $tinymceDir = dirname(__FILE__);
        $cacheHash = md5(implode($_GET));
        if (true || !$cache->isExisting($cacheHash)) {
            // Override settings with querystring params
            if ($plugins = self::getParam("plugins")) {
                $this->settings["plugins"] = $plugins;
            }
            $plugins = explode(',', $this->settings["plugins"]);

            if ($themes = self::getParam("themes")) {
                $this->settings["themes"] = $themes;
            }
            $themes = explode(',', $this->settings["themes"]);

            if ($languages = self::getParam("languages")) {
                $this->settings["languages"] = $languages;
            }
            $languages = explode(',', $this->settings["languages"]);

            if ($tagFiles = self::getParam("files")) {
                $this->settings["files"] = $tagFiles;
            }

            $src = self::getParam("src");
            if ($src) {
                $this->settings["source"] = ($src === "true");
            }

            // Add core
            $files[] = "tiny_mce";
            foreach ($languages as $language) {
                $files[] = "langs/$language";
            }

            // Add plugins
            foreach ($plugins as $plugin) {
                $files[] = "plugins/$plugin/editor_plugin";
                foreach ($languages as $language) {
                    $files[] = array("file"=>"plugins/$plugin/langs/$language");
                }
            }

            // Add themes
            foreach ($themes as $theme) {
                $files[] = "themes/$theme/editor_template";
                foreach ($languages as $language) {
                    $files[] = "themes/$theme/langs/$language";
                }
            }

            // Add any specified files.
            $allFiles = array_merge($files, explode(',', $this->settings['files']));
            $newallFiles = array();
            foreach ($allFiles as $id => $file) {
                if(empty($file)) continue; $lang = false;
                if(is_array($file)) {
                    $lang = true;
                    $file = $file['file'];
                }
                
                if (file_exists($file . ".js")) {
                    $file .= ".js";
                    $newallFiles[$id] = $file;
                } else if ($this->settings["source"] && file_exists($file . "_src.js")) {
                    $file .= "_src.js";
                    $newallFiles[$id] = $file;
                } else {
                    if(!$lang) {
                        $message = '#####################################################################'.EOL.
                        'Datum           = '.date("d.m.y H:i", time()).EOL.
                        'Message         = TinyMCE Files not found'.EOL.
                        'Compressed File = '.$file.'.js'.EOL.
                        'Source File     = '.$file.'_src.js'.EOL.
                        '#####################################################################'.EOL.EOL;
                        $fp = fopen(basePath."/inc/_logs/tinymce_compressor.log", "a+");
                        fwrite($fp, $message); fclose($fp);
                    }
                }
            }
            unset($allFiles, $file);

            // Set base URL for where tinymce is loaded from
            $buffer = "var tinyMCEPreInit={base:'" . dirname($_SERVER["SCRIPT_NAME"]) . "',suffix:''};";

            // Load all tinymce script files into buffer
            foreach ($newallFiles as $file) {
                if ($file) {
                    $fileContents = $this->getFileContents($tinymceDir . "/" . $file);
                    $buffer .= $fileContents;
                }
            }

            // Mark all themes, plugins and languages as done
            $buffer .= 'tinymce.each("' . implode(',', $files) . '".split(","),function(f){tinymce.ScriptLoader.markDone(tinyMCE.baseURL+"/"+f+".js");});';
            $cache->set($cacheHash, $buffer, $expiresOffset);
        } else {
            $buffer = $cache->get($cacheHash);
        }

        // Check if it supports gzip
        $zlibOn = ini_get('zlib.output_compression') || (ini_set('zlib.output_compression', 0) === false);
        $encodings = (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) ? strtolower($_SERVER['HTTP_ACCEPT_ENCODING']) : "";
        $encoding = preg_match( '/\b(x-gzip|gzip)\b/', $encodings, $match) ? $match[1] : "";

        // Is northon antivirus header
        if (isset($_SERVER['---------------'])) {
            $encoding = "x-gzip";
        }

        $supportsGzip = $this->settings['compress'] && !empty($encoding) && !$zlibOn && function_exists('gzencode');

        // Set headers
        header("Content-type: text/javascript");
        header("Vary: Accept-Encoding");  // Handle proxies
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
        header("Cache-Control: public, max-age=" . $expiresOffset);

        if ($supportsGzip) {
            header("Content-Encoding: " . $encoding);
            $buffer = gzencode($buffer, 9, FORCE_GZIP);
        }
        
        exit($buffer);
    }

    /**
     * Renders a script tag that loads the TinyMCE script.
     *
     * @param Array $settings Name/value array with settings for the script tag.
     * @param Bool  $return   The script tag is return instead of being output if true
     * @return String the tag is returned if $return is true
     */
    public static function renderTag($tagSettings, $return = false) {
        $settings = array_merge(self::$defaultSettings, $tagSettings);

        if (empty($settings["cache_dir"])) {
            $settings["cache_dir"] = dirname(__FILE__);
        }

        $scriptSrc = $settings["url"] . "?js=1";

        // Add plugins
        if (isset($settings["plugins"])) {
            $scriptSrc .= "&plugins=" . (is_array($settings["plugins"]) ? implode(',', $settings["plugins"]) : $settings["plugins"]);
        }

        // Add themes
        if (isset($settings["themes"])) {
            $scriptSrc .= "&themes=" . (is_array($settings["themes"]) ? implode(',', $settings["themes"]) : $settings["themes"]);
        }

        // Add languages
        if (isset($settings["languages"])) {
            $scriptSrc .= "&languages=" . (is_array($settings["languages"]) ? implode(',', $settings["languages"]) : $settings["languages"]);
        }

        // Add disk_cache
        if (isset($settings["disk_cache"])) {
            $scriptSrc .= "&diskcache=" . ($settings["disk_cache"] === true ? "true" : "false");
        }

        // Add any explicitly specified files if the default settings have been overriden by the tag ones
        /*
         * Specifying tag files will override (rather than merge with) any site-specific ones set in the
         * TinyMCE_Compressor object creation.  Note that since the parameter parser limits content to alphanumeric
         * only base filenames can be specified.  The file extension is assumed to be ".js" and the directory is
         * the TinyMCE root directory.  A typical use of this is to include a script which initiates the TinyMCE object.
         */
        if (isset($tagSettings["files"])) {
            $scriptSrc .= "&files=" . (is_array($settings["files"]) ? implode(',', $settings["files"]) : $settings["files"]);
        }

        // Add src flag
        if (isset($settings["source"])) {
            $scriptSrc .= "&src=" . ($settings["source"] === true ? "true" : "false");
        }

        $scriptTag = '<script type="text/javascript" src="' . htmlspecialchars($scriptSrc) . '"></script>';

        if ($return) {
            return $scriptTag;
        } else {
            echo $scriptTag;
        }
    }

    /**
     * Returns a sanitized query string parameter.
     *
     * @param String $name Name of the query string param to get.
     * @param String $default Default value if the query string item shouldn't exist.
     * @return String Sanitized query string parameter value.
     */
    public static function getParam($name, $default = "") {
        if (!isset($_GET[$name])) {
            return $default;
        }

        return preg_replace("/[^0-9a-z\-_,]+/i", "", $_GET[$name]); // Sanatize for security, remove anything but 0-9,a-z,-_,
    }

    /**
     * Parses the specified time format into seconds. Supports formats like 10h, 10d, 10m.
     *
     * @param String $time Time format to convert into seconds.
     * @return Int Number of seconds for the specified format.
     */
    private function parseTime($time) {
        $multipel = 1;

        // Hours
        if (strpos($time, "h") > 0) {
            $multipel = 3600;
        }

        // Days
        if (strpos($time, "d") > 0) {
            $multipel = 86400;
        }

        // Months
        if (strpos($time, "m") > 0) {
            $multipel = 2592000;
        }

        // Trim string
        return intval($time) * $multipel;
    }

    /**
     * Returns the contents of the script file if it exists and removes the UTF-8 BOM header if it exists.
     *
     * @param String $file File to load.
     * @return String File contents or empty string if it doesn't exist.
     */
    private function getFileContents($file) {
        $content = file_get_contents($file);

        // Remove UTF-8 BOM
        if (substr($content, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
            $content = substr($content, 3);
        }

        return $content;
    }
}
ob_end_flush();