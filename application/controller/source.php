<?php

/**
 * Template created by Gregory Chris
 */

load_model('source');

if (!empty($GLOBALS['url_params'][0]) && $GLOBALS['url_params'][0] == 'download') {
    // download the source
    load_model('source');
    
    // create the latest zip archive
    create_source_zip();
    
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=latest.zip');
    
    readfile(_SOURCE_ZIP_FILE);
    die();
}


load_view('header');
load_view('source');
load_view('footer');