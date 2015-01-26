/*
 * CKEditor - WYSIWYG Options
 */
var config_ckeditor_bbcode_only = {
	toolbar: [
            ['Undo','Redo','-','RemoveFormat'],
            ['Bold','Italic','Underline','Strike'],
            ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Link','Unlink','Image','-','TextColor'],['Maximize','SpellChecker', 'Scayt'],
            ['Source']
	],
	
	//{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	extraPlugins: 'bbcode',
	language: dzcp_config.lng,
	coreStyles_bold: { element : 'b', overrides : 'strong' }
};

var config_ckeditor_standard = {
	toolbar: [
            ['Cut','Paste','PasteText','PasteFromWord','-', 'SpellChecker', 'Scayt'],
            ['Undo','Redo','-','SelectAll','RemoveFormat'],
            ['Bold','Italic','Underline','Strike'],
            ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Link','Unlink','Image','HorizontalRule','Anchor'],
            ['Styles','Format','Font','FontSize'],
            ['TextColor','BGColor'],
            ['Maximize', '-','Source']
	],
	
	//{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	
	language: dzcp_config.lng,
	coreStyles_bold: { element : 'b', overrides : 'strong' }
};

var config_ckeditor_mini = {
	toolbar: [
		['Cut','Paste','-', 'SpellChecker', 'Scayt'],
		['Undo','Redo'],
		['Bold','Italic','Underline','Strike'],
		['NumberedList','BulletedList'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Image']
	],
        
	//{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	
	language: dzcp_config.lng,
	coreStyles_bold: { element : 'b', overrides : 'strong' }
};

/*
 * Template JS Code
 */
var TPL = {
    init: function() {
	    //Initialisiere Menu Tabs
	    $(".tabs").tabs("> .switchs");
	    $(".tabs2").tabs(".switchs2 > div", { effect: 'fade', rotate: true });
	},
	
	load: function() {
	    //Initialisiere CKEditor
	    TPL.CKEditor();
	},
	
	//CKEditor - WYSIWYG 
	CKEditor: function() {
            if(dzcp_config.onlyBBCode) {
                $(".editorStyleWord").ckeditor(config_ckeditor_bbcode_only);
                $(".editorStyle").ckeditor(config_ckeditor_bbcode_only);
            } else {
                $(".editorStyleWord").ckeditor(config_ckeditor_standard);
                $(".editorStyle").ckeditor(config_ckeditor_standard);
            }
	}
};