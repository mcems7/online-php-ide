/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.toolbar = [
	//['Source','-','Save','NewPage','Preview','-','Templates'],
	['Bold','Italic','Underline'/*,'Strike','-','Subscript','Superscript'*/],
	['Cut','Copy','Paste','PasteText','PasteFromWord'/*,'-','Print', 'SpellChecker', 'Scayt'*/],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Format'/*, 'Styles','Font','FontSize'*/],
	['BidiLtr', 'BidiRtl' ],
	//['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
	//'/',
	['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'/*,'CreateDiv'*/],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	['Link','Unlink','Anchor'],
	//['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
	//'/',
	//['TextColor','BGColor'],
	//['Maximize', 'ShowBlocks','-','About']        
    ];
};
