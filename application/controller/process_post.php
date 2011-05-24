<?php

/**
 *
 * template created by Gregory Chris
 * Date: 05/05/2011
 *
 */
if (
    array_key_exists('ftp_username', $_POST) && !empty($_POST['ftp_username']) &&
    array_key_exists('ftp_password', $_POST) && !empty($_POST['ftp_password']) &&
    array_key_exists('ftp_hostname', $_POST) && !empty($_POST['ftp_hostname'])
) {
    
    // check the ftp connection
    $ftp = @new ClsFTP($_POST['ftp_username'], $_POST['ftp_password'], $_POST['ftp_hostname']);
    if (!$ftp->is_login) {
        
        unset ($_SESSION['ftp']);
        $GLOBALS['login_error'] = 'Login Error';
        
    } else {
    
        session_regenerate_id();
        $_SESSION['ftp'] = array(
            'username' => $_POST['ftp_username'],
            'password' => $_POST['ftp_password'],
            'hostname' => $_POST['ftp_hostname'],
        );
        
        $GLOBALS['login_error'] = '';
        
    }
    
    header('Location: '.$_SERVER['HTTP_REFERER']);
    die();
}