var files_code = [];
var editor;

// constants for keyboard shoercuts
var _KEYS = {
	TAB	:9,

	D	:68, // duplicate line or selection
	N	:78, // create new file
	S	:83, // save file
	W	:87, // close window (close file)

	None:0
};



// queue for storing opened files sequence.
// Used on Ctrl-Tab event (jump to recent file)
var opened_files_sequence = {

	__opened_file_sequence: [],
	__max_items: 10,


	__fix_file_param: function (file) {
		return file.replace(/___/g, '/').replace(/\\/g, '/');
	},

	add: function(file) {
		file = this.__fix_file_param(file);

		this.__opened_file_sequence.push(file);
		
		while (this.__opened_file_sequence.length > this.__max_items) {
			this.__opened_file_sequence.shift();
		}
	},

	remove: function(file) {
		file = this.__fix_file_param(file);

		for ( var i in this.__opened_file_sequence ) {
			if (this.__opened_file_sequence[i] == file) {
				this.__opened_file_sequence.splice(i, 1);
				this.remove(file);
				break;
			}
		}

	},

	getLastFile: function() {
		return this.__opened_file_sequence.length >= 2 ?
				(this.__opened_file_sequence.length == 1 ? 
					this.__opened_file_sequence[0] : 
					this.__opened_file_sequence[this.__opened_file_sequence.length - 2]) :
				'';
	}


};

function removeByElement(arrayName,arrayElement)
{
	for(var i=0; i<arrayName.length;i++ ) {
		if(arrayName[i]==arrayElement) {
			arrayName.splice(i,1);
		}
	}
}

function file_is_in_right_panel(file) {
	var retVal = false;
	$('.right_panel li a').each(function(i, obj) {
		if ($(obj).text() == file.replace(/___/g, '/')) {
		
			retVal = true;
			return false;
		}
		
		return true;
	});
    
	return retVal;
}

function add_file_to_right_panel(file) {
	if ($('.right_panel ul').length == 0) {
		$('.right_panel').append($('<ul />'));
	}
	
	// (!) check the extension of the file

	$('.right_panel ul').append($('<li />').append(
		$('<a />')
		.attr('href', 'javascript:;')
		.text(file.replace(/___/g, '/'))
		.unbind('click').click(function(){
			switch_to_file($(this).text());
			$(this).addClass('selected');
		})
		)
	.css('position', 'relative')
		.append($('<span />').attr("class", "close_file_x").text("x"))
		);
	
	$('.close_file_x')
	.bind('mouseenter', function() {
		$(this).addClass('close_file_x_hover');
	})
	.bind('mouseleave', function() {
		$(this).removeClass('close_file_x_hover');
	})
	.click(function() {
		close_file($(this).parent().find('a').text());
	});
}

function switch_to_file(file) {
	// check which file is selected now and save the code
	if ($('.right_panel a.selected').length) {
		var arr_index = $('.right_panel a.selected').text();
		files_code[arr_index] = {
			"filename":arr_index,
			"content":editor.getValue(),
			"cursor":editor.getCursor(false)
		};
		$('.right_panel a.selected').removeClass('selected');
	}

	// check if the file was previously stored in the memory
	var rFile = file.replace(/___/g, '/');
	var rExtension = rFile.substr(rFile.lastIndexOf('.') + 1);
	
	if (typeof(files_code[rFile]) != 'undefined' && files_code[rFile] != null) {
		editor.setValue(files_code[rFile]["content"]);
		editor.setCursor(files_code[rFile]["cursor"]);
	} else {
		$('.bottom_toolbar .filename').text('Loading...');
		$.ajax({
			url: _HTTP_ROOT + "/ajax/get_file_content.php",
			data: {
				filename: rFile
			},
			type: "get",
			success: function(res) {
				editor.setValue(res);
				files_code[rFile] = {
					"filename":rFile,
					"content":res,
					"cursor":editor.getCursor()
				};
			}
		})
	}

	opened_files_sequence.add(rFile);

	$('.bottom_toolbar .filename').text(rFile);
	editor.focus();
	
	// mark the opened file
	$('.right_panel a').each(function(i, obj) {
		if ($(obj).text() == rFile) {
			$(obj).addClass('selected');
			return false;
		}
		return true;
	});
	
	return false;
}

