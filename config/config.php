<?

session_start();

ini_set('display_errors', '0');
//error_reporting(E_WARNING | E_ERROR);

date_default_timezone_set('Asia/Jerusalem');

require dirname(__FILE__) .'/../application/model/ftp.class.php';
require dirname(__FILE__) .'/funcs.php';

if (!defined('_DIR_ROOT')) {
   define ('_DIR_ROOT', dirname(__FILE__).'/../');
}


// define the http root for the application (how the app is being accessed thru a browser)
$http_root = str_replace(
        str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '',
	str_replace('\\', '/', _DIR_ROOT));
define('_HTTP_ROOT', $http_root);

// define the database (sqlite) connection
$GLOBALS['db'] = $db = new PDO('sqlite:'.dirname(__FILE__).'/../db/blog.sqlite.dat');
$GLOBALS['db']->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
$GLOBALS['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 

// check the POST request
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0) {
    require_once dirname(__FILE__).'/../application/controller/process_post.php';
}

// check logout
if (array_key_exists('logout', $_GET)) {
	unset($_SESSION['ftp']);
	session_destroy();
	session_regenerate_id();
	header('Location: '._HTTP_ROOT.'/');
}

