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
        die('Is not logged in, session error');
    }
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

_log('---------------- Upload file process -------------------');

function _die($str = '') {
    _log('');
    _log($str);
    die($str);
}

function upload_directory_to_ftp($ftp, $local_folder, $remote_folder) {

    _log('Entered upload function. '."\r\n".'src: "'. $local_folder.'"'."\r\n".'dest: "'.$remote_folder.'"');
    // change the (rempte) directory
    if (!$ftp->cd($remote_folder)) {
        _log('Cannot cd to "'.$remote_folder.'"');
        if (!$ftp->mkdir($remote_folder)) {
            _log('Cannot mkdir (remote) '.$remote_folder);
            return false;
        } else {
            $ftp->cd($remote_folder);
            _log('created '.$remote_folder);
            _log('cd '.$remote_folder);
        }
    } else {
        _log('cd '.$remote_folder);
    }

    // scan files from local directory
    foreach (scandir($local_folder) as $file) {
        if (in_array($file, array('.', '..'))) {
            continue;
        }

        _log('Checking file "'.$file.'"');
        if (is_dir($local_folder.DIRECTORY_SEPARATOR.$file)) {
            _log('"'.$file .'" is a directory');
            upload_directory_to_ftp($ftp, $local_folder.DIRECTORY_SEPARATOR.$file, $remote_folder.'/'.$file);
            
            // change the directory back
            if (!$ftp->cd($remote_folder)) {
                _log('Cannot cd back to "'.$remote_folder.'"');
            } else {
                _log('cd back to "'.$remote_folder.'"');
            }
        } else {
            _log('Uploading "'.$file.'"');
            if (!$ftp->put($file, $local_folder.DIRECTORY_SEPARATOR.$file)) {
                _log('Cannot upload "'.$local_folder.DIRECTORY_SEPARATOR.$file.'"');
            } else {
                _log('Successfully uploaded "'.$local_folder.DIRECTORY_SEPARATOR.$file.'"');
            }
        }
    }
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

if (array_key_exists('upload_file_dirname', $_POST)
        && !empty($_POST['upload_file_dirname']) && $ftp->cd($_POST['upload_file_dirname'])
        && array_key_exists('upload_file_name', $_FILES) && $_FILES['upload_file_name']['error'] == 0
        && $_FILES['upload_file_name']['size'] > 0) {

    if (preg_match('%\.zip$%i', $_FILES['upload_file_name']['name'])) {
        
        // ZIP archive
        $z = new ZipArchive();
        if (!empty($z)) {
            if ($z->open($_FILES['upload_file_name']['tmp_name']) === true) {

                // create temporary directory
                $temp_dir = ini_get("session.save_path");
                $new_dir_name = 'tmp_'.microtime(true);
                $dirname = $temp_dir . DIRECTORY_SEPARATOR . $new_dir_name;
                if (!@mkdir($dirname)) {
                    _die('cannot create temporary directory');
                }

                $z->extractTo($dirname);

                upload_directory_to_ftp($ftp, $dirname, $_POST['upload_file_dirname']);
                
            } else {
                _die('Cannot open ZIP file');
            }
        } else {
            _die('Cannot create ZIP object');
        }
    } else {

        // Not a ZIP archive - standard upload
        if (is_uploaded_file($_FILES['upload_file_name']['tmp_name']) && file_exists($_FILES['upload_file_name']['tmp_name'])) {
            $filename = $_FILES['upload_file_name']['tmp_name'];
            if (!$ftp->cd($_POST['upload_file_dirname'])) {
                _die('Cannot cd to "'.$_POST['upload_file_dirname'].'"');
            }

            if (!$ftp->put($_FILES['upload_file_name']['name'], $_FILES['upload_file_name']['tmp_name'])) {
                _die('Cannot upload file "'.$_FILES['upload_file_name']['name'].'"');
            } else {
                _log('file "'.$_FILES['upload_file_name']['name'].'" uploaded successfully to "'.$_POST['upload_file_dirname'].'"');
                die();
            }

        }
    }
} else {
    _die('error in parameters');
}

