<?php

/**
 * Template created by Gregory Chris
 */



if ($GLOBALS['url_params'][0] != 'transaction' ) {
    load_view('header');
}

// check login
if ($GLOBALS['url_params'][0] == 'transaction') {
    if (file_exists(dirname(__FILE__) . '/admin/transaction.php')) {
        require_once dirname(__FILE__).'/admin/'.array_shift($GLOBALS['url_params']).'.php';
    }
} elseif (!array_key_exists('user', $_SESSION) || empty($_SESSION['user']) || !($_SESSION['user']['id'] > 0)) {
    // load the login screen
    load_view('admin/login');
} else {
    
    load_view('admin/header');
    
    if (!empty($GLOBALS['url_params'][0])) {
        
        if (file_exists(dirname(__FILE__) . '/admin/'.$GLOBALS['url_params'][0].'.php')) {
            // load the relevant file
            require_once dirname(__FILE__) . '/admin/'.array_shift($GLOBALS['url_params']).'.php';
        } else {
            // 404 error!
            require_once dirname(__FILE__) . '/admin/error.php';
        }
        
    } else {
        // load the homepage of the admin panel
        require_once dirname(__FILE__) . '/admin/homepage.php';
    }
    
    load_view('admin/footer');
    
}

load_view('footer');
