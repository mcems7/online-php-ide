<?php


function get_files_tree($base_dir = NULL) {
	if (is_null($base_dir)) $base_dir = $_SERVER['DOCUMENT_ROOT'];
	$dirs = 0;
}


function _log($txt) {
    $f = fopen(dirname(__FILE__).'/../log/log.txt', 'a+');
    fputs($f, date('(d.m.y H:i)')." (".$_SESSION['ftp']['hostname'].")\t\t".$txt."\r\n");
    fclose($f);
}


///////// SIMPLE MVC FUNCTIONS //////////

function url_is_exception() {
	global $controller;

    // version check
    if ($controller == 'version') {
        return true;
    }
    
    // contact us submit
    if ($controller == 'about' && $GLOBALS['url_params'][0] == 'submit') {
        return true;
    }
    
    // snippets AJAX loading
	if ($controller == 'snippets' && $GLOBALS['url_params'][0] == 'load' && !empty($GLOBALS['url_params'][1])
		&& is_numeric($GLOBALS['url_params'][1]) && $GLOBALS['url_params'][1] > 0) {
		return true;
	}

    
    // source code download
	if ($controller == 'source' && $GLOBALS['url_params'][0] == 'download') {
		return true;
	}

    // admin transactions
	if ($controller == 'admin' && $GLOBALS['url_params'][0] == 'transaction') {
		return true;
	}

	return false;
}

function load_model($model_name) {
    if (file_exists(dirname(__FILE__).'/../application/model/'.$model_name.'.php')) {
        require_once(dirname(__FILE__).'/../application/model/'.$model_name.'.php');
    }
}

function load_view($view_name, $params = array()) {
    if (file_exists(dirname(__FILE__).'/../application/view/'.$view_name.'.phtml')) {
        extract($params);
        require_once(dirname(__FILE__).'/../application/view/'.$view_name.'.phtml');
    }
}
?>