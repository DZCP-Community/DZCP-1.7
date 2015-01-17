/*
 * CKEditor - WYSIWYG Options
 */
var config_ckeditor_mini = {
	toolbar:
	[
		['Source','-','Save','NewPage','Preview','-','Templates'],
		['Maximize', 'ShowBlocks','-','About']
	],
		
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
		$(".editorStyleWord").ckeditor(config_ckeditor_mini);
	}
};