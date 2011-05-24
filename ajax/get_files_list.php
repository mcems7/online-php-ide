<?
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

if ($_GET['id'] == '0') {
	$base_dir = $ftp->pwd();
} else {
	$base_dir = '/'.trim(str_replace('___', '/', $_GET['id']), '/');;
}

// get and parse the files from the ftp
$rawfiles = $ftp->rawlist($base_dir);
$files = array(
    'directory'=>array(),
    'link'=>array(),
    'file'=>array(),
);

foreach ($rawfiles as $file) {
    $type = $file[0] == 'd' ? 'directory' : 
                ($file[0] == 'l' ? 'link' : 'file');
    
    $spl = preg_split("%\s+%", $file);
    
    $full_file_name = '';
    $i = 8;
    while ($i < sizeof($spl)) {
        $full_file_name .= (empty($full_file_name) ? '' : ' ');
        $full_file_name .= $spl[$i++];
    }
    $files[$type][] = $full_file_name;
}


// sort the arrays
sort($files['directory']);
sort($files['link']);
sort($files['file']);

$arr = array();

// add DIRECTORIES to the array
foreach ($files as $ftype=>$file) {
	if ($ftype == 'directory') {
        foreach ($file as $item) {
			if (in_array($item, array('.', '..'))) continue;
            $arr[] = array(
                'data'=>array(
                    'title'=>$item,
                    'attr'=>array(
                        'onclick'=>'javascript:;',
                    ),
                    'icon'=>'folder',
                ),
                'attr'=>array(
                    'id'=>str_replace(DIRECTORY_SEPARATOR, '___', $base_dir.DIRECTORY_SEPARATOR.$item),
                    'rel'=>'folder',
                ),
                'state'=>'closed',
            );
        }
	}
}


// add FILES to the array
foreach ($files as $ftype=>$file) {
	if ($ftype == 'file') {
        foreach ($file as $item) {
			if (in_array($item, array('.', '..'))) continue;
            $arr[] = array(
                'data'=>array(
                    'title'=>$item,
                    'attr'=>array(
                            'is_file'=>'true',
                            'href'=>'javascript:open_file("'.str_replace(DIRECTORY_SEPARATOR, '___', $base_dir.DIRECTORY_SEPARATOR.$item).'")',
                    ),
                    'icon'=>_HTTP_ROOT.'/images/file.png',
                ),
                'attr'=>array(
                    'id'=>str_replace(DIRECTORY_SEPARATOR, '___', $base_dir.DIRECTORY_SEPARATOR.$item),
                    'rel'=>'default',
                ),
                'state'=>'',
            );
        }
	}
}
die(json_encode($arr));