function close_file(file) {

	// check if there is no parameter defined, than it's the current opened file
	if (!file) {
		if ($('.right_panel li a.selected').length > 0) {
			file = $('.right_panel li a.selected').text();
		}
	}

	if (typeof(file) == 'undefined' || file.length == 0) {
		return;
	}
	var rFile = file.replace(/___/g, '/').replace(/\\/g, '/');
	$('.right_panel a').each(function(i,obj) {
		if ($(obj).text() == rFile) {

			// remove the file from the queue
			opened_files_sequence.remove(rFile);

			// close this file
			if ($(obj).parents('li').find('a').hasClass('selected')) {
				moveToLastFile();
			}
			
			// remove the element / file
			$(obj).parents('li').remove();
			files_code[rFile] = null;

		}
	});
}


// move to last opened file (for Ctrl-Tab key event)
function moveToLastFile() {
	var last_opened_file = opened_files_sequence.getLastFile();
	if (last_opened_file.length > 0) {
		open_file(last_opened_file);
	} else {
		$('.bottom_toolbar .filename').text('');
		editor.setValue('');
	}

}


function save_file() {

	$('.bottom_toolbar .notification').text('Saving...');

	// get the filename
	var fName = $('.right_panel a.selected').text();
	// get the content
	var content = editor.getValue();
	
	$.post(_HTTP_ROOT + '/ajax/set_file_content.php', {
		"filename":fName,
		"content": content
	}, function(res) {
		if (res.length > 0) {
            // display an error (if res is not empty)
			alert(res);
			$('.bottom_toolbar .notification').text('');
		} else {
			$('.bottom_toolbar .notification').text('Saved!');
			setTimeout(function() {
				$('.bottom_toolbar .notification').text('');
			}, 2000);
		}
	});
}

function open_file(file) {
	// check that the file is textual
	var allow_extension = ['php', 'html', 'htm', 'css', 'js', 'ini', 'phtml',
	'py', 'inc', 'txt', 'htaccess', 'htpassword', 'sql', 'xml', 'conf'];

	if (typeof file != 'string') file = file.toString();
	if (!file.match(new RegExp('\\.(' + allow_extension.join('|') + ')$'), 'g')) {
		alert(file + ' is not a common textual file type');
	} else {

		// check if the file is in the right panel
		if (!file_is_in_right_panel(file)) {
			// add the file to the right panel
			add_file_to_right_panel(file);
		}
		// switch to the file
		switch_to_file(file);
	}
}


function get_opened_file() {
	if ($('.bottom_toolbar .filename').text() != '') {
		return $('.bottom_toolbar .filename').text();
	}

	return '';
}

function get_current_directory() {
	var opened_file = get_opened_file();
	var dir = window.last_opening_location;
	if (opened_file != '') {
		dir = opened_file.substr(0, opened_file.lastIndexOf('/'));
	}

	if (dir.trim() == '') {
		dir = '/';
	}
	
	return dir;
}

function open_modal_window(action) {
    

	switch(action) {
		case 'create_directory':
			$('.modal_window input[name=new_directory_dirname]').val(get_current_directory())
			break;
		case 'rename_directory':
			$('.modal_window input[name=old_directory_name]').val(get_current_directory())
			break;
		case 'create_file':
			$('.modal_window input[name=new_file_dirname]').val(get_current_directory())
			break;
		case 'upload_file':
			$('.modal_window input[name=upload_file_name]').val('');
			$('.modal_window input[name=upload_file_dirname]').val(get_current_directory())
			break;
		case 'download_directory':
			$('.modal_window input[name=download_dirname]').val(get_current_directory());
			break;
				dafault:
				break;
	}

	$('.modal_window > div').hide();
	$('.modal_window div.'+action).show();
	$('.modal_window').fadeIn();
}

function close_modal_window() {
	$('.modal_window').fadeOut();
}

function create_directory() {
	var newDirName = $('.modal_window > div.create_directory input[name=new_directory_name]').val();
	var dirName = $('.modal_window > div.create_directory input[name=new_directory_dirname]').val();


	if (newDirName != "" && dirName != "") {
		$.post(_HTTP_ROOT + '/ajax/create_directory.php', {
			dirName: dirName,
			newDirName: newDirName
		}, function(res) {
			if (res != '') {
				alert(res);
			} else {
				close_modal_window();
			}
		});
	}
}

function create_file() {
	var newFileName = $('.modal_window > div.create_file input[name=new_file_name]').val();
	var dirName = $('.modal_window > div.create_file input[name=new_file_dirname]').val();

	if (newFileName != "" && dirName != "") {
		$.post(_HTTP_ROOT + '/ajax/create_file.php', {
			dirName: dirName,
			newFileName: newFileName
		}, function(res) {
			if (res != '') {
				alert(res);
			} else {
				close_modal_window();
			}
		});
	}
}

