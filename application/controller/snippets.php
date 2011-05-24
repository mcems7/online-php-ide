<?php

/**
 * Template created by Gregory Chris
 */
load_model('snippets');

$params = array();

$params['items'] = get_snippet_items();
$params['search_string']    = '';
$params['tag']              = '';
$params['language']         = '';

if ($GLOBALS['url_params'][0] == 'tag' && !empty($GLOBALS['url_params'][1])) {
	// load snippets with specific tag
	$params['items'] = get_snippet_items_by_tag($GLOBALS['url_params'][1]);
	$params['tag'] = htmlentities(urldecode($GLOBALS['url_params'][1]), ENT_NOQUOTES, 'utf-8');
}

if ($GLOBALS['url_params'][0] == 'load' && !empty($GLOBALS['url_params'][1])
		&& is_numeric($GLOBALS['url_params'][1]) && $GLOBALS['url_params'][1] > 0) {
	// load snippets with specific tag
	$item = get_snippet_item($GLOBALS['url_params'][1]);
	die(highlight_string($item['code'], true));
}

if ($GLOBALS['url_params'][0] == 'search' && !empty($GLOBALS['url_params'][1])) {
	// load snippets with specific tag
	$params['items'] = get_snippet_item_by_name($GLOBALS['url_params'][1]);
	$params['search_string'] = str_replace('\\','&#92;', htmlentities(urldecode($GLOBALS['url_params'][1]), ENT_QUOTES, 'utf-8'));
}

if ($GLOBALS['url_params'][0] == 'language' && !empty($GLOBALS['url_params'][1])) {
	// load snippets with specific tag
	$params['items']    = get_snippet_item_by_language($GLOBALS['url_params'][1]);
    $params['language'] = urldecode($GLOBALS['url_params'][1]);
}

load_view('header');
load_view('snippets', $params);
load_view('footer');