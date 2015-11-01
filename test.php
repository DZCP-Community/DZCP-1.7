<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(!defined('T_ML_COMMENT')) { 
    define('T_ML_COMMENT', T_COMMENT); 
}  elseif(!defined('T_DOC_COMMENT')) {  
    define('T_DOC_COMMENT', T_ML_COMMENT); 
}

final class phpCompressor {
    private $compressedFile = '.gzs';
    private $controlFile = '.gzc';
    
    public function remove($dir='',$filename='') {
        $filename_0 = substr($filename, 0, -4);
        $GZFileName = $filename_0.$this->compressedFile;
        $GZControlFileName = $filename_0.$this->controlFile;
        if(file_exists($dir.$GZControlFileName)) { unlink($dir.$GZControlFileName); }
        if(file_exists($dir.$GZFileName)) { unlink($dir.$GZFileName); }
    }
    
    public function load($dir='',$filename='') {
         if(file_exists($dir.$filename)) {
            //===============================
            //Config
            //===============================
            $filename_0 = substr($filename, 0, -4);
            $GZFileName = $filename_0.$this->compressedFile;
            $GZControlFileName = $filename_0.$this->controlFile;
            
            //===============================
            //Load Pack
            //===============================
             if(apc_exists($GZControlFileName)) {
                //===============================
                //Load ControlPACK
                //===============================
                $filePackControl = apc_fetch($GZControlFileName);
                if (substr($filePackControl, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
                    $filePackControl = substr($filePackControl, 3);
                }
                
                if(empty($filePackControl) || !$filePackControl) return false;
                $fileInfo = unserialize($filePackControl); unset($filePackControl);
                if(!$fileInfo) return false;
                if(apc_exists($fileInfo['file'])) { //Check is FilePACK exists
                    $fileStats = stat($dir.$filename);
                    if($fileStats['mtime'] == $fileInfo['mtime']) {
                        if(show_compressor_debug) {
                            DebugConsole::insert_info('phpCompressor::load()', '"'.$filename.'" => Size: '.$this->FileSizeConvert(filesize($dir.$filename)).' '
                                . 'loaded as "'.$fileInfo['file'].'" => Loaded Size: '.$this->FileSizeConvert(filesize($dir.$fileInfo['file'])));
                        }
                        $code = $this->decompress($fileInfo['file']);
                        if(empty($code) || !$code) return false;
                        return $code;
                    } else {
                        if(!$this->compress($dir,$filename)) return false;
                        if(show_compressor_debug) {
                            DebugConsole::insert_info('phpCompressor::load()', '"'.$filename.'" => Size: '.$this->FileSizeConvert(filesize($dir.$filename)).' '
                                . 'refresh to "'.$fileInfo['file'].'" => Loaded Size: '.$this->FileSizeConvert(filesize($dir.$fileInfo['file'])));
                        }
                        $code = $this->decompress($fileInfo['file']);
                        if(empty($code) || !$code) return false;
                        return $code;
                    }
                }
             } else {
                if(!$this->compress($dir,$filename)) return false;
                if(show_compressor_debug) {
                    DebugConsole::insert_info('phpCompressor::load()', '"'.$filename.'" => Size: '.$this->FileSizeConvert(filesize($dir.$filename)).' '
                        . 'zipped to "'.$GZFileName.'" => Loaded Size: '.$this->FileSizeConvert(filesize($dir.$GZFileName)));
                }
                $code = $this->decompress($GZFileName);
                if(empty($code) || !$code) return false;
                return $code;
             }
         }
         
         return false;
    }
    
    private function compress($dir='',$filename='') {
        if(file_exists($dir.$filename)) {
            //===============================
            //Config
            //===============================
            $filename_0 = substr($filename, 0, -4);
            $GZFileName = $filename_0.$this->compressedFile;
            $GZControlFileName = $filename_0.$this->controlFile;
            
            //===============================
            //Build FilePACK
            //===============================
            $fileStream = file_get_contents($dir.$filename);
            if (substr($fileStream, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
                $fileStream = substr($fileStream, 3);
            } $fileStream = trim($fileStream);
            $tokens = token_get_all($fileStream);
            $fileStream = "";
            foreach ($tokens as $token) {
                if (is_string($token)) {
                    $fileStream .= $token;
                } else {
                    list($id, $text) = $token;
                    switch ($id) { 
                        case T_COMMENT: 
                        case T_ML_COMMENT:
                        case T_DOC_COMMENT: break;
                        default:$fileStream .= $text;break;
                    }
                }
            }
            
            $fileStream = str_replace(array("<?php","?>","\r\n","\n","\t"), "", $fileStream);
            $FileGZPack = gzdeflate(str_replace('<?php', null, $fileStream)); unset($fileStream);
            apc_store($GZFileName, $FileGZPack, 300); unset($FileGZPack);
            
            //===============================
            //Build ControlPACK
            //===============================
            if(apc_exists($GZFileName)) { //Check is FilePACK exists
                $fileStats = stat($dir.$filename);
                $filePackControl = serialize(array('mtime' => $fileStats['mtime'], 'file' => $GZFileName));
                apc_store($GZControlFileName, $filePackControl, 300);
                unset($filePackControl);
                if(apc_exists($GZControlFileName)) { //Check is ControlPACK exists
                    return true;
                }
            }
        }
        
        return false;
    }
    
    private function decompress($filename='') {
        if(apc_exists($filename)) {
            $FileGZPack = apc_fetch($filename);
            if (substr($FileGZPack, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
                $FileGZPack = substr($FileGZPack, 3);
            }
            
            //===============================
            //Load FilePACK and Execute
            //===============================
            if(empty($FileGZPack) || !$FileGZPack) return false;
            $fileStream = @gzinflate($FileGZPack); unset($FileGZPack);
            if(!$fileStream) return false;
            return $fileStream;
        }
    }
    
    private function FileSizeConvert($bytes) {
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );

        foreach($arBytes as $arItem) {
            if($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
}