function upload_file() {
	// create an iframe if needed
	if ($('#upload_zip_iframe').length == 0) {
		$('.ide').append(
			$('<iframe />').css({
				display:"none",
				width:"1px",
				height:"1px"
			})
			.attr('name','submit_file_iframe')
			.attr('id','submit_file_iframe')
			);
	}

	// check the form
	if ($('#upload_zip_form input[name=upload_file_name]').val() == '' ||
		$('#upload_zip_form input[name=upload_file_dirname]').val() == '') {

		return '';
	}
    
	// submit the form to the iframe
	$('#upload_zip_form')
	.attr('action', _HTTP_ROOT + '/ajax/upload_file.php')
	.attr('method', 'POST')
	.attr('enctype', 'multipart/form-data')
	.attr('target', 'submit_file_iframe')
	.submit();

	close_modal_window();
	return '';
}

function download_directory() {
	var directory = $('.modal_window input[name=download_dirname]').val();
	directory = directory.replace('/', '___');
	directory = directory.replace('\\', '___');
	window.open(_HTTP_ROOT + '/ajax/download_directory.php?download_directory='+encodeURIComponent(directory),
		'download_directory', 'toolbar=0, menubar=0, location=0, scrollbar=0, width=50, height=50');
	close_modal_window();
}

function rename_directory() {
    var new_name = $('.modal_window .rename_directory input[name=new_directory_name]').val();
    var old_name = $('.modal_window input[name=old_directory_name]').val();
    $.post(_HTTP_ROOT + '/ajax/rename_directory.php', {old_name:old_name, new_name:new_name}, function(res) {
        if (res.length > 0) {
            alert(res);
        }
        
    });
    close_modal_window()
}


function open_sub_menu() {
	$('.toolbar .toolbar_left_panel .sub_menu').slideDown();
	$('.toolbar .toolbar_left_panel h3 a span').text('-');
}

function close_sub_menu() {
	$('.toolbar .toolbar_left_panel .sub_menu').slideUp();
	$('.toolbar .toolbar_left_panel h3 a span').text('+');
}

function adjust_ide_size() {
	// adjust the height of the editor
	var new_height = $(window).height()-100;
	var new_width = $(window).width()-80;
	$('.CodeMirror').css('height', new_height+'px');
	$('.left_panel, .main_panel, .right_panel').css('height', new_height+'px');
    
    // return the left panel to its original size (width)
    $('.left_panel').css('width', '180px');
    $('.resize_arrow_right').css('left', '191px');
    
    // resize the main panel with the whole window
	$('.main_inner').css('width', new_width + 'px');
	$('.main_panel').css('width', (new_width - 400) + 'px');
	$('.toolbar_main_panel').css('width', (new_width - 400) + 'px');
}

function duplicate_line_or_selection() {
	var editor_selection = editor.getSelection();
	if (editor_selection.length > 0) {
		// duplicate selection
		editor.replaceSelection(editor_selection + editor_selection);
	} else {
		// duplicate line
		var pos = editor.getCursor();
		editor.replaceRange(editor.getLine(pos.line) + "\r\n", {
			line:pos.line,
			ch:0
		}, {
			line:pos.line,
			ch:0
		});
	}
}

