<?php

/**
 * Template created by Gregory Chris
 */

load_model('admin/blog');

if (array_key_exists('submit', $_POST) && $_POST['submit'] == 'Save') {
    if (array_key_exists('post_id', $_POST) && is_numeric($_POST['post_id']) && $_POST['post_id'] > 0) {
        update_post($_POST['post_id'], $_POST);
    }
}

if (array_key_exists('submit', $_POST) && $_POST['submit'] == 'Delete') {
    if (array_key_exists('post_id', $_POST) && is_numeric($_POST['post_id']) && $_POST['post_id'] > 0) {
        delete_post($_POST['post_id']);
    }
}

if (array_key_exists('submit', $_POST) && $_POST['submit'] == 'Add') {
    add_post($_POST);
}


header('Location: '._HTTP_ROOT.'/admin/blog/');
die();