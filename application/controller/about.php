<?php

/**
 * Template created by Gregory Chris
 */

load_model('about');
$params = array();
$params['from_submit'] = false;
if ($GLOBALS['url_params'][0] == 'submit') {
    save_contact();
    header('Location: '._HTTP_ROOT.'/about/thank_you/');
    die();
} elseif ($GLOBALS['url_params'][0] == 'thank_you') {
    $params['from_submit'] = true;
}

load_view('header');
load_view('about', $params);
load_view('footer');