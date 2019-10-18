<?php
// base directory path
define ('_DIR_ROOT', dirname(__FILE__));
// include the configuration file
require_once dirname(__FILE__).'/config/config.php'; 


$req_uri = preg_replace('%^'.preg_quote(_HTTP_ROOT, '%').'%i', '', $_SERVER['REQUEST_URI']);
$url_arr        = parse_url($req_uri);
$GLOBALS['url_params'] = $url_path_arr   = explode('/', trim($url_arr['path'], '/'));
$controller     = array_shift($GLOBALS['url_params']);


// manage exceptions (where we don't need the header)
if (!url_is_exception()) {
	require_once dirname(__FILE__).'/html/header.phtml';
}

// normal pages
if (file_exists(dirname(__FILE__).'/application/controller/'.$controller.'.php')) {
    require_once dirname(__FILE__).'/application/controller/'.$controller.'.php';
} else {
    if (!$_SESSION['ftp'] || empty($_SESSION['ftp'])) {
        require_once dirname(__FILE__).'/application/controller/login_screen.php';
    } elseif (empty($controller)) {
        ?><div class='main'><div class='main_inner'><?php
        require dirname(__FILE__).'/application/controller/ide.php';
        ?></div></div><?php
    } else {
        require_once dirname(__FILE__).'/application/controller/404.php';
    }
}

require_once dirname(__FILE__).'/html/footer.phtml'; 