$(function(){
	$(".left_panel").jstree({
		"plugins" : [ "themes", "types", "json_data"],
		"json_data" : {
			"ajax" : {
				"url" : _HTTP_ROOT+"/ajax/get_files_list.php",
				"data" : function (n) {
					window.last_opening_location = n.attr ? n.attr("id").replace(/___/g,"/").replace(/\/\//g, '/') : '';
					return {
						"operation" : "get_children",
						"id" : n.attr ? n.attr("id").replace("node_","") : 0
					};
				}
			}
		}
	});

	editor = CodeMirror.fromTextArea(document.getElementById('code'), {
		lineNumbers: true,
		matchBrackets: true,
		mode: "application/x-httpd-php",
		indentUnit: 4,
		indentWithTabs: true,
		tabMode: "classic",
		onKeyEvent: function(e, f) {
                
			// check the Ctrl-Tab
			if (f.ctrlKey && f.keyCode == _KEYS.TAB && f.type == 'keypress') {
				moveToLastFile();
				f.stop();
			}

			// check the "save" key function
			if (f.ctrlKey && f.keyCode == _KEYS.S) {
				save_file();
				f.stop();
			}


			// Ctrl-D event - duplicate
			if (f.ctrlKey && f.type == 'keydown' && f.keyCode == _KEYS.D) {
				duplicate_line_or_selection();
				f.stop();
			}

			// Ctrl-W event - close file
			if (f.ctrlKey && f.type == 'keydown' && f.keyCode == _KEYS.W) {
				close_file();
				f.stop();
			}

			// Ctrl-N event - create file
			if (f.ctrlKey && f.type == 'keydown' && f.keyCode == _KEYS.N) {
				open_modal_window('create_file');
				f.stop();
			}

			
		}
	});

	// adjust editor size
	adjust_ide_size();


	// resize the editor window
	window.resize_action					= false;
	window.resize_origX						= -1;
	window.resize_orig_left_panel_width		= -1;
	window.resize_orig_right_panel_width	= -1;
	window.resize_orig_main_panel_width		= -1;
	window.resize_arrow_left				= -1;
	window.resize_arrow_right   			= -1;
    ///////////////// ARROW_RIGHT (for the left panel) //////////////
	$('.resize_arrow_right')
	.bind('mousedown', function(e) {
		window.resize_action = true;
		window.resize_origX = e.clientX;
		window.resize_orig_left_panel_width = parseInt($('.left_panel').css('width'));
		window.resize_orig_main_panel_width = parseInt($('.main_panel').css('width'));
		window.resize_arrow_left = parseInt($('.resize_arrow_right').css('left'));
		$('.ide').bind('mousemove', function(e) {
            var left_panel_width = window.resize_orig_left_panel_width + (e.clientX - window.resize_origX);
            if (left_panel_width < 100 || left_panel_width > 400) {
                return;
            }
			$('.left_panel').css('width', (window.resize_orig_left_panel_width + (e.clientX - window.resize_origX)) + 'px');
			$('.main_panel').css('width', (window.resize_orig_main_panel_width - (e.clientX - window.resize_origX)) + 'px');
			$('.toolbar_left_panel').css('width', (window.resize_orig_left_panel_width + (e.clientX - window.resize_origX)) + 'px');
			$('.toolbar_main_panel').css('width', (window.resize_orig_main_panel_width - (e.clientX - window.resize_origX)) + 'px');
			$('.resize_arrow_right').css('left', (window.resize_arrow_left + (e.clientX - window.resize_origX)) + 'px');
		});
	})
	.bind('mouseup', function(e) {
		$('.ide').unbind('mousemove');
		window.resize_action = false;
	});
    ///////////////// ARROW_LEFT (for the right panel) //////////////
	$('.resize_arrow_left')
	.bind('mousedown', function(e) {
		window.resize_action = true;
		window.resize_origX = e.clientX;
		window.resize_orig_right_panel_width = parseInt($('.right_panel').css('width'));
		window.resize_orig_main_panel_width = parseInt($('.main_panel').css('width'));
		window.resize_arrow_right = parseInt($('.resize_arrow_left').css('right'));
		$('.ide').bind('mousemove', function(e) {
            var right_panel_width = window.resize_orig_right_panel_width + (e.clientX - window.resize_origX);
            if (right_panel_width < 100 || right_panel_width > 400) {
                return;
            }
			$('.right_panel').css('width', (window.resize_orig_right_panel_width - (e.clientX - window.resize_origX)) + 'px');
			$('.main_panel') .css('width', (window.resize_orig_main_panel_width + (e.clientX - window.resize_origX)) + 'px');
			$('.toolbar_main_panel').css('width', (window.resize_orig_main_panel_width + (e.clientX - window.resize_origX)) + 'px');
			$('.resize_arrow_left').css('right', (window.resize_arrow_right - (e.clientX - window.resize_origX)) + 'px');
		});
	})
	.bind('mouseup', function(e) {
		$('.ide').unbind('mousemove');
		window.resize_action = false;
	});


	// bind sub-menu events (sub menu from "files" menu
	$('.toolbar .toolbar_left_panel h3 a').click(function() {
		if ($('.toolbar .toolbar_left_panel .sub_menu').css('display').toLowerCase() == 'none') {
			open_sub_menu();
		} else {
			close_sub_menu();
		}
	});
	$('.toolbar .toolbar_left_panel .sub_menu a').click(function() {
		close_sub_menu();
	});
    
    
	// main menu events (links to other pages (not the IDE))
    var animate_main_menu_top = "-22px";
	$('.top_main_menu_wrapper')
	.bind('mouseenter', function() {
		$(this).stop(true, true).animate({
			top: "0"
		});
	})
	.bind('mouseleave', function() {
		$(this).animate({
			top: animate_main_menu_top
		});
	});

    setTimeout(function() {
        $('.top_main_menu_wrapper').animate({top:animate_main_menu_top});
    }, 1500);


	// bind event on window resize
	$(window).bind('resize', function(e) {
		adjust_ide_size();
	});
});