/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */
var tinyMCE_GZ = {
    settings : { 
        themes : '',
        plugins : '',
        languages : '',
        disk_cache : true,
        call_file : 'tiny_mce_gzip.php',
        debug : false,
        compress : true,
        core : true,
        suffix : '' 
    },

    init : function(setOptions) {
        var option, source, script = document.getElementsByTagName('script');
        if(!DZCP.jQueryCheck(false)) return false;
        tinyMCE_GZ.DebugLogger('Load TinyMCE-Engine 1.0');
        for (option in setOptions) {
            this.settings[option] = setOptions[option];
        }
        
        if(window.tinyMCEPreInit) {
            this.baseURL = tinyMCEPreInit.base;
        } else {
            for (i=0; i<script.length; i++) {
                source = script[i];
                if (source.src && source.src.indexOf('tiny_mce') != -1) {
                    this.baseURL = source.src.substring(0, source.src.lastIndexOf('/'));
                }
            }
        }

        if(!this.coreLoaded) { this.loadScripts(); }
    },
    
    loadScripts : function() {
        if(!DZCP.jQueryCheck(false)) return false;
        tinyMCE_GZ.DebugLogger('Tinymce DiskCache: '+DZCP.BooleanToString(this.settings.disk_cache));
        tinyMCE_GZ.DebugLogger('Tinymce Core: '+DZCP.BooleanToString(this.settings.core));
        tinyMCE_GZ.DebugLogger('Tinymce Suffix: '+escape(this.settings.suffix));
        tinyMCE_GZ.DebugLogger('Tinymce Themes: '+escape(this.settings.themes));
        tinyMCE_GZ.DebugLogger('Tinymce Plugins: '+escape(this.settings.plugins));
        tinyMCE_GZ.DebugLogger('Tinymce Languages: '+escape(this.settings.languages));
        tinyMCE_GZ.DebugLogger('Tinymce Compress: '+DZCP.BooleanToString(this.settings.compress));

        $.get("../inc/tinymce/" + this.settings.call_file,{ 
            js: "true",
            disk_cache: DZCP.BooleanToString(this.settings.disk_cache),
            core: DZCP.BooleanToString(this.settings.core),
            suffix: escape(this.settings.suffix),
            themes: escape(this.settings.themes),
            plugins: escape(this.settings.plugins),
            languages: escape(this.settings.languages),
            compress: DZCP.BooleanToString(this.settings.compress)
        }, function(data) { tinyMCE_GZ.eval(data); });
    },
    
    eval : function(code) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.text = code;
        (document.getElementsByTagName('head')[0] || document.documentElement).appendChild(script);
        script.parentNode.removeChild(script);
        tinyMCE_GZ.CallTinyMCE();
    },

    CallTinyMCE: function() {
        tinyMCE_GZ.DebugLogger('Initiation Tinymce');
        //default wysiwyg editor
        tinyMCE.init({
            theme                               : "advanced",
            mode                                : "specific_textareas",
            editor_selector                     : 'editorStyle',
            plugins                             : 'contextmenu,dzcp,inlinepopups,spellchecker,media',
            language                            : (dzcp_config.lng == 'de' ? dzcp_config.lng : 'en'),
            theme_advanced_buttons1: 'bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,|,image,youtube,forecolor,'
            + 'backcolor,blockquote,|,smileys,flags,dzcpuser,|,spellchecker',
            theme_advanced_toolbar_location     : 'top',
            theme_advanced_toolbar_align        : 'center',
            theme_advanced_statusbar_location   : 'bottom',
            spellchecker_languages              : 'English=en,+Deutsch=de',
            theme_advanced_resizing             : true,
            theme_advanced_resize_horizontal    : false,
            theme_advanced_resizing_use_cookie  : false,
            accessibility_warnings              : false,
            entity_encoding                     : 'raw',
            verify_html                         : false,
            forced_root_block                   : '',
            button_tile_map                     : true
        });

        //mini wysiwyg editor
        tinyMCE.init({
            mode                                : 'specific_textareas',
            editor_selector                     : 'editorStyleMini',
            theme                               : 'advanced',
            plugins                             : 'contextmenu,dzcp,inlinepopups,media',
            language                            : (dzcp_config.lng == 'de' ? dzcp_config.lng : 'en'),
            theme_advanced_buttons1             : 'bold,italic,underline,|,link,unlink,|,image',
            theme_advanced_buttons2             : '',
            theme_advanced_buttons3             : '',
            theme_advanced_toolbar_location     : 'top',
            theme_advanced_toolbar_align        : 'center',
            theme_advanced_resizing             : true,
            theme_advanced_resize_horizontal    : false,
            theme_advanced_resizing_use_cookie  : false,
            accessibility_warnings              : false,
            entity_encoding                     : 'raw',
            verify_html                         : false,
            forced_root_block                   : '',
            button_tile_map                     : true
        });

        //newsletter wysiwyg editor
        tinyMCE.init({
            mode                                : 'specific_textareas',
            editor_selector                     : 'editorStyleNewsletter',
            theme                               : 'advanced',
            plugins                             : 'contextmenu,dzcp,media',
            language                            : (dzcp_config.lng == 'de' ? dzcp_config.lng : 'en'),
            theme_advanced_buttons1             : 'bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,|,image,forecolor,backcolor,blockquote',
            theme_advanced_buttons2             : '',
            theme_advanced_buttons3             : '',
            theme_advanced_toolbar_location     : 'top',
            theme_advanced_toolbar_align        : 'center',
            theme_advanced_statusbar_location   : 'bottom',
            theme_advanced_resizing             : true,
            theme_advanced_resize_horizontal    : false,
            theme_advanced_resizing_use_cookie  : false,
            accessibility_warnings              : false,
            entity_encoding                     : 'raw',
            verify_html                         : false,
            button_tile_map                     : true,
            forced_root_block                   : '',
            convert_urls                        : false
        });

        //full wysiwyg editor
        tinyMCE.init({
            mode                              : 'specific_textareas',
            editor_selector                   : 'editorStyleWord',
            theme                             : 'advanced',
            elements                          : "ajaxfilemanager",
            plugins                           : 'contextmenu,dzcp,advimage,paste,table,fullscreen,inlinepopups,spellchecker,searchreplace,insertdatetime,media',
            language                          : (dzcp_config.lng == 'de' ? dzcp_config.lng : 'en'),
            theme_advanced_buttons1           : 'bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,undo,redo,|,bullist,numlist,|,link,unlink,|,pastephp,|,forecolor,'
                                              + 'backcolor,blockquote,|,smileys,flags,',
            theme_advanced_buttons2           : 'paste,pastetext,pasteword,|,search,replace,|,image,|,tablecontrols,|,dzcpuser,|,media',
            theme_advanced_buttons3           : 'fontselect,fontsizeselect,|,insertdate,inserttime,|,sub,sup,|,outdent,indent,|,fullscreen,clip,spellchecker,code,youtube',
            extended_valid_elements           : 'img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],'
                                              + 'hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]',
            theme_advanced_toolbar_location   : 'top',
            spellchecker_languages            : 'English=en,+Deutsch=de',
            theme_advanced_toolbar_align      : 'center',
            theme_advanced_statusbar_location : 'bottom',
            theme_advanced_resizing           : true,
            theme_advanced_resize_horizontal  : false,
            accessibility_warnings            : false,
            button_tile_map                   : true,
            entity_encoding                   : 'raw',
            verify_html                       : false,
            forced_root_block                 : '',
            file_browser_callback             : 'ajaxfilemanager' 
        });

        //filebrowser callback
        function ajaxfilemanager(field_name, url, type, win) {
            var view = 'detail';
            switch (type) {
                case "image":
                view = 'thumbnail';
                    break;
                case "media":
                    break;
                case "flash":
                    break;
                case "file":
                    break;
                default:
                    return false;
            }

            tinyMCE.activeEditor.windowManager.open({
                url: "../inc/tinymce/plugins/ajaxfilemanager/ajaxfilemanager.php?view=" + view,
                width: 850,
                height: 478,
                inline : "yes",
                close_previous : "no"
            },{
                window : win,
                input : field_name
            });
        }
    },
    
    DebugLogger: function(message) {
        if(dzcp_config.debug) {
            console.info("DZCP Debug: " + message);
        }
    }
};

$(document).ready(function() { 
    tinyMCE_GZ.init({ 
        plugins : 'contextmenu,dzcp,advimage,paste,table,fullscreen,inlinepopups,spellchecker,searchreplace,insertdatetime,media', 
        themes : 'advanced', 
        languages : (dzcp_config.lng == 'de' ? dzcp_config.lng : 'en'), 
        disk_cache : true, 
        debug : false, 
        compress : true });
});