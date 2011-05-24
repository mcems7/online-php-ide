<?

session_start();

ini_set('display_errors', '1');
error_reporting(E_WARNING | E_ERROR);

date_default_timezone_set('Asia/Jerusalem');

require dirname(__FILE__) .'/../application/model/ftp.class.php';
require dirname(__FILE__) .'/funcs.php';

define ('_HTTP_ROOT', '/ide');

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

