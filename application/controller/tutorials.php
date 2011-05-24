<?php
/**
 * Template created by Gregory Chris
 */
load_model('tutorials');

$params = array();

if (!empty($GLOBALS['url_params'][0])) {
    $params['item']     = get_tutorial_item_by_title($GLOBALS['url_params'][0]);
    $params['item']     = preg_replace('%<h3(.*?)>%smi', '<h4\1>', $params['item']);
    $params['item']     = preg_replace('%</h3>%smi', '</h4>', $params['item']);
    $params['item']     = preg_replace('%<h2(.*?)>%smi', '<h3\1>', $params['item']);
    $params['item']     = preg_replace('%</h2>%smi', '</h3>', $params['item']);
    $params['items']    = NULL;
} else {
    $params['items']    = get_tutorial_items();
}

load_view('header');
load_view('tutorials', $params);
load_view('footer');