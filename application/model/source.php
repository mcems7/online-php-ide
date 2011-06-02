<?php

/**
 * Template created by Gregory Chris
 */


// directories and files to exclude from the ZIP file.
// RegExp based
$GLOBALS['exclude_dirs'] = $exclude_dirs   = array(
    // exclude the source directory (where the ZIP will be stored)
    '%'.preg_quote(DIRECTORY_SEPARATOR, '%').'source('.preg_quote(DIRECTORY_SEPARATOR, '%').'|$|\\s)%i',
    // exclude the log directory
    '%'.preg_quote(DIRECTORY_SEPARATOR, '%').'log('.preg_quote(DIRECTORY_SEPARATOR, '%').'|$|\\s)%i',
    // exclude the log directory
    '%nbproject%i',
);
$GLOBALS['exclude_files'] = $exclude_files  = array(
);


function add_directory_to_zip(&$z, $dir, $base_dir = NULL) {
    global $exclude_files, $exclude_dirs;
    
    if (empty($z)) {
        _die('Error in ZIP Parameter');
    }

    if (is_null($base_dir)) {
        $base_dir = trim($dir, '/');
        $base_dir = trim($base_dir, '\\');
    }

    if (!file_exists($dir)) {
        _log('dir: '.$dir.' does not exist');
        return;
    }
    
    foreach (scandir($dir) as $file) {
        if (in_array($file, array('.', '..'))) {
            continue;
        }

        
        // check the exclude dirs
        $continue = false;
        if (!empty ($exclude_dirs)) foreach ($exclude_dirs as $e_dir) {
            if (preg_match($e_dir, $dir.DIRECTORY_SEPARATOR.$file)) {
                $continue = true;
            }
        }
        if ($continue) continue;
        
        // check the exclude files
        $continue = false;
        if (!empty ($exclude_files)) foreach ($exclude_files as $e_file) {
            if (preg_match($e_file, $dir.DIRECTORY_SEPARATOR.$file)) {
                $continue = true;
            }
        }
        if ($continue) continue;
        
        
        if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
            
            // add 
            add_directory_to_zip($z, $dir.DIRECTORY_SEPARATOR.$file, $base_dir);
        } elseif (is_readable($dir.DIRECTORY_SEPARATOR.$file)) {
            
            // directory for the ZIP file
            $zDir = str_replace($base_dir, '', $dir);
            $zDir = trim($zDir, '/');
            $zDir = trim($zDir, '\\');
            $zDir.= empty($zDir) ? '' : '/';

            $z->addFile($dir.DIRECTORY_SEPARATOR.$file, $zDir . $file);
        }
    }
}



function create_source_zip() {
    
    if (file_exists(_DIR_ROOT . '/source/latest.zip')) {

        if (($fp = @fopen(_DIR_ROOT . '/source/latest.zip', 'r')) !== false) {
            $stats = fstat($fp);            
            fclose($fp);
            
            // check if the source was created in the last hour
            if ((time() - $stats['mtime']) <= 3600) {
                return;
            }
        } 
        
        rename(_DIR_ROOT . '/source/latest.zip', _DIR_ROOT . '/source/latest'.time().'.zip');
    }
    // Create the latest source of the application 
    // in a ZIP archive
    $z = new ZipArchive();
    $z->open(_DIR_ROOT.'/source/latest.zip', ZipArchive::CREATE);
    add_directory_to_zip($z, _DIR_ROOT);
    
    // define a constant with the path to the file
    define ('_SOURCE_ZIP_FILE', _DIR_ROOT.'/source/latest.zip');
}



function store_subscription($data) {
    
    if (($f = @fopen(_DIR_ROOT.'/log/subscribe.txt', 'a+')) !== false) {
        @fputs($f, date('d.M.Y H:i') . ': SUBSCRIBE <email:' . htmlentities($data['email'], ENT_QUOTES, 'utf-8') . "> 
<".($data['subscribe']=='1'?'subscribe':'DO NOT subscribe').">\r\n");


        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: Online-PHP.com <admin@online-php.com>' . "\r\n";

        @mail('www.online.php@gmail.com', 'Subscription from online-php.com', 
                '<b>'.htmlentities($data['email'], ENT_QUOTES, 'utf-8')."</b>".
                '<br><br><a href="http://online-php.com/">Online-PHP</a>', $headers);

        @fclose($f);
    }
    
}