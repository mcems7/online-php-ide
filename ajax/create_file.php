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

$params_ok = array_key_exists('dirName', $_POST) &&
        array_key_exists('newFileName', $_POST) &&
        !empty($_POST['dirName']) &&
        !empty($_POST['newFileName']);

if ($params_ok) {

	// change directory
	if (!$ftp->cd($_POST['dirName'])) {
		die('cannot change directory to '.$_POST['dirName']);
	}

	// create the file
	if (!$ftp->put_string($_POST['newFileName'], '<?php')) {
		die('cannot create file '.$_POST['newFileName']);
	} else {
		die();
	}
}

if (empty($_POST['dirName'])) {
    die('empty directory name');
}

if (empty($_POST['newFileName'])) {
    die('empty new file name');
}

if (file_exists($_POST['dirName'])) {
    die ('directory ' . $_POST['dirName'] . ' does not exist');
}

die('Error in parameters');