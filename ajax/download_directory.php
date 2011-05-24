<?php

/**
 * Template created by Gregory Chris
 */
require_once dirname(__FILE__) . '/../config/config.php';

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

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

_log('---------------- Download directory process -------------------');

function _die($str = '') {
    _log('');
    _log($str);
    die($str);
}

function get_fileinfo_from_raw_data($raw_data, &$full_file_name, &$type) {
    $type = $raw_data[0] == 'd' ? 'directory' : ($raw_data[0] == 'l' ? 'link' : 'file');
    $spl = preg_split("%\s+%", $raw_data);
    $full_file_name = '';
    $i = 8;
    while ($i < sizeof($spl)) {
        $full_file_name .= ( empty($full_file_name) ? '' : ' ');
        $full_file_name .= $spl[$i++];
    }
}


// define limits
$files_count = 0;
$max_files_for_download = 1000; // 1000 files maximum
$max_files_size_for_download = 3000000; // 3Mb
$total_downloaded = 0;
function download_remote_dir($ftp, $remote_dir, $local_dir) {
    global $files_count, $max_files_for_download, $max_files_size_for_download, $total_downloaded;
    if (!$ftp->cd($remote_dir)) {
        _log('Cannot cd to "'.$remote_dir.'"');
        return;
    }

    $files_list = $ftp->rawlist($remote_dir);
    foreach ($files_list as $file) {
        
        if ($files_count++ > $max_files_for_download) {
            _log('Maximum of '.$max_files_for_download.' files exceeded!');
            return;
        }
        
        get_fileinfo_from_raw_data($file, $filename, $type);
        _log('checking '.$type.' "'.$remote_dir.'/'.$filename.'"');
        
        if (in_array($filename, array('.', '..'))) {
            continue;
        }

        if ($type == 'directory') {
            // create local directory
            if (!@mkdir($local_dir.DIRECTORY_SEPARATOR.$filename)) {
                _die('Cannot create local dir "'.$local_dir.DIRECTORY_SEPARATOR.$filename.'"');
            }

            _log('Entering to directory "'.$remote_dir.'/'.$filename.'"');
            download_remote_dir($ftp, $remote_dir.'/'.$filename, $local_dir.DIRECTORY_SEPARATOR.$filename);

            if (!$ftp->cd($remote_dir)) {
                _log('Cannot cd back to "'.$remote_dir.'"');
            } else {
                _log('CD back to "'.$remote_dir.'"');
            }
            
        } elseif ($type == 'file') {
            
            if (!$ftp->get($local_dir.DIRECTORY_SEPARATOR.$filename, $remote_dir.'/'.$filename)) {
                _log('Cannot download "'.$remote_dir.'/'.$filename.'"');
            } else {
                $total_downloaded += filesize($local_dir.DIRECTORY_SEPARATOR.$filename);
                _log('Downloaded '.(sprintf('%.1f', ($total_downloaded/1024))).'Kb Downloaded');
                if ($total_downloaded >= $max_files_size_for_download) {
                    _log('Maximum '.(sprintf('%.1f', ($max_files_size_for_download/1024))).' Kb exceeded');
                    return;
                }
                _log('Successfully downloaded "'.$remote_dir.'/'.$filename.'"');
            }
            
        }
    }
}



function add_directory_to_zip(&$z, $dir, $base_dir = NULL) {
    if (empty($z)) {
        _die('Error in ZIP Parameter');
    }

    if (is_null($base_dir)) {
        $base_dir = trim($dir, '/');
        $base_dir = trim($base_dir, '\\');
    }

    foreach (scandir($dir) as $file) {
        if (in_array($file, array('.', '..'))) {
            continue;
        }
        
        if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
            add_directory_to_zip($z, $dir.DIRECTORY_SEPARATOR.$file, $base_dir);
        } elseif (is_readable($dir.DIRECTORY_SEPARATOR.$file)) {

            // directory for the ZIP file
            $zDir = str_replace($base_dir, '', $dir);
            $zDir = trim($zDir, '/');
            $zDir = trim($zDir, '\\');
            $zDir.= empty($zDir) ? '' : '/';

            $z->addFile($dir.DIRECTORY_SEPARATOR.$file, $zDir . $file);
            _log('Added "'.$dir.DIRECTORY_SEPARATOR.$file.'" to ZIP');
        }
    }
}



// create temp local dir
$temp_dir = ini_get("session.save_path");
$temp_dirname = 'tmp_'.microtime(true);
if (!@mkdir($temp_dir.DIRECTORY_SEPARATOR.$temp_dirname)) {
    _die('Cannot create local temp directory "'.$temp_dir.DIRECTORY_SEPARATOR.$temp_dirname.'"');
}

$remote_dir = $_GET['download_directory'];
$remote_dir = str_replace('___', '/', $remote_dir);

$total_downloaded = 0;
download_remote_dir($ftp, $remote_dir, $temp_dir.DIRECTORY_SEPARATOR.$temp_dirname);

_log('Downloaded directory successfully');

$temp_zip_filename = 'tmp_'.microtime(true).'.zip';
$z = new ZipArchive();
if (!$z->open($temp_dir.DIRECTORY_SEPARATOR.$temp_zip_filename, ZIPARCHIVE::CREATE)) {
    _die('Cannot create ZIP file "'.$temp_dir.DIRECTORY_SEPARATOR.$temp_zip_filename.'"');
} else {
    _log('Created ZIP "'.$temp_dir.DIRECTORY_SEPARATOR.$temp_zip_filename.'"');
}

add_directory_to_zip($z, $temp_dir.DIRECTORY_SEPARATOR.$temp_dirname);

$z->close();
_log('Finished creating ZIP "'.$temp_dir.DIRECTORY_SEPARATOR.$temp_zip_filename.'"');

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename='.array_pop(explode('/', $remote_dir)).'.zip');
if (file_exists($temp_dir.DIRECTORY_SEPARATOR.$temp_zip_filename)) {
    readfile($temp_dir.DIRECTORY_SEPARATOR.$temp_zip_filename);
} else {
    _log('The file "'.$temp_dir.DIRECTORY_SEPARATOR.$temp_zip_filename.'" does not exist');
}
