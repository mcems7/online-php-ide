<?php
/**
 * Template created by Gregory Chris
 */
require_once dirname(__FILE__).'/../config/config.php';

if (!(
        array_key_exists('ftp', $_SESSION) && array_key_exists('username', $_SESSION['ftp']) &&
        !empty($_SESSION['ftp']['username'])
        )) {
    die('Not Logged in - Session ended');
} else {
    $ftp = @new ClsFTP($_SESSION['ftp']['username'], $_SESSION['ftp']['password'], $_SESSION['ftp']['hostname']);
    if (!$ftp->is_login) {
        die('Cannot login to FTP');
    }
}


if (array_key_exists('filename',$_POST) && !empty($_POST['filename']) &&
    array_key_exists('content', $_POST) && !empty($_POST['content'])) {
    $filename = str_replace('___', DIRECTORY_SEPARATOR, $_POST['filename']);
    $filename = str_replace('\\', '/', $filename);

	if (!$ftp->cd('/'.trim(dirname($filename), '/'))) {
		die('cannot change dir to '.'/'.trim(dirname($filename), '/'));
	}

	if (!$ftp->put_string(basename($filename), $_POST['content'])) {
		die('cannot put content into '.basename($filename));
	} else {
		$ftp->close();
		die('');
	}
}

die('Error occured');

