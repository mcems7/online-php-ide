<?php

/**
 * Template created by Gregory Chris
 */
require_once dirname(__FILE__).'/../config/config.php';

if (!(
        array_key_exists('ftp', $_SESSION) && array_key_exists('username', $_SESSION['ftp']) &&
        !empty($_SESSION['ftp']['username'])
        )) {
    die();
} else {
    $ftp = @new ClsFTP($_SESSION['ftp']['username'], $_SESSION['ftp']['password'], $_SESSION['ftp']['hostname']);
    if (!$ftp->is_login) {
        die('Is not logged in, session error');
    }
}

$params_ok = array_key_exists('new_name', $_POST) &&
        array_key_exists('old_name', $_POST) &&
        !empty($_POST['new_name']) &&
        !empty($_POST['old_name']);

if ($params_ok) {

    $one_dir_up = preg_replace('%/([^/]+?)/?$%i', '', $_POST['old_name']);
    if (empty($one_dir_up)) $one_dir_up = '/';
    
	// change directory
	if (!$ftp->cd($one_dir_up)) {
		die('cannot change directory to '.$_POST['old_name']);
	}
        
    // get pure names from the 
    $pure_old_name = array_pop(explode('/', rtrim($_POST['old_name'], '/')));
    
	// create the file
	if (!$ftp->rename($pure_old_name, $_POST['new_name'])) {
		die('cannot rename directory '.$pure_old_name.' in dir ' . $one_dir_up);
	} else {
		die();
	}
}

die('Error in parameters');
