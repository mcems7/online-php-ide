<?php

/**
 * Template created by Gregory Chris
 */
load_model('admin/tutorials');

$params = array();

if (is_numeric($GLOBALS['url_params'][0]) && $GLOBALS['url_params'][0] > 0) {
    $params['item'] = get_items($GLOBALS['url_params'][0]);
} else {
    $params['items'] = get_items();
}


load_view('admin/tutorials', $params);
