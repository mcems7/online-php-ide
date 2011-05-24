<?php

/**
 * Template created by Gregory Chris
 */
if (!empty($GLOBALS['url_params'][0])) {
    if (file_exists(dirname(__FILE__) . '/transaction/' . $GLOBALS['url_params'][0] . '.php')) {
        require_once dirname(__FILE__) . '/transaction/' . array_shift($GLOBALS['url_params']) . '.php';
    } else {
        header('Location: ' . _HTTP_ROOT . '/admin/');
        die();
    }
} else {
    header('Location: ' . _HTTP_ROOT . '/admin/');
    die();
}