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
        die();
    }
}

if (array_key_exists('filename', $_GET) && !empty($_GET['filename'])) {

    $filename = str_replace('___', DIRECTORY_SEPARATOR, $_GET['filename']);
    $filename = str_replace('\\', '/', $filename);

    // get the directory name from the filename
    $dir = dirname($_GET['filename']);
    $dir = trim($dir, '/');
    if (!$ftp->cd('/'.$dir)) {
        die();
    }
    
    // download the file to a local temporary file
    $temp_dir = ini_get("session.save_path");
    $loc_file = tempnam($temp_dir, 'online_php_');
    if (file_exists($loc_file)) {
        if (!$ftp->get($loc_file, basename($filename))) {
            die();
        } else {
            readfile($loc_file);
            die();
        }
    }

}